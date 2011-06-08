<?php
/**
 * This file handles the display of the 'Roles' page in the admin.
 *
 * @package Members
 * @subpackage Admin
 */

/* Get the global $wp_roles variable. */
global $wp_roles;

/* Get a count of all the roles available. */
$roles_count = members_count_roles();

/* Get all of the active and inactive roles. */
$active_roles = members_get_active_roles();
$inactive_roles = members_get_inactive_roles();

/* Get a count of the active and inactive roles. */
$active_roles_count = count( $active_roles );
$inactive_roles_count = count( $inactive_roles );

/* If we're viewing 'active' or 'inactive' roles. */
if ( !empty( $_GET['role_status'] ) && in_array( $_GET['role_status'], array( 'active', 'inactive' ) ) ) {

	/* Get the role status ('active' or 'inactive'). */
	$role_status = esc_attr( $_GET['role_status'] );

	/* Set up the roles array. */
	$list_roles = ( ( 'active' == $role_status ) ? $active_roles : $inactive_roles );

	/* Set the current page URL. */
	$current_page = admin_url( "users.php?page=roles&role_status={$role_status}" );
}

/* If viewing the regular role list table. */
else {

	/* Get the role status ('active' or 'inactive'). */
	$role_status = 'all';

	/* Set up the roles array. */
	$list_roles = $wp_roles->role_names;

	/* Set the current page URL. */
	$current_page = $current_page = admin_url( 'users.php?page=roles' );
}

/* Sort the roles array into alphabetical order. */
ksort( $list_roles ); ?>

<div class="wrap">

	<?php screen_icon(); ?>

	<h2>
		<?php _e( 'Roles', 'members' ); ?> 
		<?php if ( current_user_can( 'create_roles' ) ) echo '<a href="' . admin_url( 'users.php?page=role-new' ) . '" class="add-new-h2">' . __( 'Add New', 'members' ) . '</a>'; ?>
	</h2>

	<?php do_action( 'members_pre_edit_roles_form' ); // Available action hook for displaying messages. ?>

	<div id="poststuff">

		<form id="roles" action="<?php echo $current_page; ?>" method="post">

			<?php wp_nonce_field( members_get_nonce( 'edit-roles' ) ); ?>

			<ul class="subsubsub">
				<li><a <?php if ( 'all' == $role_status ) echo 'class="current"'; ?> href="<?php echo admin_url( esc_url( 'users.php?page=roles' ) ); ?>"><?php _e( 'All', 'members' ); ?> <span class="count">(<span id="all_count"><?php echo $roles_count; ?></span>)</span></a> | </li>
				<li><a <?php if ( 'active' == $role_status ) echo 'class="current"'; ?> href="<?php echo admin_url( esc_url( 'users.php?page=roles&amp;role_status=active' ) ); ?>"><?php _e( 'Has Users', 'members' ); ?> <span class="count">(<span id="active_count"><?php echo $active_roles_count; ?></span>)</span></a> | </li>
				<li><a <?php if ( 'inactive' == $role_status ) echo 'class="current"'; ?> href="<?php echo admin_url( esc_url( 'users.php?page=roles&amp;role_status=inactive' ) ); ?>"><?php _e( 'No Users', 'members' ); ?> <span class="count">(<span id="inactive_count"><?php echo $inactive_roles_count; ?></span>)</span></a></li>
			</ul><!-- .subsubsub -->

			<div class="tablenav">

				<?php if ( current_user_can( 'delete_roles' ) ) { ?>

					<div class="alignleft actions">

						<select name="bulk-action">

							<option value="" selected="selected"><?php _e( 'Bulk Actions', 'members' ); ?></option>

							<?php if ( current_user_can( 'delete_roles' ) ) { ?>
								<option value="delete"><?php esc_html_e( 'Delete', 'members' ); ?></option>
							<?php } ?>

						</select>

						<?php submit_button( esc_attr__( 'Apply', 'members' ), 'button-secondary action', 'roles-bulk-action', false ); ?>

					</div><!-- .alignleft .actions -->

				<?php } // End cap check ?>

				<div class='tablenav-pages one-page'>
					<span class="displaying-num"><?php printf( _n( '%s item', '%s items', count( $list_roles ), 'members' ), count( $list_roles ) ); ?></span>
				</div>

				<br class="clear" />

			</div><!-- .tablenav -->

			<table class="widefat fixed" cellspacing="0">

				<thead>
					<tr>
						<th class='check-column'><input type='checkbox' /></th>
						<th class='name-column'><?php _e( 'Role Label', 'members' ); ?></th>
						<th><?php _e( 'Role Name', 'members' ); ?></th>
						<th><?php _e( 'Users', 'members' ); ?></th>
						<th><?php _e( 'Capabilities', 'members' ); ?></th>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<th class='check-column'><input type='checkbox' /></th>
						<th class='name-column'><?php _e( 'Role Label', 'members' ); ?></th>
						<th><?php _e( 'Role', 'members' ); ?></th>
						<th><?php _e( 'Users', 'members' ); ?></th>
						<th><?php _e( 'Capabilities', 'members' ); ?></th>
					</tr>
				</tfoot>

				<tbody id="users" class="list:user user-list plugins">

				<?php foreach ( $list_roles as $role => $name ) { ?>

					<tr valign="top" class="<?php echo ( isset( $active_roles[$role] ) ? 'active' : 'inactive' ); ?>">

						<th class="manage-column column-cb check-column">

							<?php if ( ( is_multisite() && is_super_admin() && $role !== get_option( 'default_role' ) ) || ( !current_user_can( $role ) && $role !== get_option( 'default_role' ) ) ) { ?>
								<input type="checkbox" name="roles[<?php echo esc_attr( $role ); ?>]" id="<?php echo esc_attr( $role ); ?>" value="<?php echo esc_attr( $role ); ?>" />
							<?php } ?>

						</th><!-- .manage-column .column-cb .check-column -->

						<td class="plugin-title">

							<?php $edit_link = admin_url( wp_nonce_url( "users.php?page=roles&amp;action=edit&amp;role={$role}", members_get_nonce( 'edit-roles' ) ) ); ?> 

							<?php if ( current_user_can( 'edit_roles' ) ) { ?>
								<a href="<?php echo esc_url( $edit_link ); ?>" title="<?php printf( esc_attr__( 'Edit the %s role', 'members' ), $name ); ?>"><strong><?php echo esc_html( $name ); ?></strong></a>
							<?php } else { ?>
								<strong><?php echo esc_html( $name ); ?></strong>
							<?php } ?>

							<div class="row-actions">

								<?php if ( current_user_can( 'edit_roles' ) ) { ?>
									<a href="<?php echo esc_url( $edit_link ); ?>" title="<?php printf( esc_attr__( 'Edit the %s role', 'members' ), $name ); ?>"><?php _e( 'Edit', 'members' ); ?></a> 
								<?php } ?>

								<?php if ( ( is_multisite() && is_super_admin() && $role !== get_option( 'default_role' ) ) || ( current_user_can( 'delete_roles' ) && $role !== get_option( 'default_role' ) && !current_user_can( $role ) ) ) { ?>
									| <a href="<?php echo admin_url( wp_nonce_url( "users.php?page=roles&amp;action=delete&amp;role={$role}", members_get_nonce( 'edit-roles' ) ) ); ?>" title="<?php printf( esc_attr__( 'Delete the %s role', 'members' ), $name ); ?>"><?php _e( 'Delete', 'members' ); ?></a>
								<?php } ?>

								<?php if ( current_user_can( 'manage_options' ) && $role == get_option( 'default_role' ) ) { ?>
									| <a href="<?php echo admin_url( ( 'options-general.php' ) ); ?>" title="<?php _e( 'Change default role', 'members' ); ?>"><?php _e( 'Default Role', 'members' ); ?></a> 
								<?php } ?>

								<?php if ( current_user_can( 'list_users' ) ) { ?>
									| <a href="<?php echo admin_url( esc_url( "users.php?role={$role}" ) ); ?>" title="<?php printf( esc_attr__( 'View all users with the %s role', 'members' ), $name ); ?>"><?php _e( 'View Users', 'members' ); ?></a> 
								<?php } ?>

							</div><!-- .row-actions -->

						</td><!-- .plugin-title -->

						<td class="desc">
							<p>
								<?php echo $role; ?>
							</p>
						</td><!-- .desc -->

						<td class="desc">
							<p>
								<?php if ( current_user_can( 'list_users' ) ) { ?>
									<a href="<?php echo admin_url( esc_url( "users.php?role={$role}" ) ); ?>" title="<?php printf( __( 'View all users with the %s role', 'members' ), $name ); ?>"><?php printf( _n( '%s User', '%s Users', members_get_role_user_count( $role ), 'members' ), members_get_role_user_count( $role ) ); ?></a>
								<?php } else { ?>
									<?php printf( _n( '%s User', '%s Users', members_get_role_user_count( $role ), 'members' ), members_get_role_user_count( $role ) ); ?>
								<?php } ?>
							</p>
						</td><!-- .desc -->

						<td class="desc">
							<p>
							<?php
								$role_object = get_role( $role );
								$cap_count = count( $role_object->capabilities );
								printf( _n( '%s Capability', '%s Capabilities', $cap_count, 'members' ), $cap_count );
							?>
							</p>
						</td><!-- .desc -->

					</tr><!-- .active .inactive -->

				<?php } // End foreach ?>

				</tbody><!-- #users .list:user .user-list .plugins -->

			</table><!-- .widefat .fixed -->

			<div class="tablenav">

				<?php if ( current_user_can( 'delete_roles' ) ) { ?>

					<div class="alignleft actions">

						<select name="bulk-action-2">

							<option value="" selected="selected"><?php _e( 'Bulk Actions', 'members' ); ?></option>

							<?php if ( current_user_can( 'delete_roles' ) ) { ?>
								<option value="delete"><?php _e( 'Delete', 'members' ); ?></option>
							<?php } ?>

						</select>

						<?php submit_button( esc_attr__( 'Apply', 'members' ), 'button-secondary action', 'roles-bulk-action-2', false ); ?>

					</div><!-- .alignleft .actions -->

				<?php } // End cap check ?>

				<div class='tablenav-pages one-page'>
					<span class="displaying-num"><?php printf( _n( '%s item', '%s items', count( $list_roles ), 'members' ), count( $list_roles ) ); ?></span>
				</div>

				<br class="clear" />

			</div><!-- .tablenav -->

		</form><!-- #roles -->

	</div><!-- #poststuff -->

</div><!-- .wrap -->