<?php

final class Members_Settings_Page {

	/**
	 * Holds the instances of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance;

	/**
	 * Settings page name.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $settings_page = '';

	/**
	 * Holds an array the plugin settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $settings = array();

	/**
	 * Sets up the needed actions for adding and saving the meta boxes.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	private function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
	 * Sets up custom admin menus.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_menu() {

		$this->settings_page = add_options_page(
			esc_html__( 'Members Settings', 'members' ),
			esc_html__( 'Members',          'members' ),
			apply_filters( 'members_settings_capability', 'manage_options' ),
			'members-settings',
			array( $this, 'settings_page' )
		);

		if ( $this->settings_page ) {
			add_action( 'admin_init', array( $this, 'register_settings' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		}
	}

	public function enqueue( $hook_suffix ) {

		if ( $this->settings_page !== $hook_suffix )
			return;

		wp_enqueue_script( 'members-admin' );
	}

	/**
	 * Registers the plugin settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	function register_settings() {

		$this->settings = get_option( 'members_settings' );

		register_setting( 'members_settings', 'members_settings', array( $this, 'validate_settings' ) );

		// Add settings sections.
		add_settings_section( 'roles_caps',          esc_html__( 'Roles and Capabilities', 'members' ), array( $this, 'section_roles_caps'          ), $this->settings_page );
		add_settings_section( 'content_permissions', esc_html__( 'Content Permissions',    'members' ), array( $this, 'section_content_permissions' ), $this->settings_page );
		add_settings_section( 'sidebar_widgets',     esc_html__( 'Sidebar Widgets',        'members' ), array( $this, 'section_sidebar_widgets'     ), $this->settings_page );
		add_settings_section( 'private_site',        esc_html__( 'Private Site',           'members' ), array( $this, 'section_private_site'        ), $this->settings_page );

		// Add settings fields.
		add_settings_field( 'enable_role_manager', esc_html__( 'Role Manager',        'members' ), array( $this, 'field_enable_role_manager' ), $this->settings_page, 'roles_caps' );
		add_settings_field( 'enable_cap_manager',  esc_html__( 'Capability Manager',  'members' ), array( $this, 'field_enable_cap_manager'  ), $this->settings_page, 'roles_caps' );
		add_settings_field( 'enable_multi_roles',  esc_html__( 'Multiple User Roles', 'members' ), array( $this, 'field_enable_multi_roles'  ), $this->settings_page, 'roles_caps' );

		add_settings_field( 'enable_content_permissions', esc_html__( 'Enable Permissions', 'members' ), array( $this, 'field_enable_content_permissions' ), $this->settings_page, 'content_permissions' );
		add_settings_field( 'content_permissions_error',  esc_html__( 'Error Message',              'members' ), array( $this, 'field_content_permissions_error'  ), $this->settings_page, 'content_permissions' );

		add_settings_field( 'widget_login', esc_html__( 'Login Widget', 'members' ), array( $this, 'field_widget_login' ), $this->settings_page, 'sidebar_widgets' );
		add_settings_field( 'widget_users', esc_html__( 'Users Widget', 'members' ), array( $this, 'field_widget_users' ), $this->settings_page, 'sidebar_widgets' );

		add_settings_field( 'enable_private_site', esc_html__( 'Enable Private Site', 'members' ), array( $this, 'field_enable_private_site' ), $this->settings_page, 'private_site' );
		add_settings_field( 'enable_private_feed', esc_html__( 'Disable Feed',        'members' ), array( $this, 'field_enable_private_feed' ), $this->settings_page, 'private_site' );
		add_settings_field( 'private_feed_error',  esc_html__( 'Feed Error Message',  'members' ), array( $this, 'field_private_feed_error'  ), $this->settings_page, 'private_site' );
	}

	/**
	 * Validates the plugin settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array  $input
	 * @return array
	 * @return void
	 */
	function validate_settings( $settings ) {

		// Validate true/false checkboxes.
		$settings['role_manager']        = isset( $settings['role_manager'] )        ? true : false;
		$settings['cap_manager']         = isset( $settings['cap_manager'] )         ? true : false;
		$settings['multi_roles']         = isset( $settings['multi_roles'] )         ? true : false;
		$settings['content_permissions'] = isset( $settings['content_permissions'] ) ? true : false;
		$settings['login_form_widget']   = isset( $settings['login_form_widget'] )   ? 1 : 0;
		$settings['users_widget']        = isset( $settings['users_widget'] )        ? 1 : 0;
		$settings['private_blog']        = isset( $settings['private_blog'] )        ? 1 : 0;
		$settings['private_feed']        = isset( $settings['private_feed'] )        ? 1 : 0;

		// Kill evil scripts.
		$settings['content_permissions_error'] = stripslashes( wp_filter_post_kses( addslashes( $settings['content_permissions_error'] ) ) );
		$settings['private_feed_error']        = stripslashes( wp_filter_post_kses( addslashes( $settings['private_feed_error'] ) ) );

		// Return the validated/sanitized settings.
		return $settings;
	}

	public function section_roles_caps() { ?>
		<p class="description">
			<?php esc_html_e( 'Your roles and capabilities will not revert back to their previous settings after deactivating or uninstalling this plugin, so use this feature wisely.', 'members' ); ?>
		</p>
	<?php }

	public function section_content_permissions() {}
	public function section_sidebar_widgets() {}
	public function section_private_site() {}

	public function field_enable_role_manager() { ?>
		<p>
			<label>
				<input type="checkbox" name="members_settings[role_manager]" value="true" <?php checked( members_role_manager_enabled() ); ?> />
				<?php esc_html_e( 'Enable the role manager.', 'members' ); ?>
			</label>
		</p>
	<?php }

	public function field_enable_cap_manager() { ?>
		<p>
			<label>
				<input type="checkbox" name="members_settings[cap_manager]" value="true" <?php checked( members_cap_manager_enabled() ); ?> />
				<?php esc_html_e( 'Enable the capability manager.', 'members' ); ?>
			</label>
		</p>
	<?php }

	public function field_enable_multi_roles() { ?>
		<p>
			<label>
				<input type="checkbox" name="members_settings[multi_roles]" value="true" <?php checked( members_multiple_user_roles_enabled() ); ?> />
				<?php esc_html_e( 'Allow users to be assigned more than a single role.', 'members' ); ?>
			</label>
		</p>
	<?php }

	public function field_enable_content_permissions() { ?>
		<p>
			<label>
				<input type="checkbox" name="members_settings[content_permissions]" value="true" <?php checked( members_content_permissions_enabled() ); ?> />
				<?php esc_html_e( 'Enable the content permissions feature.', 'members' ); ?>
			</label>
		</p>
	<?php }

	public function field_content_permissions_error() {

		wp_editor(
			members_get_setting( 'content_permissions_error' ),
			'members_settings_content_permissions_error',
			array(
				'textarea_name'    => 'members_settings[content_permissions_error]',
				'drag_drop_upload' => true,
				'editor_height'    => 250
			)
		);
	}

	public function field_widget_login() { ?>
		<p>
			<label>
				<input type="checkbox" name="members_settings[login_form_widget]" value="1" <?php checked( 1, members_get_setting( 'login_form_widget' ) ); ?> />
				<?php esc_html_e( 'Enable the login form widget.', 'members' ); ?>
			</label>
		</p>
	<?php }

	public function field_widget_users() { ?>
		<p>
			<label>
				<input type="checkbox" name="members_settings[users_widget]" value="1" <?php checked( 1, members_get_setting( 'users_widget' ) ); ?> />
				<?php esc_html_e( 'Enable the users widget.', 'members' ); ?>
			</label>
		</p>
	<?php }

	public function field_enable_private_site() { ?>
		<p>
			<label>
				<input type="checkbox" name="members_settings[private_blog]" value="1" <?php checked( 1, members_get_setting( 'private_blog' ) ); ?> />
				<?php esc_html_e( 'Redirect all logged-out users to the login page before allowing them to view the site.', 'members' ); ?>
			</label>
		</p>
	<?php }

	public function field_enable_private_feed() { ?>
		<p>
			<label>
				<input type="checkbox" name="members_settings[private_feed]" value="1" <?php checked( 1, members_get_setting( 'private_feed' ) ); ?> />
				<?php esc_html_e( 'Show error message for feed items.', 'members' ); ?>
			</label>
		</p>
	<?php }

	public function field_private_feed_error() {

		wp_editor(
			members_get_setting( 'private_feed_error' ),
			'members_settings_private_feed_error',
			array(
				'textarea_name'    => 'members_settings[private_feed_error]',
				'drag_drop_upload' => true,
				'editor_height'    => 250
			)
		);
	}

	/**
	 * Renders the settings page.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function settings_page() { ?>

		<div class="wrap">
			<h2><?php _e( 'Members Settings', 'members' ); ?></h2>

			<form method="post" action="options.php">
				<?php settings_fields( 'members_settings' ); ?>
				<?php do_settings_sections( $this->settings_page ); ?>
				<?php submit_button( esc_attr__( 'Update Settings', 'members' ), 'primary' ); ?>
			</form>

		</div><!-- wrap -->
	<?php }

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		if ( !self::$instance )
			self::$instance = new self;

		return self::$instance;
	}
}

Members_Settings_Page::get_instance();
