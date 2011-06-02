<?php
/**
 * This file handles the display of the edit role form and the updates submitted by the user for the role.
 *
 * @package Members
 * @subpackage Admin
 */

/* Get the current role object to edit. */
$role = get_role( esc_attr( strip_tags( $_GET['role'] ) ) );

/* Get all the capabilities */
$capabilities = members_get_capabilities();

/* Check if the current user can edit roles and the form has been submitted. */
if ( current_user_can( 'edit_roles' ) && ( isset( $_POST['role-caps'] ) || isset( $_POST['new-cap'] ) ) ) {

	/* Verify the nonce. */
	check_admin_referer( members_get_nonce( 'edit-roles' ) );

	/* Set the $role_updated variable to true. */
	$role_updated = true;

	/* Loop through all available capabilities. */
	foreach ( $capabilities as $cap ) {

		/* Get the posted capability. */
		$posted_cap = isset( $_POST['role-caps']["{$role->name}-{$cap}"] ) ? $_POST['role-caps']["{$role->name}-{$cap}"] : false;

		/* If the role doesn't have the capability and it was selected, add it. */
		if ( !$role->has_cap( $cap ) && !empty( $posted_cap ) )
			$role->add_cap( $cap );

		/* If the role has the capability and it wasn't selected, remove it. */
		elseif ( $role->has_cap( $cap ) && empty( $posted_cap ) )
			$role->remove_cap( $cap );

	} // End loop through existing capabilities

	/* If new caps were added and are in an array, we need to add them. */
	if ( !empty( $_POST['new-cap'] ) && is_array( $_POST['new-cap'] ) ) {

		/* Loop through each new capability from the edit roles form. */
		foreach ( $_POST['new-cap'] as $new_cap ) {

			/* Sanitize the new capability to remove any unwanted characters. */
			$new_cap = sanitize_key( $new_cap );

			/* Run one more check to make sure the new capability exists. Add the cap to the role. */
			if ( !empty( $new_cap ) && !$role->has_cap( $new_cap ) )
				$role->add_cap( $new_cap );

		} // End loop through new capabilities

		/* If new caps are added, we need to reset the $capabilities array. */
		$capabilities = members_get_capabilities();

	} // End check for new capabilities

} // End check for form submission ?>

<div class="wrap">

	<?php screen_icon(); ?>

	<h2>
		<?php _e( 'Edit Role', 'members' ); ?>
		<?php if ( current_user_can( 'create_roles' ) ) echo '<a href="' . admin_url( 'users.php?page=role-new' ) . '" class="add-new-h2">' . __( 'Add New', 'members' ) . '</a>'; ?>
	</h2>

	<?php if ( !empty( $role_updated ) ) echo '<div class="updated"><p><strong>' . __( 'Role updated.', 'members' ) . '</strong></p><p><a href="' . admin_url( 'users.php?page=roles' ) . '">' . __( '&larr; Back to Roles', 'members' ) . '</a></p></div>'; ?>

	<?php do_action( 'members_pre_edit_role_form' ); //Available pre-form hook for displaying messages. ?>

	<div id="poststuff">

		<form name="form0" method="post" action="<?php echo admin_url( esc_url( "users.php?page=roles&amp;action=edit&amp;role={$role->name}" ) ); ?>">

			<?php wp_nonce_field( members_get_nonce( 'edit-roles' ) ); ?>

			<table class="form-table">

				<tr>
					<th>
						<?php _e( 'Role Name', 'members' ); ?>
					</th>
					<td>
						<input type="text" disabled="disabled" readonly="readonly" value="<?php echo esc_attr( $role->name ); ?>" />
					</td>
				</tr>

				<tr>
					<th>
						<?php _e( 'Capabilities', 'members' ); ?>
					</th>

					<td>
						<?php $i = -1; foreach ( $capabilities as $cap ) { ?>

							<div class="members-role-checkbox <?php if ( ++$i % 3 == 0 ) echo 'clear'; ?>">
								<?php $has_cap = ( $role->has_cap( $cap ) ? true : false ); ?>
								<input  type="checkbox" name="<?php echo esc_attr( "role-caps[{$role->name}-{$cap}]" ); ?>" id="<?php echo esc_attr( "{$role->name}-{$cap}" ); ?>" <?php checked( true, $has_cap ); ?> value="true" /> 
								<label for="<?php echo esc_attr( "{$role->name}-{$cap}" ); ?>" class="<?php echo ( $has_cap ? 'has-cap' : 'has-cap-not' ); ?>"><?php echo $cap; ?></label>
							</div>

						<?php } // Endforeach ?>
					</td>
				</tr>

				<tr>
					<th>
						<?php _e( 'Custom Capabilities', 'members' ); ?>
					</th>
					<td>

						<p class="members-add-new-cap-wrap clear hide-if-no-js">
							<a class="button-secondary" id="members-add-new-cap"><?php _e( 'Add New Capability', 'members' ); ?></a>
						</p>
						<p class="new-cap-holder">
							<input type="text" class="new-cap hide-if-js" name="new-cap[]" value="" size="20" />
						</p>
					</td>
				</tr>

			</table><!-- .form-table -->

			<?php submit_button( esc_attr__( 'Update Role', 'members' ) ); ?>

		</form>

	</div><!-- #poststuff -->

</div><!-- .wrap -->