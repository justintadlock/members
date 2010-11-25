<?php
/**
 * Page for creating new roles.  Displays the new role form and creates
 * a new role if a new role has been submitted.
 *
 * @package Members
 * @subpackage Components
 */

/* Check if the form has been submitted. */
if ( isset($_POST['new-role-submit']) && 'Y' == $_POST['new-role-submit'] ) {

	/* Verify the nonce. */
	check_admin_referer( members_get_nonce( 'new-role' ) );

	/* Check if any capabilities were selected. */
	if ( !empty( $_POST['capabilities'] ) && is_array( $_POST['capabilities'] ) )
		$new_user_caps = $_POST['capabilities'];

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

	<h2><?php _e('Add a new user role', 'members'); ?></h2>

	<?php if ( isset($new_role_added) and $new_role_added ) members_admin_message( '', sprintf( __('The %1$s role has been created.', 'members'), $_POST['role-name'] ) ); ?>

	<?php do_action( 'members_pre_new_role_form' ); // Available action hook for displaying messages. ?>

	<div id="poststuff">

		<form name="form0" method="post" action="<?php echo admin_url( "users.php?page=new-role" ); ?>" style="border:none;background:transparent;">

			<?php wp_nonce_field( members_get_nonce( 'new-role' ) ); ?>

			<div class="postbox open">

				<h3><?php _e('Create a new user role', 'members'); ?></h3>

				<div class="inside">

					<table class="form-table">
					<tr>
						<th style="width: 20%;">
							<strong><?php _e('About:', 'members'); ?></strong>
						</th>
						<td>
							<?php printf( __('Here you can create as many new roles as you\'d like.  Roles are a way of grouping your users.  You can give individual users a role from the <a href="%1$s" title="Manage Users">user management</a> screen.  This will allow you to do specific things for users with a specific role.  Once you\'ve created a new role, you can manage it with the <em>Edit Roles</em> component.', 'members'), admin_url( 'users.php' ) ); ?>
						</td>
					</tr>

					<tr>
						<th style="width: 20%;">
							<label for="role-id"><strong><?php _e('Role:', 'members'); ?></strong></label>
						</th>
						<td>
							<?php _e('<strong>Required:</strong> Enter the name of your role.  This is a unique key that should only contain numbers, letters, and underscores.  Please don\'t add spaces or other odd characters.', 'members'); ?>
							<br />
							<input type="text" id="role-id" name="role-id" value="" size="30" />
						</td>
					</tr>

					<tr>
						<th style="width: 20%;">
							<label for="role-name"><strong><?php _e('Role Label:', 'members'); ?></strong></label>
						</th>
						<td>
							<?php _e('<strong>Required:</strong> Enter a label your role.  This will be the title that is displayed in most cases.', 'members'); ?>
							<br />
							<input type="text" id="role-name" name="role-name" value="" size="30" />
						</td>
					</tr>

					<tr>
						<th style="width: 20%;">
							<strong><?php _e('Capabilities:', 'members'); ?></strong>
						</th>
						<td>
							<?php _e('<strong>Optional:</strong> Select which capabilities your new role should have.  These may be changed later using the <em>Edit Roles</em> component.', 'members'); ?>
							<br /><br />
							<?php foreach ( members_get_capabilities() as $cap ) : ?>
								<div style="float: left; width: 32.67%; margin: 0 0 5px 0;">
									<input name='capabilities[<?php echo $cap; ?>]' id='capabilities-<?php echo $cap; ?>' type="checkbox" value='<?php echo $cap; ?>' <?php if ( in_array( $cap, members_new_role_default_capabilities() ) ) echo "checked='checked'"; ?> /> 
									<label for="capabilities-<?php echo $cap; ?>"><?php if ( in_array( $cap, members_new_role_default_capabilities() ) ) echo "<strong>$cap</strong>"; else echo $cap; ?></label>
								</div>
							<?php endforeach; ?>
						</td>
					</tr>

					</table><!-- .form-table -->

				</div><!-- .inside -->

			</div><!-- .postbox -->

			<p class="submit">
				<input type="submit" name="Submit" class="button-primary" value="<?php _e('Create Role', 'members') ?>" />
				<input type="hidden" name="new-role-submit" value="Y" />
			</p><!-- .submit -->

		</form>

	</div><!-- #poststuff -->

</div><!-- .poststuff -->