<?php
/**
 * Displays and processes the 'Add New Role' page in the admin.
 *
 * @package Members
 * @subpackage Admin
 */

/* Check if the current user can create roles and the form has been submitted. */
if ( current_user_can( 'create_roles' ) && isset( $_POST['role-name'] ) && isset( $_POST['role-label'] ) ) {

	/* Verify the nonce. */
	check_admin_referer( members_get_nonce( 'new-role' ) );

	/* Check if any capabilities were selected. */
	if ( !empty( $_POST['capabilities'] ) && is_array( $_POST['capabilities'] ) )
		$new_role_caps = array_map( 'esc_attr', $_POST['capabilities'] );

	/* If no capabilities were selected, set the variable to null. */
	else
		$new_role_caps = null;

	/* Check if both a role name and role were submitted. */
	if ( !empty( $_POST['role-name'] ) && !empty( $_POST['role-label'] ) ) {

		/* Sanitize the new role, removing any unwanted characters. */
		$new_role_name = sanitize_key( $_POST['role-name'] );

		/* Sanitize the new role name/label. We just want to strip any tags here. */
		$new_role_label = strip_tags( $_POST['role-label'] );

		/* Add a new role with the data input. */
		if ( !empty( $new_role_name ) && !empty( $new_role_label ) )
			$new_role_added = add_role( $new_role_name, $new_role_label, $new_role_caps );

	} // End check for role and role name

} // End check for form submit ?>

<div class="wrap">

	<?php screen_icon(); ?>

	<h2><?php _e( 'Add New Role', 'members' ); ?></h2>

	<?php if ( !empty( $new_role_added ) ) members_admin_message( '', sprintf( __( 'The %s role has been created.', 'members' ), esc_html( $_POST['role-name'] ) ) ); ?>

	<?php do_action( 'members_pre_new_role_form' ); // Available action hook for displaying messages. ?>

	<div id="poststuff">

		<form name="form0" method="post" action="<?php echo admin_url( 'users.php?page=role-new' ); ?>">

			<?php wp_nonce_field( members_get_nonce( 'new-role' ) ); ?>

			<table class="form-table">

				<tr>
					<th>
						<label for="role-name"><?php _e( 'Role Name', 'members' ); ?></label>
					</th>
					<td>
						<input type="text" id="role-name" name="role-name" value="" size="30" />
						<br />
						<label for="role-name"><?php _e( "<strong>Required:</strong> The role name should be unique and contain only alphanumeric characters and underscores.", 'members' ); ?></label>
					</td>
				</tr>

				<tr>
					<th>
						<label for="role-label"><?php _e( 'Role Label', 'members' ); ?></label>
					</th>
					<td>
						<input type="text" id="role-label" name="role-label" value="" size="30" />
						<br />
						<label for="role-label"><?php _e( '<strong>Required:</strong> The role label is used to represent your role in the WordPress admin.', 'members' ); ?></label>
					</td>
				</tr>

				<tr>
					<th>
						<?php _e( 'Role Capabilities', 'members' ); ?>
					</th>
					<td>
						<p>
							<?php _e( '<strong>Optional:</strong> Select the capabilities this role should have. These may be updated later.', 'members' ); ?>
						</p>

						<?php $i = -1; foreach ( members_get_capabilities() as $cap ) { ?>

							<div class="members-role-checkbox <?php if ( ++$i % 3 == 0 ) echo 'clear'; ?>">
								<input  type="checkbox" name="capabilities[<?php echo esc_attr( $cap ); ?>]" id="capabilities-<?php echo esc_attr( $cap ); ?>" <?php checked( true, in_array( $cap, members_new_role_default_capabilities() ) ); ?> value="true" /> 
								<label for="capabilities-<?php echo esc_attr( $cap ); ?>" class="<?php echo ( in_array( $cap, members_new_role_default_capabilities() ) ? 'has-cap' : 'has-cap-not' ); ?>"><?php echo $cap; ?></label>
							</div>

						<?php } // Endforeach ?>
					</td>
				</tr>

			</table><!-- .form-table -->

			<?php submit_button( esc_attr__( 'Add Role', 'members' ) ); ?>

		</form>

	</div><!-- #poststuff -->

</div><!-- .poststuff -->