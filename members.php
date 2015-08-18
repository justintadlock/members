<?php
/**
 * Plugin Name: Members
 * Plugin URI:  http://themehybrid.com/plugins/members
 * Description: A user, role, and content management plugin for controlling permissions and access. A plugin for making WordPress a more powerful <acronym title="Content Management System">CMS</acronym>.
 * Version:     1.0.0-alpha-1
 * Author:      Justin Tadlock
 * Author URI:  http://justintadlock.com
 *
 * The members plugin was created because the WordPress community is lacking a solid permissions
 * plugin that is both open source and works completely within the confines of the APIs in WordPress.
 * But, the plugin is so much more than just a plugin to control permissions.  It is meant to extend
 * WordPress by making user, role, and content management as simple as using WordPress itself.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not,
 * write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @package   Members
 * @version   1.0.0
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2009 - 2015, Justin Tadlock
 * @link      http://themehybrid.com/plugins/members
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Singleton class for setting up the plugin.
 *
 * @since  1.0.0
 * @access public
 */
final class Members_Plugin {

	/**
	 * Plugin directory path.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $dir_path = '';

	/**
	 * Plugin directory URI.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $dir_uri = '';

	/**
	 * Plugin admin directory path.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $admin_dir = '';

	/**
	 * Plugin includes directory path.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $inc_dir = '';

	/**
	 * Plugin templates directory path.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $templates_dir = '';

	/**
	 * Plugin CSS directory URI.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $css_uri = '';

	/**
	 * Plugin JS directory URI.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $js_uri = '';

	/**
	 * User count of all roles.
	 *
	 * @see    members_get_role_user_count()
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $role_user_count = array();

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new Members_Plugin;
			$instance->setup();
			$instance->includes();
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Magic method to output a string if trying to use the object as a string.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function __toString() {
		return esc_html__( 'Members', 'members' );
	}

	/**
	 * Magic method to keep the object from being cloned.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Whoah, partner!', 'members' ), '1.0.0' );
	}

	/**
	 * Magic method to keep the object from being unserialized.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Whoah, partner!', 'members' ), '1.0.0' );
	}

	/**
	 * Magic method to prevent a fatal error when calling a method that doesn't exist.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return null
	 */
	public function __call( $method = '', $args = array() ) {
		_doing_it_wrong( "Members_Plugin::{$method}", esc_html__( 'Method does not exist.', 'members' ), '1.0.0' );
		unset( $method, $args );
		return null;
	}

	/**
	 * Sets up globals.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	private function setup() {

		// Main plugin directory path and URI.
		$this->dir_path = trailingslashit( plugin_dir_path( __FILE__ ) );
		$this->dir_uri  = trailingslashit( plugin_dir_url(  __FILE__ ) );

		// Plugin directory paths.
		$this->inc_dir       = trailingslashit( $this->dir_path . 'inc'       );
		$this->admin_dir     = trailingslashit( $this->dir_path . 'admin'     );
		$this->templates_dir = trailingslashit( $this->dir_path . 'templates' );

		// Plugin directory URIs.
		$this->css_uri = trailingslashit( $this->dir_uri . 'css' );
		$this->js_uri  = trailingslashit( $this->dir_uri . 'js'  );
	}

	/**
	 * Loads files needed by the plugin.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	private function includes() {

		// Load class files.
		require_once( $this->inc_dir . 'class-role.php'         );
		require_once( $this->inc_dir . 'class-role-factory.php' );

		// Load includes files.
		require_once( $this->inc_dir . 'functions.php'                     );
		require_once( $this->inc_dir . 'functions-admin-bar.php'           );
		require_once( $this->inc_dir . 'functions-capabilities.php'        );
		require_once( $this->inc_dir . 'functions-content-permissions.php' );
		require_once( $this->inc_dir . 'functions-deprecated.php'          );
		require_once( $this->inc_dir . 'functions-options.php'             );
		require_once( $this->inc_dir . 'functions-private-site.php'        );
		require_once( $this->inc_dir . 'functions-roles.php'               );
		require_once( $this->inc_dir . 'functions-shortcodes.php'          );
		require_once( $this->inc_dir . 'functions-widgets.php'             );

		// Load template files.
		require_once( $this->inc_dir . 'template.php' );

		// Load admin files.
		if ( is_admin() ) {
			require_once( $this->admin_dir . 'admin.php'          );
			require_once( $this->admin_dir . 'class-settings.php' );

			require_once( $this->admin_dir . 'class-manage-roles.php'              );
			require_once( $this->admin_dir . 'class-roles.php'                     );
			require_once( $this->admin_dir . 'class-role-edit.php'                 );
			require_once( $this->admin_dir . 'class-role-new.php'                  );
			require_once( $this->admin_dir . 'quick-edit-content-permissions.php'  );

			require_once( $this->admin_dir . 'page-capabilities.php' );
			require_once( $this->admin_dir . 'class-cap-new.php'     );
		}
	}

	/**
	 * Sets up main plugin actions and filters.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	private function setup_actions() {

		// Internationalize the text strings used.
		add_action( 'plugins_loaded', array( $this, 'i18n' ), 2 );

		// Register activation hook.
		register_activation_hook( __FILE__, array( $this, 'activation' ) );
	}

	/**
	 * Loads the translation files.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function i18n() {
		load_plugin_textdomain( 'members', false, trailingslashit( dirname( plugin_basename( __FILE__ ) ) ). 'languages' );
	}

	/**
	 * Method that runs only when the plugin is activated.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function activation() {

		// Get the administrator role.
		$role = get_role( 'administrator' );

		// If the administrator role exists, add required capabilities for the plugin.
		if ( !empty( $role ) ) {

			$role->add_cap( 'list_roles'       ); // View roles in backend.
			$role->add_cap( 'create_roles'     ); // Create new roles.
			$role->add_cap( 'delete_roles'     ); // Delete existing roles.
			$role->add_cap( 'edit_roles'       ); // Edit existing roles/caps.
			$role->add_cap( 'restrict_content' ); // Edit per-post content permissions.
		}

		// If the administrator role does not exist for some reason, we have a bit of a problem
		// because this is a role management plugin and requires that someone actually be able to
		// manage roles.  So, we're going to create a custom role here.  The site administrator can
		// assign this custom role to any user they wish to work around this problem.  We're only
		// doing this for single-site installs of WordPress.  The 'super admin' has permission to do
		// pretty much anything on a multisite install.
		elseif ( empty( $role ) && !is_multisite() ) {

			// Add the `members_role_manager` role with limited capabilities.
			add_role(
				'members_role_manager',
				esc_html_x( 'Role Manager', 'role', 'members' ),
				array(
					'read'       => true,
					'list_roles' => true,
					'edit_roles' => true
				)
			);
		}
	}
}

/**
 * Gets the instance of the `Members_Plugin` class.  This function is useful for quickly grabbing data
 * used throughout the plugin.
 *
 * @since  1.0.0
 * @access public
 * @return object
 */
function members_plugin() {
	return Members_Plugin::get_instance();
}

// Let's roll!
members_plugin();
