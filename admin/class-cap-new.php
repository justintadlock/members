<?php

final class Members_Admin_Cap_New {

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
	 * Cap that's being created.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $cap = '';

	/**
	 * Array of the cap's roles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $roles = array();

	/**
	 * Sets up our initial actions.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		// If the cap manager is active.
		if ( members_cap_manager_enabled() )
			add_action( 'admin_menu', array( $this, 'add_admin_page' ) );
	}

	/**
	 * Adds the caps page to the admin.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function add_admin_page() {

		$this->page = add_submenu_page( 'users.php', esc_html__( 'Add New Capability', 'members' ), esc_html__( 'Add New Capability', 'members' ), 'edit_roles', 'cap-new', array( $this, 'page' ) );

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

		// Check if the current user can edit roles and the form has been submitted.
		if ( current_user_can( 'edit_roles' ) && isset( $_POST['members_new_cap_nonce'] ) ) {

			// Verify the nonce.
			check_admin_referer( 'new_cap', 'members_new_cap_nonce' );

			// Get the cap name.
			if ( isset( $_POST['capability'] ) )
				$this->cap = members_sanitize_cap( $_POST['capability'] );

			// Get the roles to add the cap to.
			if ( isset( $_POST['roles'] ) && is_array( $_POST['roles'] ) )
				$this->roles = array_map( 'members_sanitize_role', $_POST['roles'] );

			// If we don't have a cap.
			if ( ! $this->cap )
				add_settings_error( 'members_cap_new', 'no_cap', esc_html__( 'Please input a valid capability.', 'members' ) );

			// If the cap exists.
			else if ( members_cap_exists( $this->cap ) )
				add_settings_error( 'members_cap_new', 'cap_exists', sprintf( esc_html__( 'The %s capability already exists.', 'members' ), '<code>' . $this->cap . '</code>' ) );

			// If we don't have roles.
			if ( empty( $this->roles ) )
				add_settings_error( 'members_cap_new', 'no_roles', esc_html__( 'Please select at least one role.', 'members' ) );

			// If the cap doesn't already exist and we have roles, let's add the cap.
			if ( $this->cap && ! members_cap_exists( $this->cap ) && ! empty( $this->roles ) ) {

				foreach ( $this->roles as $role ) {

					$role_obj = get_role( $role );

					$role_obj->add_cap( $this->cap );
				}

				// Add cap created message.
				add_settings_error( 'members_cap_new', 'cap_added', sprintf( esc_html__( 'The %s capability has been created.', 'members' ), '<code>' . $this->cap . '</code>' ), 'updated' );

				$this->roles = array();
				$this->cap = '';
			}
		}
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

			<h2><?php esc_html_e( 'Add New Capability', 'members' ); ?></h2>

			<?php settings_errors( 'members_cap_new' ); ?>

			<div id="poststuff">

				<form id="roles" action="<?php echo members_get_new_cap_url(); ?>" method="post">

					<?php wp_nonce_field( 'new_cap', 'members_new_cap_nonce' ); ?>

					<table class="form-table">

						<tr>
							<th>
								<label for="capability"><?php esc_html_e( 'Capability', 'members' ); ?></label>
							</th>
							<td>
								<input type="text" id="capability" name="capability" value="<?php echo esc_attr( $this->cap ); ?>" size="30" />

								<p class="description">
									<?php esc_html_e( 'The capability should be unique and contain only alphanumeric characters and underscores.', 'members' ); ?>
								</p>
							</td>
						</tr>

						<tr>
							<th>
								<?php esc_html_e( 'Roles', 'members' ); ?>
							</th>
							<td>
								<p class="description">
									<?php esc_html_e( 'Select at least one role. Because of the way capabilities work in WordPress, they can only exist if assigned to a role.', 'members' ); ?>
								</p>

								<ul>
									<?php foreach ( members_get_editable_role_names() as $role => $name ) : ?>

										<li>
											<label>
												<input type="checkbox" name="roles[<?php echo esc_attr( $role ); ?>]" value="<?php echo esc_attr( $role ); ?>" <?php checked( in_array( $role, $this->roles ) ); ?> />
												<?php echo esc_html( $name ); ?>
											</label>
										</li>

									<?php endforeach; ?>
								</ul>
							</td>
						</tr>

					</table><!-- .form-table -->

					<?php submit_button( esc_attr__( 'Add Capability', 'members' ) ); ?>

				</form><!-- #roles -->

			</div><!-- #poststuff -->

		</div><!-- .wrap -->
	<?php }

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

Members_Admin_Cap_New::get_instance();
