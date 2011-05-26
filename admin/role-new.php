<?php
/**
 * @todo Add inline styles the the admin.css stylesheet.
 *
 * @package Members
 * @subpackage Admin
 */

/* Check if the form has been submitted. */
if ( isset( $_POST['role-id'] ) && isset( $_POST['role-name'] ) ) {

	/* Verify the nonce. */
	check_admin_referer( members_get_nonce( 'new-role' ) );

	/* Check if any capabilities were selected. */
	if ( !empty( $_POST['capabilities'] ) && is_array( $_POST['capabilities'] ) )
		$new_user_caps = array_map( 'esc_attr', $_POST['capabilities'] );

	/* If no capabilities were selected, set the variable to null. */
	else
		$new_user_caps = null;

	/* Check if both a role name and role were submitted. */
	if ( isset($_POST['role-name']) && isset($_POST['role-id']) ) {

		/* Sanitize the new role, removing any unwanted characters. */
		$new_role = strip_tags( $_POST['role-id'] );
		$new_role = str_replace( array( '-', ' ', '&nbsp;' ) , '_', $new_role );
		$new_role = preg_replace('/[^A-Za-z0-9_]/', '', $new_role );
		$new_role = strtolower( $new_role );

		/* Sanitize the new role name/label. We just want to strip any tags here. */
		$new_role_name = strip_tags( $_POST['role-name'] ); // Should we use something like the WP user sanitation method?

		/* Add a new role with the data input. */
		$new_role_added = add_role( $new_role, $new_role_name, $new_user_caps );

	} // End check for role and role name

} // End check for form submit ?>

<div class="wrap">

	<?php screen_icon(); ?>

	<h2><?php _e( 'Add New Role', 'members' ); ?></h2>

	<?php if ( isset($new_role_added) and $new_role_added ) members_admin_message( '', sprintf( __('The %1$s role has been created.', 'members'), $_POST['role-name'] ) ); ?>

	<?php do_action( 'members_pre_new_role_form' ); // Available action hook for displaying messages. ?>

	<div id="poststuff">

		<form name="form0" method="post" action="<?php echo admin_url( "users.php?page=new-role" ); ?>">

			<?php wp_nonce_field( members_get_nonce( 'new-role' ) ); ?>


					<table class="form-table">

					<tr>
						<th>
							<label for="role-id"><?php _e( 'Role Name', 'members' ); ?></label>
						</th>
						<td>
							<?php _e( "<strong>Required:</strong> Enter the name of your role. This is a unique key that should only contain numbers, letters, and underscores.  Please don't add spaces or other odd characters.", 'members' ); ?>
							<br />
							<input type="text" id="role-id" name="role-id" value="" size="30" />
						</td>
					</tr>

					<tr>
						<th>
							<label for="role-name"><?php _e( 'Role Label', 'members' ); ?></label>
						</th>
						<td>
							<?php _e( '<strong>Required:</strong> Enter a label your role. This is the label used for the role in the WordPress admin.', 'members' ); ?>
							<br />
							<input type="text" id="role-name" name="role-name" value="" size="30" />
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

							<?php $i = -1; foreach ( members_get_capabilities() as $cap ) : ?>

								<div class="members-role-checkbox <?php if ( ++$i % 3 == 0 ) echo 'clear'; ?>">
									<input  type="checkbox" name="capabilities[<?php echo esc_attr( $cap ); ?>]" id="capabilities-<?php echo esc_attr( $cap ); ?>" <?php checked( true, in_array( $cap, members_new_role_default_capabilities() ) ); ?> value="true" /> 
									<label for="capabilities-<?php echo esc_attr( $cap ); ?>" class="<?php echo ( in_array( $cap, members_new_role_default_capabilities() ) ? 'has-cap' : 'has-cap-not' ); ?>"><?php echo $cap; ?></label>
								</div>
							<?php endforeach; ?>
						</td>
					</tr>

					</table><!-- .form-table -->

			<?php submit_button( __( 'Add Role', 'members' ) ); ?>

		</form>

	</div><!-- #poststuff -->

</div><!-- .poststuff -->