<?php
/**
 * @todo Add inline styles the the admin.css stylesheet.
 *
 * @package Members
 * @subpackage Admin
 */

/* Get the global $wp_roles variable. */
global $wp_roles;

/* Sort out the roles, active roles, and inactive roles. */
$all_roles = members_count_roles();
$active_roles_arr = members_get_active_roles();
$inactive_roles_arr = members_get_inactive_roles();
$active_roles = count( $active_roles_arr );
$inactive_roles = count( $inactive_roles_arr );

if ( !isset( $_GET['role_status'] ) ) {
	$role_status = 'all';
	$roles_loop_array = $wp_roles->role_names;
	$current_page = admin_url( esc_url( "users.php?page=roles" ) );
} elseif ( 'active' == $_GET['role_status'] ) {
	$role_status = 'active';
	$roles_loop_array = $active_roles_arr;
	$current_page = admin_url( 'users.php?page=roles&role_status=active' );
} elseif ( 'inactive' == $_GET['role_status'] ) {
	$role_status = 'inactive';
	$roles_loop_array = $inactive_roles_arr;
	$current_page = admin_url( 'users.php?page=roles&role_status=inactive' );
}

/* Sort the roles array into alphabetical order. */
ksort( $roles_loop_array ); ?>

<div class="wrap">

	<?php screen_icon(); ?>

	<h2>
		<?php _e( 'Roles', 'members' ); ?> 
		<?php if ( current_user_can( 'create_roles' ) ) echo '<a href="' . admin_url( 'users.php?page=new-role' ) . '" class="add-new-h2">' . __( 'Add New', 'members' ) . '</a>'; ?>
	</h2>

	<?php do_action( 'members_pre_edit_roles_form' ); // Available action hook for displaying messages. ?>

	<div id="poststuff">

		<form id="roles" action="<?php echo $current_page; ?>" method="post">

			<?php wp_nonce_field( members_get_nonce( 'edit-roles' ) ); ?>

			<ul class="subsubsub">
				<li><a <?php if ( 'all' == $role_status ) echo 'class="current"'; ?> href="<?php echo admin_url( esc_url( 'users.php?page=roles' ) ); ?>"><?php _e( 'All', 'members' ); ?> <span class="count">(<span id="all_count"><?php echo $all_roles; ?></span>)</span></a> | </li>
				<li><a <?php if ( 'active' == $role_status ) echo 'class="current"'; ?> href="<?php echo admin_url( esc_url( 'users.php?page=roles&amp;role_status=active' ) ); ?>"><?php _e( 'Active', 'members' ); ?> <span class="count">(<span id="active_count"><?php echo $active_roles; ?></span>)</span></a> | </li>
				<li><a <?php if ( 'inactive' == $role_status ) echo 'class="current"'; ?> href="<?php echo admin_url( esc_url( 'users.php?page=roles&amp;role_status=inactive' ) ); ?>"><?php _e( 'Inactive', 'members' ); ?> <span class="count">(<span id="inactive_count"><?php echo $inactive_roles; ?></span>)</span></a></li>
			</ul><!-- .subsubsub -->

			<div class="tablenav">

				<div class="alignleft actions">

					<select name="action">

						<option value="" selected="selected"><?php _e( 'Bulk Actions', 'members' ); ?></option>

						<?php if ( current_user_can( 'delete_roles' ) ) { ?>
							<option value="delete"><?php esc_html_e( 'Delete', 'members' ); ?></option>
						<?php } ?>

					</select>

					<?php submit_button( __( 'Apply', 'members' ), 'button-secondary action', 'doaction', false ); ?>

				</div><!-- .alignleft .actions -->

				<div class='tablenav-pages one-page'>
					<span class="displaying-num"><?php printf( _n( '%s item', '%s items', count( $roles_loop_array ), 'members' ), count( $roles_loop_array ) ); ?></span>
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

				<?php foreach ( $roles_loop_array as $role => $name ) { ?>

					<tr valign="top" class="<?php echo ( isset( $active_roles_arr[$role] ) ? 'active' : 'inactive' ); ?>">

						<th class="manage-column column-cb check-column">

							<?php if ( !current_user_can( $role ) && $role !== get_option( 'default_role' ) ) { ?>
								<input type="checkbox" name="roles[<?php echo esc_attr( $role ); ?>]" id="<?php echo esc_attr( $role ); ?>" value="<?php echo esc_attr( $role ); ?>" />
							<?php } ?>

						</th><!-- .manage-column .column-cb .check-column -->

						<td class="plugin-title">

							<?php $edit_link = admin_url( wp_nonce_url( "users.php?page=roles&amp;action=edit&amp;role={$role}", members_get_nonce( 'edit-roles' ) ) ); ?> 

							<a href="<?php echo esc_url( $edit_link ); ?>" title="<?php printf( esc_attr__( 'Edit the %s role', 'members' ), $name ); ?>"><strong><?php echo $name; ?></strong></a>

							<div class="row-actions">

								<a href="<?php echo esc_url( $edit_link ); ?>" title="<?php printf( esc_attr__( 'Edit the %s role', 'members' ), $name ); ?>"><?php _e( 'Edit', 'members' ); ?></a> 

								<?php if ( current_user_can( 'delete_roles' ) && $role !== get_option( 'default_role' ) && !current_user_can( $role ) ) { ?>
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
							<p><?php echo $role; ?></p>
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

				<div class="alignleft actions">

					<select name="action2">

						<option value="" selected="selected"><?php _e( 'Bulk Actions', 'members' ); ?></option>

						<?php if ( current_user_can( 'delete_roles' ) ) { ?>
							<option value="delete"><?php _e( 'Delete', 'members' ); ?></option>
						<?php } ?>

					</select>

					<?php submit_button( __( 'Apply', 'members' ), 'button-secondary action', 'doaction2', false ); ?>

				</div><!-- .alignleft .actions -->

				<br class="clear" />

			</div><!-- .tablenav -->

		</form><!-- #roles -->

	</div><!-- #poststuff -->

</div><!-- .wrap -->