<?php
/**
 * Displays and processes the 'Add New Role' page in the admin.
 *
 * @package Members
 * @subpackage Admin
 */

// Set the new role added variable.
$new_role_added = false;

// Are we cloning a role?
$is_clone   = isset( $_GET['clone'] ) && members_role_exists( $_GET['clone'] );
$clone_role = $is_clone ? members_sanitize_role( $_GET['clone'] ) : '';

// Check if the current user can create roles and the form has been submitted.
if ( current_user_can( 'create_roles' ) && !empty( $_POST['role-name'] ) && !empty( $_POST['role-label'] ) ) {

	// Verify the nonce.
	check_admin_referer( members_get_nonce( 'new-role' ) );

	// Assume no caps.
	$new_role_caps = null;

	// Check if any capabilities were selected.
	if ( isset( $_POST['capabilities'] ) && is_array( $_POST['capabilities'] ) ) {

		$new_role_caps = array();

		foreach ( members_get_capabilities() as $cap ) {

			if ( isset( $_POST['capabilities'][ $cap ] ) )
				$new_role_caps[ $cap ] = true;
		}
	}

	// Sanitize the new role, removing any unwanted characters.
	$new_role_name = members_sanitize_role( $_POST['role-name'] );

	// Sanitize the new role name/label. We just want to strip any tags here.
	$new_role_label = strip_tags( $_POST['role-label'] );

	// Add a new role with the data input.
	if ( !empty( $new_role_name ) && !empty( $new_role_label ) )
		$new_role_added = add_role( $new_role_name, $new_role_label, $new_role_caps );

} // End check for form submit

# Filters the new role default capabilities when cloning a role.
add_filter( 'members_new_role_default_capabilities', 'members_clone_role_default_capabilities', 5 );

global $wp_roles;

$default_caps = members_new_role_default_capabilities(); ?>

<div class="wrap">

	<h2><?php ! $is_clone ? esc_html_e( 'Add New Role', 'members' ) : esc_html_e( 'Clone Role', 'members' ); ?></h2>

	<?php if ( $new_role_added ) : ?>
		<div class="updated">
			<p><strong><?php printf( esc_html__( 'The %s role has been created.', 'members' ), members_get_role_name( members_sanitize_role( $_POST['role-name'] ) ) ); ?></strong></p>
			<p><?php printf( '<a href="%s">%s</a>', members_get_edit_roles_url(), esc_html__( '&larr; Back to roles screen', 'members' ) ); ?>
		</div>
	<?php endif; ?>

	<?php do_action( 'members_pre_new_role_form' ); // Available action hook for displaying messages. ?>

	<div id="poststuff">

		<form name="form0" method="post" action="<?php echo members_get_new_role_url(); ?>">

			<?php wp_nonce_field( members_get_nonce( 'new-role' ) ); ?>

			<table class="form-table">

				<tr>
					<th>
						<label for="role-name"><?php esc_html_e( 'Role', 'members' ); ?></label>
					</th>
					<td>
						<input type="text" id="role-name" name="role-name" value="" size="30"<?php if ( $is_clone ) echo ' placeholder="' . esc_attr( $clone_role ) . '_clone"'; ?> />

						<p class="description">
							<?php esc_html_e( 'The role should be unique and contain only alphanumeric characters and underscores.', 'members' ); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th>
						<label for="role-label"><?php esc_html_e( 'Role Name', 'members' ); ?></label>
					</th>
					<td>
						<input type="text" id="role-label" name="role-label" value="" size="30"<?php if ( $is_clone ) echo ' placeholder="' . esc_attr( sprintf( __( '%s Clone', 'members' ), members_get_role_name( $clone_role ) ) ) . '"'; ?> />

						<p class="description">
							<?php esc_html_e( 'The role name is the human-readable name of your role.', 'members' ); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th>
						<?php esc_html_e( 'Capabilities', 'members' ); ?>
					</th>
					<td>
						<p class="description">
							<?php esc_html_e( 'Select the capabilities this role should have. These may be updated later.', 'members' ); ?>
						</p>

						<div class="role-checkboxes">

						<?php $i = -1; foreach ( members_get_capabilities() as $cap ) { ?>

							<div class="members-role-checkbox <?php if ( ++$i % 3 == 0 ) echo 'clear'; ?>">
								<input type="checkbox" name="capabilities[<?php echo esc_attr( $cap ); ?>]" id="capabilities-<?php echo esc_attr( $cap ); ?>" <?php checked( true, in_array( $cap, members_new_role_default_capabilities() ) ); ?> />
								<label for="capabilities-<?php echo esc_attr( $cap ); ?>" class="<?php echo ( in_array( $cap, $default_caps ) ? 'has-cap' : 'has-cap-not' ); ?>"><?php echo esc_html( $cap ); ?></label>
							</div>

						<?php } // Endforeach ?>

						</div><!-- .role-checkboxes -->
					</td>
				</tr>

			</table><!-- .form-table -->

			<?php submit_button( esc_attr__( 'Add Role', 'members' ) ); ?>

		</form>

	</div><!-- #poststuff -->

</div><!-- .poststuff -->
