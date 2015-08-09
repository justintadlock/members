<?php

/**
 * Class that displays the edit role screen and handles the form submissions for that page.
 *
 * @since  1.0.0
 * @access public
 */
final class Members_Admin_Role_Edit {

	/**
	 * Current role object to be edited/viewed.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    object
	 */
	protected $role;

	/**
	 * Whether the current role can be edited.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    bool
	 */
	protected $is_editable = true;

	/**
	 * Available capabilities.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $capabilities = array();

	/**
	 * Whether the page was updated.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    bool
	 */
	protected $role_updated = false;

	/**
	 * Runs on the `load-{$page}` hook.  This is the handler for form submissions.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function load() {

		// If the current user can't edit roles, don't proceed.
		if ( ! current_user_can( 'edit_roles' ) )
			wp_die( esc_html__( 'Whoah, partner!', 'members' ) );

		// Get the current role object to edit.
		$this->role = get_role( members_sanitize_role( $_GET['role'] ) );

		// If we don't have a real role, die.
		if ( is_null( $this->role ) )
			wp_die( esc_html__( 'The requested role to edit does not exist.', 'members' ) );

		// Get all the capabilities.
		$this->capabilities = members_get_capabilities();

		// Is the role editable?
		$this->is_editable = members_is_role_editable( $this->role->name );

		// Check if the form has been submitted.
		if ( $this->is_editable && ( isset( $_POST['role-caps'] ) || isset( $_POST['new-cap'] ) ) ) {

			// Verify the nonce.
			check_admin_referer( 'edit_role', 'members_edit_role_nonce' );

			// Set the $role_updated variable to true.
			$this->role_updated = true;

			// Loop through all available capabilities.
			foreach ( $this->capabilities as $cap ) {

				// Get the posted capability.
				$posted_cap = isset( $_POST['role-caps']["{$this->role->name}-{$cap}"] ) ? $_POST['role-caps']["{$this->role->name}-{$cap}"] : false;

				// If the role doesn't have the capability and it was selected, add it.
				if ( ! $this->role->has_cap( $cap ) && ! empty( $posted_cap ) )
					$this->role->add_cap( $cap );

				// If the role has the capability and it wasn't selected, remove it.
				elseif ( $this->role->has_cap( $cap ) && empty( $posted_cap ) )
					$this->role->remove_cap( $cap );

			} // End loop through existing capabilities.

			// If new caps were added and are in an array, we need to add them.
			if ( ! empty( $_POST['new-cap'] ) && is_array( $_POST['new-cap'] ) ) {

				// Loop through each new capability from the edit roles form.
				foreach ( $_POST['new-cap'] as $new_cap ) {

					// Sanitize the new capability to remove any unwanted characters.
					$new_cap = sanitize_key( $new_cap );

					// Run one more check to make sure the new capability exists. Add the cap to the role.
					if ( ! empty( $new_cap ) && ! $this->role->has_cap( $new_cap ) )
						$this->role->add_cap( $new_cap );

				} // End loop through new capabilities.

				// If new caps are added, we need to reset the $capabilities array.
				$this->capabilities = members_get_capabilities();

			} // End check for new capabilities.

		} // End check for form submission.

		if ( $this->role_updated )
			add_settings_error( 'members_edit_role', 'role_updated', sprintf( esc_html__( '%s role updated.', 'members' ), members_get_role_name( $this->role->name ) ), 'updated' );

		if ( ! $this->is_editable )
			add_settings_error( 'members_edit_role', 'role_uneditable', sprintf( esc_html__( 'The %s role is not editable. This means that it is most likely added via another plugin for a special use or that you do not have permission to edit it.', 'members' ), members_get_role_name( $this->role->name ) ) );

		if ( isset( $_GET['message'] ) && 'role_added' === $_GET['message'] )
			add_settings_error( 'members_edit_role', 'role_added', sprintf( esc_html__( 'The %s role has been created.', 'members' ), members_get_role_name( $this->role->name ) ), 'updated' );
	}

	/**
	 * Displays the page content.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function page() { ?>

		<div class="wrap">

			<h2>
				<?php esc_html_e( 'Edit Role', 'members' ); ?>

				<?php if ( current_user_can( 'create_roles' ) ) : ?>
					<?php printf( '<a class="page-title-action" href="%s">%s</a>', members_get_new_role_url(), esc_html__( 'Add New', 'members' ) ); ?>
				<?php endif; ?>
			</h2>

			<?php settings_errors( 'members_edit_role' ); ?>

			<div id="poststuff">

				<form name="form0" method="post" action="<?php echo members_get_edit_role_url( $this->role->name ); ?>">

					<?php wp_nonce_field( 'edit_role', 'members_edit_role_nonce' ); ?>

					<table class="form-table">

						<tr>
							<th>
								<?php esc_html_e( 'Role', 'members' ); ?>
							</th>
							<td>
								<input type="text" disabled="disabled" readonly="readonly" value="<?php echo esc_attr( $this->role->name ); ?>" />
							</td>
						</tr>

						<tr>
							<th>
								<?php esc_html_e( 'Role Name', 'members' ); ?>
							</th>
							<td>
								<input type="text" disabled="disabled" readonly="readonly" value="<?php echo esc_attr( members_get_role_name( $this->role->name ) ); ?>" />
							</td>
						</tr>

						<tr>
							<th>
								<?php esc_html_e( 'Capabilities', 'members' ); ?>
							</th>

							<td>
								<?php
									$i = -1;
									$disabled = $this->is_editable ? '' : ' disabled="disabled" readonly="readonly"';
								?>

								<?php foreach ( $this->capabilities as $cap ) : ?>

									<div class="members-role-checkbox <?php if ( ++$i % 3 == 0 ) echo 'clear'; ?>">
										<?php $has_cap = $this->role->has_cap( $cap ) ? true : false; // Note: $role->has_cap() returns a string intead of TRUE. ?>
										<label>
											<input type="checkbox" name="<?php echo esc_attr( "role-caps[{$this->role->name}-{$cap}]" ); ?>" <?php checked( true, $has_cap ); ?> value="true"<?php echo $disabled; ?> />
											<?php echo esc_html( $cap ); ?>
										</label>
									</div>

								<?php endforeach; ?>
							</td>
						</tr>

						<?php if ( $this->is_editable ) : ?>

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

					<?php if ( $this->is_editable ) submit_button( esc_attr__( 'Update Role', 'members' ) ); ?>

				</form>

			</div><!-- #poststuff -->

		</div><!-- .wrap -->
	<?php }
}
