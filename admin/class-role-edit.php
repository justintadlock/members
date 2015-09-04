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

	protected $members_role;

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

		$this->members_role = members_get_role( $this->role->name );

		// Get all the capabilities.
		$this->capabilities = members_get_capabilities();

		// Is the role editable?
		$this->is_editable = members_is_role_editable( $this->role->name );

		// Check if the form has been submitted.
		if ( $this->is_editable && ( isset( $_POST['grant-caps'] ) || isset( $_POST['deny-caps'] ) || isset( $_POST['grant-new-caps'] ) || isset( $_POST['deny-new-caps'] ) ) ) {

			$grant_caps = ! empty( $_POST['grant-caps'] ) ? array_unique( $_POST['grant-caps'] ) : array();
			$deny_caps  = ! empty( $_POST['deny-caps'] )  ? array_unique( $_POST['deny-caps']  ) : array();

			$grant_new_caps = ! empty( $_POST['grant-new-caps'] ) ? array_unique( $_POST['grant-new-caps'] ) : array();
			$deny_new_caps  = ! empty( $_POST['deny-new-caps'] )  ? array_unique( $_POST['deny-new-caps']  ) : array();

			// Verify the nonce.
			check_admin_referer( 'edit_role', 'members_edit_role_nonce' );

			// Set the $role_updated variable to true.
			$this->role_updated = true;

			// Loop through all available capabilities.
			foreach ( $this->capabilities as $cap ) {

				// Get the posted capability.
				$grant_this_cap = in_array( $cap, $grant_caps );
				$deny_this_cap  = in_array( $cap, $deny_caps  );

				// Does the role have the cap?
				$is_granted_cap = $this->role->has_cap( $cap );
				$is_denied_cap  = isset( $this->role->capabilities[ $cap ] ) && false === $this->role->capabilities[ $cap ];

				if ( $grant_this_cap && ! $is_granted_cap )
					$this->role->add_cap( $cap );

				else if ( $deny_this_cap && ! $is_denied_cap )
					$this->role->add_cap( $cap, false );

				else if ( ! $grant_this_cap && $is_granted_cap )
					$this->role->remove_cap( $cap );

				else if ( ! $deny_this_cap && $is_denied_cap )
					$this->role->remove_cap( $cap );

			} // End loop through existing capabilities.

			foreach ( $grant_new_caps as $grant_new_cap ) {

				$_cap = members_sanitize_cap( $grant_new_cap );

				if ( ! in_array( $_cap, $this->capabilities ) )
					$this->role->add_cap( $_cap );
			}

			foreach ( $deny_new_caps as $deny_new_cap ) {

				$_cap = members_sanitize_cap( $deny_new_cap );

				if ( ! in_array( $_cap, $this->capabilities ) && ! in_array( $_cap, $grant_new_caps ) )
					$this->role->add_cap( $_cap, false );
			}

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

			$_nm_role = members_role_factory()->add_role( $this->role->name );

			$this->members_role = members_get_role( $this->role->name );

		} // End check for form submission.

		if ( $this->role_updated )
			add_settings_error( 'members_edit_role', 'role_updated', sprintf( esc_html__( '%s role updated.', 'members' ), members_get_role_name( $this->role->name ) ), 'updated' );

		if ( ! $this->is_editable )
			add_settings_error( 'members_edit_role', 'role_uneditable', sprintf( esc_html__( 'The %s role is not editable. This means that it is most likely added via another plugin for a special use or that you do not have permission to edit it.', 'members' ), members_get_role_name( $this->role->name ) ) );

		if ( isset( $_GET['message'] ) && 'role_added' === $_GET['message'] )
			add_settings_error( 'members_edit_role', 'role_added', sprintf( esc_html__( 'The %s role has been created.', 'members' ), members_get_role_name( $this->role->name ) ), 'updated' );

		// Enqueue scripts/styles.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	public function enqueue() {

		wp_enqueue_style(  'members-admin'     );
		wp_enqueue_script( 'members-edit-role' );
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

			<h1>
				<?php esc_html_e( 'Edit Role', 'members' ); ?>

				<?php if ( current_user_can( 'create_roles' ) ) : ?>
					<?php printf( '<a class="page-title-action" href="%s">%s</a>', members_get_new_role_url(), esc_html__( 'Add New', 'members' ) ); ?>
				<?php endif; ?>
			</h1>

			<?php settings_errors( 'members_edit_role' ); ?>

			<div id="poststuff">

				<form name="form0" method="post" action="<?php echo members_get_edit_role_url( $this->role->name ); ?>">

					<?php wp_nonce_field( 'edit_role', 'members_edit_role_nonce' ); ?>

					<div id="post-body" class="columns-2">

						<div id="post-body-content">

							<div id="titlediv" class="members-title-div">

								<div id="titlewrap">
									<span class="screen-reader-text"><?php esc_html_e( 'Role Name', 'members' ); ?></span>
									<input type="text" disabled="disabled" readonly="readonly" value="<?php echo esc_attr( members_get_role_name( $this->role->name ) ); ?>" />
								</div><!-- #titlewrap -->

								<div class="inside">
									<div id="edit-slug-box">
										<strong><?php esc_html_e( 'Role:', 'members' ); ?></strong> <?php echo esc_attr( $this->role->name ); ?> <!-- edit box -->
									</div>
								</div><!-- .inside -->

							</div><!-- .members-title-div -->

							<?php $cap_tabs = new Members_Cap_Tabs( $this->role->name ); ?>
							<?php $cap_tabs->display(); ?>

						</div><!-- #post-body-content -->

						<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
						<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>

						<div id="postbox-container-1" class="post-box-container column-1 side">

							<?php do_action( 'members_add_meta_boxes_role', $this->role->name ); ?>
							<?php do_meta_boxes( 'members_edit_role', 'side', $this->role ); ?>

						</div><!-- .post-box-container -->

					</div><!-- #post-body -->
				</form>

			</div><!-- #poststuff -->

		</div><!-- .wrap -->
	<?php }
}
