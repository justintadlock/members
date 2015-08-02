<?php

final class Members_Admin_Role_New {

	/**
	 * Holds the instances of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance;

	/**
	 * Name of the page we've created.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $page = '';

	/**
	 * Role that's being created.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $role = '';

	/**
	 * Name of the role that's being created.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $role_name = '';

	/**
	 * Array of the role's capabilities.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $capabilities = array();

	/**
	 * Conditional to see if we're cloning a role.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    bool
	 */
	public $is_clone = false;

	/**
	 * Role that is being cloned.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $clone_role = '';

	/**
	 * Sets up our initial actions.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		// If the role manager is active.
		if ( members_get_setting( 'role_manager' ) )
			add_action( 'admin_menu', array( $this, 'add_admin_page' ) );
	}

	/**
	 * Adds the roles page to the admin.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function add_admin_page() {

		$this->page = add_submenu_page( 'users.php', esc_html__( 'Add New Role', 'members' ), esc_html__( 'Add New Role', 'members' ), 'create_roles', 'role-new', array( $this, 'page' ) );

		// Let's roll if we have a page.
		if ( $this->page ) {

			// Load actions.
			add_action( "load-{$this->page}", array( $this, 'load' ) );

			// Load scripts/styles.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		}
	}

	/**
	 * Checks posted data on load and performs actions if needed.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function load() {

		// Are we cloning a role?
		$this->is_clone = isset( $_GET['clone'] ) && members_role_exists( $_GET['clone'] );

		if ( $this->is_clone ) {

			add_filter( 'members_new_role_default_capabilities', array( $this, 'clone_default_caps' ) );

			$this->clone_role = members_sanitize_role( $_GET['clone'] );
		}

		// Check if the current user can create roles and the form has been submitted.
		if ( current_user_can( 'create_roles' ) && ( isset( $_POST['role-name'] ) || isset( $_POST['role-label'] ) || isset( $_POST['capabilities'] ) ) ) {

			// Verify the nonce.
			check_admin_referer( 'new_role', 'members_new_role_nonce' );

			// Assume no caps.
			$new_role_caps = null;

			// Check if any capabilities were selected.
			if ( isset( $_POST['capabilities'] ) && is_array( $_POST['capabilities'] ) ) {

				$new_role_caps = array();

				foreach ( members_get_capabilities() as $cap ) {

					// Make sure the cap is in the whitelist.
					if ( isset( $_POST['capabilities'][ $cap ] ) )
						$new_role_caps[ $cap ] = true;
				}

				if ( !empty( $new_role_caps ) )
					$this->capabilities = array_keys( $new_role_caps );
			}

			// Sanitize the new role, removing any unwanted characters.
			if ( !empty( $_POST['role-name'] ) )
				$this->role = members_sanitize_role( $_POST['role-name'] );

			// Sanitize the new role name/label. We just want to strip any tags here.
			if ( !empty( $_POST['role-label'] ) )
				$this->role_name = strip_tags( $_POST['role-label'] );

			// Add a new role with the data input.
			if ( $this->role && $this->role_name ) {
				add_role( $this->role, $this->role_name, $new_role_caps );

				// If the current user can edit roles, redirect to edit role screen.
				if ( current_user_can( 'edit_roles' ) ) {
					wp_redirect( add_query_arg( 'message', 'role_added', members_get_edit_role_url( $this->role, true ) ) );
 					exit;
				}

				// Add role added message.
				add_settings_error( 'members_role_new', 'role_added', sprintf( esc_html__( 'The %s role has been created.', 'members' ), $this->role_name ), 'updated' );
			}

			// Add error if there's no role.
			if ( ! $this->role )
				add_settings_error( 'members_role_new', 'no_role', esc_html__( 'You must enter a valid role.', 'members' ) );

			// Add error if there's no role name.
			if ( ! $this->role_name )
				add_settings_error( 'members_role_new', 'no_role_name', esc_html__( 'You must enter a valid role name.', 'members' ) );
		}

		// If we don't have caps yet, get the new role default caps.
		if ( empty( $this->capabilities ) )
			$this->capabilities = members_new_role_default_capabilities();
	}

	/**
	 * Loads necessary scripts/styles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue( $hook ) {

		if ( $this->page !== $hook )
			return;

		wp_enqueue_script( 'members-admin' );
		wp_enqueue_style(  'members-admin' );
	}

	/**
	 * Outputs the page.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function page() { ?>

		<div class="wrap">

			<h2><?php ! $this->is_clone ? esc_html_e( 'Add New Role', 'members' ) : esc_html_e( 'Clone Role', 'members' ); ?></h2>

			<?php settings_errors( 'members_role_new' ); ?>

			<div id="poststuff">

				<form name="form0" method="post" action="<?php echo members_get_new_role_url(); ?>">

					<?php wp_nonce_field( 'new_role', 'members_new_role_nonce' ); ?>

					<table class="form-table">

						<tr>
							<th>
								<?php esc_html_e( 'Role', 'members' ); ?>
							</th>
							<td>
								<input type="text" name="role-name" value="<?php echo esc_attr( $this->role ); ?>" size="30"<?php if ( $this->is_clone ) printf( 'placeholder="%s"', esc_attr( "{$this->clone_role}_clone" ) ); ?> />

								<p class="description">
									<?php esc_html_e( 'The role should be unique and contain only alphanumeric characters and underscores.', 'members' ); ?>
								</p>
							</td>
						</tr>

						<tr>
							<th>
								<?php esc_html_e( 'Role Name', 'members' ); ?>
							</th>
							<td>
								<input type="text" name="role-label" value="<?php echo esc_attr( $this->role_name ); ?>" size="30"<?php if ( $this->is_clone ) printf( 'placeholder="%s"', esc_attr( sprintf( __( '%s Clone', 'members' ), members_get_role_name( $this->clone_role ) ) ) ); ?> />

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

								<?php $i = -1; foreach ( members_get_capabilities() as $cap ) : ?>

									<div class="members-role-checkbox <?php if ( ++$i % 3 == 0 ) echo 'clear'; ?>">
										<input type="checkbox" name="capabilities[<?php echo esc_attr( $cap ); ?>]" id="capabilities-<?php echo esc_attr( $cap ); ?>" <?php checked( in_array( $cap, $this->capabilities ) ); ?> />
										<label for="capabilities-<?php echo esc_attr( $cap ); ?>" class="<?php echo in_array( $cap, $this->capabilities ) ? 'has-cap' : 'has-cap-not'; ?>"><?php echo esc_html( $cap ); ?></label>
									</div>

								<?php endforeach; ?>

								</div><!-- .role-checkboxes -->
							</td>
						</tr>

					</table><!-- .form-table -->

					<?php submit_button( esc_attr__( 'Add Role', 'members' ) ); ?>

				</form>

			</div><!-- #poststuff -->

		</div><!-- .poststuff -->

	<?php }

	/**
	 * Filters the new role default caps in the case that we're cloning a role.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array  $capabilities
	 * @param  array
	 */
	public function clone_default_caps( $capabilities ) {

		if ( $this->is_clone ) {

			$role = get_role( $this->clone_role );

			if ( $role && isset( $role->capabilities ) && is_array( $role->capabilities ) ) {

				$capabilities = array();

				foreach ( $role->capabilities as $cap => $grant ) {

					if ( false !== $grant )
						$capabilities[] = $cap;
				}
			}
		}

		return $capabilities;
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		if ( ! self::$instance )
			self::$instance = new self;

		return self::$instance;
	}
}

Members_Admin_Role_New::get_instance();
