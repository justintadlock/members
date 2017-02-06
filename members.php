<?php
/**
 * Plugin Name: Members
 * Plugin URI:  http://themehybrid.com/plugins/members
 * Description: A user and role management plugin that puts you in full control of your site's permissions. This plugin allows you to edit your roles and their capabilities, clone existing roles, assign multiple roles per user, block post content, or even make your site completely private.
 * Version:     1.1.3
 * Author:      Justin Tadlock
 * Author URI:  http://themehybrid.com
 * Text Domain: members
 * Domain Path: /languages
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
 * @version   1.1.3
 * @author    Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2009 - 2016, Justin Tadlock
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
		return 'members';
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
		require_once( $this->inc_dir . 'functions-users.php'               );
		require_once( $this->inc_dir . 'functions-widgets.php'             );

		// Load template files.
		require_once( $this->inc_dir . 'template.php' );

		// Load admin files.
		if ( is_admin() ) {

			// General admin functions.
			require_once( $this->admin_dir . 'functions-admin.php' );
			require_once( $this->admin_dir . 'functions-help.php'  );

			// Plugin settings.
			require_once( $this->admin_dir . 'class-settings.php' );

			// Edit users.
			require_once( $this->admin_dir . 'class-user-edit.php' );

			// Edit posts.
			require_once( $this->admin_dir . 'class-meta-box-content-permissions.php' );

			// Role management.
			require_once( $this->admin_dir . 'class-manage-roles.php'          );
			require_once( $this->admin_dir . 'class-roles.php'                 );
			require_once( $this->admin_dir . 'class-role-edit.php'             );
			require_once( $this->admin_dir . 'class-role-new.php'              );
			require_once( $this->admin_dir . 'class-meta-box-publish-role.php' );
			require_once( $this->admin_dir . 'class-meta-box-custom-cap.php'   );

			// Role groups.
			require_once( $this->admin_dir . 'class-role-group.php'         );
			require_once( $this->admin_dir . 'class-role-group-factory.php' );
			require_once( $this->admin_dir . 'functions-role-groups.php'    );

			// Edit capabilities tabs and groups.
			require_once( $this->admin_dir . 'class-cap-tabs.php'          );
			require_once( $this->admin_dir . 'class-cap-section.php'       );
			require_once( $this->admin_dir . 'class-cap-control.php'       );
			require_once( $this->admin_dir . 'class-cap-group.php'         );
			require_once( $this->admin_dir . 'class-cap-group-factory.php' );
			require_once( $this->admin_dir . 'functions-cap-groups.php'    );
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
		if ( ! empty( $role ) ) {

			$role->add_cap( 'list_roles'       ); // View roles in backend.
			$role->add_cap( 'create_roles'     ); // Create new roles.
			$role->add_cap( 'delete_roles'     ); // Delete existing roles.
			$role->add_cap( 'edit_roles'       ); // Edit existing roles/caps.
			$role->add_cap( 'restrict_content' ); // Edit per-post content permissions.
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
