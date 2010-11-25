<?php
/**
 * The Edit Role form is for editing individual roles. The role to edit must
 * have been selected on the Edit Roles page.
 *
 * @package Members
 * @subpackage Components
 */

/* Get the current role object to edit. */
$role = get_role( $role );

/* Get all the capabilities */
$capabilities = members_get_capabilities();

/* Check if the form has been submitted. */
if ( isset($_POST['edit-role-saved']) && 'Y' == $_POST['edit-role-saved'] ) {

	/* Verify the nonce. */
	check_admin_referer( members_get_nonce( 'edit-roles' ) );

	/* Set the $role_updated variable to true. */
	$role_updated = true;

	/* Loop through all available capabilities. */
	foreach ( $capabilities as $cap ) {

		/* Get the posted capability. */
		$posted_cap = isset($_POST['role-caps']["{$role->name}-{$cap}"]) ? $_POST['role-caps']["{$role->name}-{$cap}"] : false;
		
		/* If the role doesn't have the capability and it was selected, add it. */
		if ( !$role->has_cap( $cap ) && $posted_cap )
			$role->add_cap( $cap );

		/* If the role has the capability and it wasn't selected, remove it. */
		elseif ( $role->has_cap( $cap ) && !$posted_cap )
			$role->remove_cap( $cap );

	} // End loop through existing capabilities

	/* If new caps were added and are in an array, we need to add them. */
	if ( !empty( $_POST['new-cap'] ) && is_array( $_POST['new-cap'] ) ) {

		/* Loop through each new capability from the edit roles form. */
		foreach ( $_POST['new-cap'] as $new_cap ) {

			/* Sanitize the new capability to remove any unwanted characters. */
			$new_cap = strip_tags( $new_cap );
			$new_cap = str_replace( array( '-', ' ', '&nbsp;' ) , '_', $new_cap );
			$new_cap = preg_replace('/[^A-Za-z0-9_]/', '', $new_cap );
			$new_cap = strtolower( $new_cap );

			/* Run one more check to make sure the new capability exists. Add the cap to the role. */
			if ( $new_cap && !$role->has_cap( $new_cap ) )
				$role->add_cap( $new_cap );

		} // End loop through new capabilities

	} // End check for new capabilities

} // End check for form submission ?>

<div class="wrap">

	<h2><?php printf(__('Edit the %1$s role', 'members'), $role->name ); ?></h2>

	<?php if ( isset($role_updated) and $role_updated ) members_admin_message( '', __('Role updated.', 'members') ); ?>

	<?php do_action( 'members_pre_edit_role_form' ); //Available pre-form hook for displaying messages. ?>

	<div id="poststuff">

		<form name="form0" method="post" action="<?php echo admin_url( esc_url( "users.php?page=roles&amp;action=edit&amp;role={$role->name}" ) ); ?>" style="border:none;background:transparent;">

			<?php wp_nonce_field( members_get_nonce( 'edit-roles' ) ); ?>

			<div class="postbox open">

				<h3><?php printf( __('<strong>Role:</strong> %1$s', 'members'), $role->name ); ?></h3>

				<div class="inside">

					<table class="form-table">

					<tr>
						<th style="width: 20%;">
							<strong><?php _e('Capabilities', 'members'); ?></strong>
						</th>

						<td>
							<?php _e('Select which capabilities this role should have. Make sure you understand what the capability does before giving it to just any role. This is a powerful feature, but it can cause you some grief if you give regular ol\' Joe more capabilities than yourself.', 'members'); ?>
							<br /><br />
						<?php

							/* Looop through each available capability. */
							foreach ( $capabilities as $cap ) {

								/* If the role has the capability, set the checkbox to 'checked'. */
								if ( $role->has_cap( $cap ) )
									$checked = " checked='checked' ";

								/* If the role doesn't have the the capability, set the checkbox value to false. */
								else
									$checked = ''; ?>

								<div style='overflow: hidden; margin: 0 0 5px 0; float:left; width: 32.67%;'>
								<input name='<?php echo "role-caps[{$role->name}-{$cap}]"; ?>' id='<?php echo "{$role->name}-{$cap}"; ?>' <?php echo $checked; ?> type='checkbox' value='true' /> 
								<label for="<?php echo "{$role->name}-{$cap}"; ?>"><?php if ( $checked ) echo "<strong>$cap</strong>"; else echo "<em>$cap</em>"; ?></label>
								</div>

							<?php } // Endforeach ?>
						</td>
					</tr>

					<tr>
						<th style="width: 20%;">
							<strong><?php _e('New Capabilities', 'members'); ?></strong>
						</th>

						<td>
							<?php _e('Add up to six new capabilities with this form for this role (more can be added later). Please only use letters, numbers, and underscores.', 'members'); ?>
							<br /><br />
							<p style="margin: 0 0 5px 0; float:left; width: 32.67%;">
								<input type="text" id="new-cap-1" name="new-cap[]" value="" size="20" /> 
							</p>
							<p style="margin: 0 0 5px 0; float:left; width: 32.67%;">
								<input type="text" id="new-cap-2" name="new-cap[]" value="" size="20" /> 
							</p>
							<p style="margin: 0 0 5px 0; float:left; width: 32.67%;">
								<input type="text" id="new-cap-3" name="new-cap[]" value="" size="20" /> 
							</p>
							<p style="margin: 0 0 5px 0; float:left; width: 32.67%;">
								<input type="text" id="new-cap-4" name="new-cap[]" value="" size="20" /> 
							</p>
							<p style="margin: 0 0 5px 0; float:left; width: 32.67%;">
								<input type="text" id="new-cap-5" name="new-cap[]" value="" size="20" /> 
							</p>
							<p style="margin: 0 0 5px 0; float:left; width: 32.67%;">
								<input type="text" id="new-cap-6" name="new-cap[]" value="" size="20" /> 
							</p>

						</td>
					</tr>

					</table><!-- .form-table -->

				</div><!-- .inside -->

			</div><!-- .postbox .open -->

			<p class="submit" style="clear:both;">
				<input type="submit" name="Submit"  class="button-primary" value="<?php _e('Update Role', 'members') ?>" />
				<input type="hidden" name="edit-role-saved" value="Y" />
			</p><!-- .submit -->

		</form>

	</div><!-- #poststuff -->

</div><!-- .wrap -->