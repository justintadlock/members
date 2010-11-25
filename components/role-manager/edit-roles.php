<?php
/**
 * The Edit Roles page displays all of the site's roles in an easy-to-read manner.  Along with
 * each role, the number of users and capabilities are displayed.  Roles without users are
 * considered "inactive" roles within this plugin's system. 
 *
 * All roles can be edited.  However, the current user's role and the default role cannot be 
 * deleted. To delete the current user's role, another logged-in user with the 'delete_roles' 
 * capability and a different role must perform this action.  To delete the default role, the 
 * default must be changed under the General Options page in the WordPress admin.
 *
 * Users of roles that are deleted will be given the default role (typically 'Subscriber').  It
 * is advisable to not make such a change with a large number of users because a new user 
 * object must be created to change each individual user.
 *
 * @todo Test deleting a role with 100s (even 1,000s) of users to see what sort of strain this has.
 *
 * @package Members
 * @subpackage Components
 */

/* Get the global $members variable. */
global $members;

/* Current user in the admin. */
$user = new WP_User( $members->current_user->ID );

/* Set the available roles array.*/
$avail_roles = array();

/* Get all the users of the current blog. */
$users_of_blog = get_users_of_blog();

/* Loop through each user. */
foreach ( (array) $users_of_blog as $blog_user ) {

	$meta_values = unserialize( $blog_user->meta_value );

	foreach ( (array) $meta_values as $role => $value ) {
		if ( !isset( $avail_roles[$role] ) )
			$avail_roles[$role] = 0;

		++$avail_roles[$role];
	}
}

/* Destroy the $users_of_blog variable. */
unset( $users_of_blog );

/* Can the current user delete_roles? */
if ( current_user_can( 'delete_roles' ) )
	$delete_roles = true;

/* Get the default role. */
$default_role = get_option( 'default_role' );

/* Sort out the roles, active roles, and inactive roles. */
$all_roles = $active_roles = $inactive_roles = 0;

$active_roles_arr = $inactive_roles_arr = array();

/* Loop through all of the roles, adding each role to its respective category (active, inactive). */
foreach ( $wp_roles->role_names as $role => $name ) {
	$all_roles++;
	if ( isset($avail_roles[$role]) ) {
		$active_roles++;
		$active_roles_arr[$role] = $name;
	}
	else {
		$inactive_roles++;
		$inactive_roles_arr[$role] = $name;
	}
}

$role_status = isset( $_GET['role_status'] ) ? $_GET['role_status'] : 'all';

/* Set variables for when role_status is active. */
if ( 'active' == $role_status ) {	
	$roles_loop_array = $active_roles_arr;
	$title = __('Edit Active Roles', 'members');
	$current_page = admin_url( esc_url( 'users.php?page=roles&role_status=active' ) );
}

/* Set variables for when role_status is inactive. */
elseif ( 'inactive' == $role_status ) {
	$roles_loop_array = $inactive_roles_arr;
	$title = __('Edit Inactive Roles', 'members');
	$current_page = admin_url( esc_url( 'users.php?page=roles&role_status=inactive' ) );
}

/* Set default variables for when role_status is neither active nor inactive. */
else {
	$roles_loop_array = $wp_roles->role_names;
	$title = __('Edit Roles', 'members');
	$current_page = admin_url( esc_url( "users.php?page=roles" ) );
}

/* Sort the roles array into alphabetical order. */
ksort( $roles_loop_array ); ?>

<div class="wrap">

	<h2><?php echo $title; ?></h2>

	<?php do_action( 'members_pre_edit_roles_form' ); // Available action hook for displaying messages. ?>

	<div id="poststuff">

		<form id="roles" action="<?php echo $current_page; ?>" method="post">

			<?php wp_nonce_field( members_get_nonce( 'edit-roles' ) ); ?>

			<ul class="subsubsub">
				<li><a <?php if ( 'all' == $role_status ) echo 'class="current"'; ?> href="<?php echo admin_url( esc_url( 'users.php?page=roles' ) ); ?>"><?php _e('All', 'members'); ?> <span class="count">(<span id="all_count"><?php echo $all_roles; ?></span>)</span></a> | </li>
				<li><a <?php if ( 'active' == $role_status ) echo 'class="current"'; ?> href="<?php echo admin_url( esc_url( 'users.php?page=roles&amp;role_status=active' ) ); ?>"><?php _e('Active', 'members'); ?> <span class="count">(<span id="active_count"><?php echo $active_roles; ?></span>)</span></a> | </li>
				<li><a <?php if ( 'inactive' == $role_status ) echo 'class="current"'; ?> href="<?php echo admin_url( esc_url( 'users.php?page=roles&amp;role_status=inactive' ) ); ?>"><?php _e('Inactive', 'members'); ?> <span class="count">(<span id="inactive_count"><?php echo $inactive_roles; ?></span>)</span></a></li>
			</ul><!-- .subsubsub -->

			<div class="tablenav">

				<div class="alignleft actions">
					<select name="action">
						<option value="" selected="selected"><?php _e('Bulk Actions', 'members'); ?></option>
						<?php if ( $delete_roles ) echo '<option value="delete">' . __('Delete', 'members') . '</option>'; ?>
					</select>
					<input type="submit" value="<?php _e('Apply', 'members'); ?>" name="doaction" id="doaction" class="button-secondary action" />
				</div><!-- .alignleft .actions -->

				<br class="clear" />

			</div><!-- .tablenav -->

			<table class="widefat fixed" cellspacing="0">
				<thead>
					<tr>
						<th class='check-column'><input type='checkbox' /></th>
						<th class='name-column'><?php _e('Role Name', 'members'); ?></th>
						<th><?php _e('Role', 'members'); ?></th>
						<th><?php _e('Users', 'members'); ?></th>
						<th><?php _e('Capabilities', 'members'); ?></th>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<th class='check-column'><input type='checkbox' /></th>
						<th class='name-column'><?php _e('Role Name', 'members'); ?></th>
						<th><?php _e('Role', 'members'); ?></th>
						<th><?php _e('Users', 'members'); ?></th>
						<th><?php _e('Capabilities', 'members'); ?></th>
					</tr>
				</tfoot>

				<tbody id="users" class="list:user user-list plugins">

				<?php foreach ( $roles_loop_array as $role => $name ) { ?>

					<?php $name = str_replace( '|User role', '', $name ); ?>

					<tr valign="top" class="<?php if ( isset($avail_roles[$role]) ) echo 'active'; else echo 'inactive'; ?>">

						<th class="manage-column column-cb check-column">
							<?php if ( $role !== $default_role && !$user->has_cap( $role ) ) { ?>
								<input name="roles[<?php echo $role; ?>]" id="<?php echo $role; ?>" type="checkbox" value="<?php echo $role; ?>" />
							<?php } ?>
						</th><!-- .manage-column .column-cb .check-column -->

						<td class="plugin-title">
							<?php $edit_link = admin_url( wp_nonce_url( "users.php?page=roles&amp;action=edit&amp;role={$role}", members_get_nonce( 'edit-roles' ) ) ); ?> 

							<a href="<?php echo $edit_link; ?>" title="<?php printf( __('Edit the %1$s role', 'members'), $name ); ?>"><strong><?php echo $name; ?></strong></a>

							<div class="row-actions">
								<a href="<?php echo $edit_link; ?>" title="<?php printf( __('Edit the %1$s role', 'members'), $name ); ?>"><?php _e('Edit', 'members'); ?></a> 

								<?php /* Delete role link. */
								if ( $delete_roles && $role !== $default_role && !$user->has_cap( $role ) ) {
									$delete_link = admin_url( wp_nonce_url( "users.php?page=roles&amp;action=delete&amp;role={$role}", members_get_nonce( 'edit-roles' ) ) ); ?>
									| <a href="<?php echo $delete_link; ?>" title="<?php printf( __('Delete the %1$s role', 'members'), $name ); ?>"><?php _e('Delete', 'members'); ?></a>
								<?php }

								/* Link to change the default role Options General. */
								if ( $role == $default_role ) { ?>
									| <a href="<?php echo admin_url( ( 'options-general.php' ) ); ?>" title="<?php _e('Change default role', 'members'); ?>"><?php _e('Default Role', 'members'); ?></a> 
								<?php }

								/* If there are users, provide a link to the users page of that role. */
								if ( isset($avail_roles[$role]) ) { ?>
									| <a href="<?php echo admin_url( esc_url( "users.php?role={$role}" ) ); ?>" title="<?php printf( __('View all users with the %1$s role', 'members'), $name ); ?>"><?php _e('View Users', 'members'); ?></a> 
								<?php } ?>

							</div><!-- .row-actions -->

						</td><!-- .plugin-title -->

						<td class="desc">
							<p><?php echo $role; ?></p>
						</td><!-- .desc -->

						<td class="desc">
							<p><?php /* Check if any users are assigned to the role.  If so, display a link to the role's users page. */
							if ( isset($avail_roles[$role]) && 1 < $avail_roles[$role] )
								echo '<a href="' . admin_url( esc_url( "users.php?role={$role}" ) ) . '" title="' . sprintf( __('View all users with the %1$s role', 'members'), $name ) . '">' . sprintf( __('%1$s Users', 'members'), $avail_roles[$role] ) . '</a>'; 
							elseif ( isset($avail_roles[$role]) && 1 == $avail_roles[$role] )
								echo '<a href="' . admin_url( esc_url( "users.php?role={$role}" ) ) . '" title="' . sprintf( __('View all users with the %1$s role', 'members'), $name ) . '">' . __('1 User', 'members') . '</a>'; 
							else
								echo '<em>' . __('No users have this role.', 'members') . '</em>';
							?></p>
						</td><!-- .desc -->

						<td class="desc">
							<p>
							<?php /* Check if the role has any capabilities. */

							$role_2 = get_role( $role );

							if ( is_array( $role_2->capabilities ) ) {
								$cap_count = count( $role_2->capabilities ); 
								if ( 1 < $cap_count ) printf( __('%1$s Capabilities', 'members'), $cap_count );
								elseif ( 1 == $cap_count ) _e('1 Capability', 'members');
							}
							else
								echo '<em>' . __('This role has no capabilities', 'members') . '</em>'; ?>
							</p>
						</td><!-- .desc -->

					</tr><!-- .active .inactive -->

				<?php } // End foreach ?>

				</tbody><!-- #users .list:user .user-list .plugins -->

			</table><!-- .widefat .fixed -->

			<div class="tablenav">

				<div class="alignleft actions">
					<select name="action2">
						<option value="" selected="selected"><?php _e('Bulk Actions', 'members'); ?></option>
						<?php if ( $delete_roles ) echo '<option value="delete">' . __('Delete', 'members') . '</option>'; ?>
					</select>
					<input type="submit" value="<?php _e('Apply', 'members'); ?>" name="doaction2" id="doaction2" class="button-secondary action" />
				</div><!-- .alignleft .actions -->

				<br class="clear" />

			</div><!-- .tablenav -->

		</form><!-- #roles -->

	</div><!-- #poststuff -->

</div><!-- .wrap -->