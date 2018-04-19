<?php
/**
 * Handles the general settings view.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2018, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Members\Admin;

/**
 * Sets up and handles the general settings view.
 *
 * @since  2.0.0
 * @access public
 */
class View_General extends View {

	/**
	 * Holds an array the plugin settings.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    array
	 */
	public $settings = array();

	/**
	 * Enqueue scripts/styles.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {

		wp_enqueue_script( 'members-settings' );
	}

	/**
	 * Registers the plugin settings.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	function register_settings() {

		// Get the current plugin settings w/o the defaults.
		$this->settings = get_option( 'members_settings' );

		// Register the setting.
		register_setting( 'members_settings', 'members_settings', array( $this, 'validate_settings' ) );

		/* === Settings Sections === */

		// Add settings sections.
		add_settings_section( 'roles_caps',          esc_html__( 'Roles and Capabilities', 'members' ), array( $this, 'section_roles_caps' ), 'members-settings' );
		add_settings_section( 'content_permissions', esc_html__( 'Content Permissions',    'members' ), '__return_false',                     'members-settings' );
		add_settings_section( 'sidebar_widgets',     esc_html__( 'Sidebar Widgets',        'members' ), '__return_false',                     'members-settings' );
		add_settings_section( 'private_site',        esc_html__( 'Private Site',           'members' ), '__return_false',                     'members-settings' );

		/* === Settings Fields === */

		// Role manager fields.
		add_settings_field( 'enable_role_manager',  esc_html__( 'Role Manager',        'members' ), array( $this, 'field_enable_role_manager'  ), 'members-settings', 'roles_caps' );
		add_settings_field( 'enable_multi_roles',   esc_html__( 'Multiple User Roles', 'members' ), array( $this, 'field_enable_multi_roles'   ), 'members-settings', 'roles_caps' );
		add_settings_field( 'explicit_denied_caps', esc_html__( 'Capabilities',        'members' ), array( $this, 'field_explicit_denied_caps' ), 'members-settings', 'roles_caps' );

		// Content permissions fields.
		add_settings_field( 'enable_content_permissions', esc_html__( 'Enable Permissions', 'members' ), array( $this, 'field_enable_content_permissions' ), 'members-settings', 'content_permissions' );
		add_settings_field( 'content_permissions_error',  esc_html__( 'Error Message',      'members' ), array( $this, 'field_content_permissions_error'  ), 'members-settings', 'content_permissions' );

		// Widgets fields.
		add_settings_field( 'widget_login', esc_html__( 'Login Widget', 'members' ), array( $this, 'field_widget_login' ), 'members-settings', 'sidebar_widgets' );
		add_settings_field( 'widget_users', esc_html__( 'Users Widget', 'members' ), array( $this, 'field_widget_users' ), 'members-settings', 'sidebar_widgets' );

		// Private site fields.
		add_settings_field( 'enable_private_site', esc_html__( 'Enable Private Site', 'members' ), array( $this, 'field_enable_private_site' ), 'members-settings', 'private_site' );
		add_settings_field( 'private_rest_api',    esc_html__( 'REST API',            'members' ), array( $this, 'field_private_rest_api'    ), 'members-settings', 'private_site' );
		add_settings_field( 'enable_private_feed', esc_html__( 'Disable Feed',        'members' ), array( $this, 'field_enable_private_feed' ), 'members-settings', 'private_site' );
		add_settings_field( 'private_feed_error',  esc_html__( 'Feed Error Message',  'members' ), array( $this, 'field_private_feed_error'  ), 'members-settings', 'private_site' );
	}

	/**
	 * Validates the plugin settings.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  array  $input
	 * @return array
	 */
	function validate_settings( $settings ) {

		// Validate true/false checkboxes.
		$settings['role_manager']         = ! empty( $settings['role_manager'] )         ? true : false;
		$settings['explicit_denied_caps'] = ! empty( $settings['explicit_denied_caps'] ) ? true : false;
		$settings['show_human_caps']      = ! empty( $settings['show_human_caps'] )      ? true : false;
		$settings['multi_roles']          = ! empty( $settings['multi_roles'] )          ? true : false;
		$settings['content_permissions']  = ! empty( $settings['content_permissions'] )  ? true : false;
		$settings['login_form_widget']    = ! empty( $settings['login_form_widget'] )    ? true : false;
		$settings['users_widget']         = ! empty( $settings['users_widget'] )         ? true : false;
		$settings['private_blog']         = ! empty( $settings['private_blog'] )         ? true : false;
		$settings['private_rest_api']     = ! empty( $settings['private_rest_api'] )     ? true : false;
		$settings['private_feed']         = ! empty( $settings['private_feed'] )         ? true : false;

		// Kill evil scripts.
		$settings['content_permissions_error'] = stripslashes( wp_filter_post_kses( addslashes( $settings['content_permissions_error'] ) ) );
		$settings['private_feed_error']        = stripslashes( wp_filter_post_kses( addslashes( $settings['private_feed_error']        ) ) );

		// Return the validated/sanitized settings.
		return $settings;
	}

	/**
	 * Role/Caps section callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function section_roles_caps() { ?>

		<p class="description">
			<?php esc_html_e( 'Your roles and capabilities will not revert back to their previous settings after deactivating or uninstalling this plugin, so use this feature wisely.', 'members' ); ?>
		</p>
	<?php }

	/**
	 * Role manager field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function field_enable_role_manager() { ?>

		<label>
			<input type="checkbox" name="members_settings[role_manager]" value="true" <?php checked( members_role_manager_enabled() ); ?> />
			<?php esc_html_e( 'Enable the role manager.', 'members' ); ?>
		</label>
	<?php }

	/**
	 * Explicit denied caps field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function field_explicit_denied_caps() { ?>

		<fieldset>

			<p>
				<label>
					<input type="checkbox" name="members_settings[explicit_denied_caps]" value="true" <?php checked( members_explicitly_deny_caps() ); ?> />
					<?php esc_html_e( 'Denied capabilities should always overrule granted capabilities.', 'members' ); ?>
				</label>
			</p>

			<p>
				<label>
					<input type="checkbox" name="members_settings[show_human_caps]" value="true" <?php checked( members_show_human_caps() ); ?> />
					<?php esc_html_e( 'Show human-readable capabilities when possible.', 'members' ); ?>
				</label>
			</p>

		</fieldset>
	<?php }

	/**
	 * Multiple roles field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function field_enable_multi_roles() { ?>

		<label>
			<input type="checkbox" name="members_settings[multi_roles]" value="true" <?php checked( members_multiple_user_roles_enabled() ); ?> />
			<?php esc_html_e( 'Allow users to be assigned more than a single role.', 'members' ); ?>
		</label>
	<?php }

	/**
	 * Enable content permissions field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function field_enable_content_permissions() { ?>

		<label>
			<input type="checkbox" name="members_settings[content_permissions]" value="true" <?php checked( members_content_permissions_enabled() ); ?> />
			<?php esc_html_e( 'Enable the content permissions feature.', 'members' ); ?>
		</label>
	<?php }

	/**
	 * Content permissions error message field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
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

	/**
	 * Login widget field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function field_widget_login() { ?>

		<label>
			<input type="checkbox" name="members_settings[login_form_widget]" value="true" <?php checked( members_login_widget_enabled() ); ?> />
			<?php esc_html_e( 'Enable the login form widget.', 'members' ); ?>
		</label>
	<?php }

	/**
	 * Uers widget field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function field_widget_users() { ?>

		<label>
			<input type="checkbox" name="members_settings[users_widget]" value="true" <?php checked( members_users_widget_enabled() ); ?> />
			<?php esc_html_e( 'Enable the users widget.', 'members' ); ?>
		</label>
	<?php }

	/**
	 * Enable private site field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function field_enable_private_site() { ?>

		<label>
			<input type="checkbox" name="members_settings[private_blog]" value="true" <?php checked( members_is_private_blog() ); ?> />
			<?php esc_html_e( 'Redirect all logged-out users to the login page before allowing them to view the site.', 'members' ); ?>
		</label>
	<?php }

	/**
	 * Enable private REST API field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function field_private_rest_api() { ?>

		<label>
			<input type="checkbox" name="members_settings[private_rest_api]" value="true" <?php checked( members_is_private_rest_api() ); ?> />
			<?php esc_html_e( 'Require authentication for access to the REST API.', 'members' ); ?>
		</label>
	<?php }

	/**
	 * Enable private feed field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function field_enable_private_feed() { ?>

		<label>
			<input type="checkbox" name="members_settings[private_feed]" value="true" <?php checked( members_is_private_feed() ); ?> />
			<?php esc_html_e( 'Show error message for feed items.', 'members' ); ?>
		</label>
	<?php }

	/**
	 * Private feed error message field callback.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
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
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function template() { ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'members_settings' ); ?>
			<?php do_settings_sections( 'members-settings' ); ?>
			<?php submit_button( esc_attr__( 'Update Settings', 'members' ), 'primary' ); ?>
		</form>

	<?php }

	/**
	 * Adds help tabs.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function add_help_tabs() {

		// Get the current screen.
		$screen = get_current_screen();

		// Roles/Caps help tab.
		$screen->add_help_tab(
			array(
				'id'       => 'roles-caps',
				'title'    => esc_html__( 'Role and Capabilities', 'members' ),
				'callback' => array( $this, 'help_tab_roles_caps' )
			)
		);

		// Content Permissions help tab.
		$screen->add_help_tab(
			array(
				'id'       => 'content-permissions',
				'title'    => esc_html__( 'Content Permissions', 'members' ),
				'callback' => array( $this, 'help_tab_content_permissions' )
			)
		);

		// Widgets help tab.
		$screen->add_help_tab(
			array(
				'id'       => 'sidebar-widgets',
				'title'    => esc_html__( 'Sidebar Widgets', 'members' ),
				'callback' => array( $this, 'help_tab_sidebar_widgets' )
			)
		);

		// Private Site help tab.
		$screen->add_help_tab(
			array(
				'id'       => 'private-site',
				'title'    => esc_html__( 'Private Site', 'members' ),
				'callback' => array( $this, 'help_tab_private_site' )
			)
		);

		// Set the help sidebar.
		$screen->set_help_sidebar( members_get_help_sidebar_text() );
	}

	/**
	 * Displays the roles/caps help tab.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function help_tab_roles_caps() { ?>

		<p>
			<?php esc_html_e( 'The role manager allows you to manage roles on your site by giving you the ability to create, edit, and delete any role. Note that changes to roles do not change settings for the Members plugin. You are literally changing data in your WordPress database. This plugin feature merely provides an interface for you to make these changes.', 'members' ); ?>
		</p>

		<p>
			<?php esc_html_e( 'The multiple user roles feature allows you to assign more than one role to each user from the edit user screen.', 'members' ); ?>
		</p>

		<p>
			<?php esc_html_e( 'Tick the checkbox for denied capabilities to always take precedence over granted capabilities when there is a conflict. This is only relevant when using multiple roles per user.', 'members' ); ?>
		</p>

		<p>
			<?php esc_html_e( 'Tick the checkbox to show human-readable capabilities when possible. Note that custom capabilities and capabilities from third-party plugins will show the machine-readable capability name unless they are registered.', 'members' ); ?>
		</p>
	<?php }

	/**
	 * Displays the content permissions help tab.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function help_tab_content_permissions() { ?>

		<p>
			<?php printf( esc_html__( "The content permissions features adds a meta box to the edit post screen that allows you to grant permissions for who can read the post content based on the user's role. Only users of roles with the %s capability will be able to use this component.", 'members' ), '<code>restrict_content</code>' ); ?>
		</p>
	<?php }

	/**
	 * Displays the sidebar widgets help tab.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function help_tab_sidebar_widgets() { ?>

		<p>
			<?php esc_html_e( "The sidebar widgets feature adds additional widgets for use in your theme's sidebars.", 'members' ); ?>
		</p>
	<?php }

	/**
	 * Displays the private site help tab.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function help_tab_private_site() { ?>

		<p>
			<?php esc_html_e( 'The private site feature redirects all users who are not logged into the site to the login page, creating an entirely private site. You may also replace your feed content with a custom error message.', 'members' ); ?>
		</p>
	<?php }
}
