<?php
/**
 * This file handles the display of the edit role form and the updates submitted by the user for the role.
 *
 * @package Members
 * @subpackage Admin
 */

// If the current user can't edit roles, don't proceed.
if ( ! current_user_can( 'edit_roles' ) )
	wp_die( esc_html__( 'Whoah, partner!', 'members' ) );

// Get the current role object to edit.
$role = get_role( members_sanitize_role( $_GET['role'] ) );

// If we don't have a real role, die.
if ( is_null( $role ) )
	wp_die( esc_html__( 'The requested role to edit does not exist.', 'members' ) );

// Get all the capabilities.
$capabilities = members_get_capabilities();

// Is the role editable?
$is_editable = members_is_role_editable( $role->name );

// Set the `$role_updated` variable.
$role_updated = false;

// Check if the form has been submitted.
if ( $is_editable && ( isset( $_POST['role-caps'] ) || isset( $_POST['new-cap'] ) ) ) {

	// Verify the nonce.
	check_admin_referer( members_get_nonce( 'edit-roles' ) );

	// Set the $role_updated variable to true.
	$role_updated = true;

	// Loop through all available capabilities.
	foreach ( $capabilities as $cap ) {

		// Get the posted capability.
		$posted_cap = isset( $_POST['role-caps']["{$role->name}-{$cap}"] ) ? $_POST['role-caps']["{$role->name}-{$cap}"] : false;

		// If the role doesn't have the capability and it was selected, add it.
		if ( ! $role->has_cap( $cap ) && ! empty( $posted_cap ) )
			$role->add_cap( $cap );

		// If the role has the capability and it wasn't selected, remove it.
		elseif ( $role->has_cap( $cap ) && empty( $posted_cap ) )
			$role->remove_cap( $cap );

	} // End loop through existing capabilities.

	// If new caps were added and are in an array, we need to add them.
	if ( ! empty( $_POST['new-cap'] ) && is_array( $_POST['new-cap'] ) ) {

		// Loop through each new capability from the edit roles form.
		foreach ( $_POST['new-cap'] as $new_cap ) {

			// Sanitize the new capability to remove any unwanted characters.
			$new_cap = sanitize_key( $new_cap );

			// Run one more check to make sure the new capability exists. Add the cap to the role.
			if ( !empty( $new_cap ) && !$role->has_cap( $new_cap ) )
				$role->add_cap( $new_cap );

		} // End loop through new capabilities.

		// If new caps are added, we need to reset the $capabilities array.
		$capabilities = members_get_capabilities();

	} // End check for new capabilities.

} // End check for form submission. ?>

<div class="wrap">

	<h2>
		<?php esc_html_e( 'Edit Role', 'members' ); ?>
		<?php if ( current_user_can( 'create_roles' ) ) printf( '<a class="add-new-h2" href="%s">%s</a>', members_get_new_role_url(), esc_html__( 'Add New', 'members' ) ); ?>
	</h2>

	<?php if ( $role_updated ) : ?>
		<div class="updated">
			<p><strong><?php printf( esc_html__( '%s role updated.', 'members' ), members_get_role_name( $role->name ) ); ?></strong></p>
			<p><?php printf( '<a href="%s">%s</a>', members_get_edit_roles_url(), esc_html__( '&larr; Back to roles screen', 'members' ) ); ?>
		</div>
	<?php endif; ?>

	<?php if ( ! $is_editable ) : ?>
		<div class="error">
			<p><?php printf( esc_html__( 'The %s role is not editable. This means that it is most likely added via a third-party plugin that has a special use case for it or that you do not have permission to edit it.', 'members' ), members_get_role_name( $role->name ) ); ?></p>
		</div>
	<?php endif; ?>

	<?php do_action( 'members_pre_edit_role_form' ); //Available pre-form hook for displaying messages. ?>

	<div id="poststuff">

		<form name="form0" method="post" action="<?php echo esc_url( add_query_arg( array( 'page' => 'roles', 'action' => 'edit', 'role' => $role->name ), admin_url( 'users.php' ) ) ); ?>">

			<?php wp_nonce_field( members_get_nonce( 'edit-roles' ) ); ?>

			<table class="form-table">

				<tr>
					<th>
						<?php esc_html_e( 'Role', 'members' ); ?>
					</th>
					<td>
						<input type="text" disabled="disabled" readonly="readonly" value="<?php echo esc_attr( $role->name ); ?>" />
					</td>
				</tr>

				<tr>
					<th>
						<?php esc_html_e( 'Role Name', 'members' ); ?>
					</th>
					<td>
						<input type="text" disabled="disabled" readonly="readonly" value="<?php echo esc_attr( members_get_role_name( $role->name ) ); ?>" />
					</td>
				</tr>

				<tr>
					<th>
						<?php esc_html_e( 'Capabilities', 'members' ); ?>
					</th>

					<td>
						<?php
							$i = -1;
							$disabled = $is_editable ? '' : ' disabled="disabled" readonly="readonly"';
						?>

						<?php foreach ( $capabilities as $cap ) : ?>

							<div class="members-role-checkbox <?php if ( ++$i % 3 == 0 ) echo 'clear'; ?>">
								<?php $has_cap = $role->has_cap( $cap ) ? true : false; // Note: $role->has_cap() returns a string intead of TRUE. ?>
								<label class="<?php echo ( $has_cap ? 'has-cap' : 'has-cap-not' ); ?>">
									<input type="checkbox" name="<?php echo esc_attr( "role-caps[{$role->name}-{$cap}]" ); ?>" <?php checked( true, $has_cap ); ?> value="true"<?php echo $disabled; ?> />
									<?php echo esc_html( $cap ); ?>
								</label>
							</div>

						<?php endforeach; ?>
					</td>
				</tr>

				<?php if ( $is_editable ) : ?>

				<tr>
					<th>
						<?php esc_html_e( 'Custom Capabilities', 'members' ); ?>
					</th>
					<td>

						<p class="members-add-new-cap-wrap clear hide-if-no-js">
							<a class="button-secondary" id="members-add-new-cap"><?php esc_html_e( 'Add New Capability', 'members' ); ?></a>
						</p>
						<p class="new-cap-holder">
							<input type="text" class="new-cap hide-if-js" name="new-cap[]" value="" size="20" />
						</p>
					</td>
				</tr>

				<?php endif; ?>

			</table><!-- .form-table -->

			<?php if ( $is_editable ) submit_button( esc_attr__( 'Update Role', 'members' ) ); ?>

		</form>

	</div><!-- #poststuff -->

</div><!-- .wrap -->
