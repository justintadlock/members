<?php
/**
 * Plugin Name: Members
 * Plugin URI:  https://themehybrid.com/plugins/members
 * Description: A user and role management plugin that puts you in full control of your site's permissions. This plugin allows you to edit your roles and their capabilities, clone existing roles, assign multiple roles per user, block post content, or even make your site completely private.
 * Version:     2.0.2
 * Author:      Justin Tadlock
 * Author URI:  https://themehybrid.com
 * Text Domain: members
 * Domain Path: /lang
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
 * @version   2.0.2
 * @author    Justin Tadlock <justintadlock@gmail.com>
 * @copyright Copyright (c) 2009 - 2017, Justin Tadlock
 * @link      https://themehybrid.com/plugins/members
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
	 * Minimum required PHP version.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	private $php_version = '5.3.0';

	/**
	 * Plugin directory path.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $dir = '';

	/**
	 * Plugin directory URI.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $uri = '';

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
			$instance = new self;
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
	 * @access private
	 * @return void
	 */
	private function setup() {

		// Main plugin directory path and URI.
		$this->dir = trailingslashit( plugin_dir_path( __FILE__ ) );
		$this->uri  = trailingslashit( plugin_dir_url(  __FILE__ ) );
	}

	/**
	 * Loads files needed by the plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function includes() {

		// Check if we meet the minimum PHP version.
		if ( version_compare( PHP_VERSION, $this->php_version, '<' ) ) {

			// Add admin notice.
			add_action( 'admin_notices', array( $this, 'php_admin_notice' ) );

			// Bail.
			return;
		}

		// Load class files.
		require_once( $this->dir . 'inc/class-capability.php' );
		require_once( $this->dir . 'inc/class-cap-group.php'  );
		require_once( $this->dir . 'inc/class-registry.php'   );
		require_once( $this->dir . 'inc/class-role-group.php' );
		require_once( $this->dir . 'inc/class-role.php'       );

		// Load includes files.
		require_once( $this->dir . 'inc/functions.php'                     );
		require_once( $this->dir . 'inc/functions-admin-bar.php'           );
		require_once( $this->dir . 'inc/functions-capabilities.php'        );
		require_once( $this->dir . 'inc/functions-cap-groups.php'          );
		require_once( $this->dir . 'inc/functions-content-permissions.php' );
		require_once( $this->dir . 'inc/functions-deprecated.php'          );
		require_once( $this->dir . 'inc/functions-options.php'             );
		require_once( $this->dir . 'inc/functions-private-site.php'        );
		require_once( $this->dir . 'inc/functions-roles.php'               );
		require_once( $this->dir . 'inc/functions-role-groups.php'         );
		require_once( $this->dir . 'inc/functions-shortcodes.php'          );
		require_once( $this->dir . 'inc/functions-users.php'               );
		require_once( $this->dir . 'inc/functions-widgets.php'             );

		// Load template files.
		require_once( $this->dir . 'inc/template.php' );

		// Load admin files.
		if ( is_admin() ) {

			// General admin functions.
			require_once( $this->dir . 'admin/functions-admin.php' );
			require_once( $this->dir . 'admin/functions-help.php'  );

			// Plugin settings.
			require_once( $this->dir . 'admin/class-settings.php' );

			// User management.
			require_once( $this->dir . 'admin/class-manage-users.php' );
			require_once( $this->dir . 'admin/class-user-edit.php'    );
			require_once( $this->dir . 'admin/class-user-new.php'     );

			// Edit posts.
			require_once( $this->dir . 'admin/class-meta-box-content-permissions.php' );

			// Role management.
			require_once( $this->dir . 'admin/class-manage-roles.php'          );
			require_once( $this->dir . 'admin/class-roles.php'                 );
			require_once( $this->dir . 'admin/class-role-edit.php'             );
			require_once( $this->dir . 'admin/class-role-new.php'              );
			require_once( $this->dir . 'admin/class-meta-box-publish-role.php' );
			require_once( $this->dir . 'admin/class-meta-box-custom-cap.php'   );

			// Edit capabilities tabs and groups.
			require_once( $this->dir . 'admin/class-cap-tabs.php'       );
			require_once( $this->dir . 'admin/class-cap-section.php'    );
			require_once( $this->dir . 'admin/class-cap-control.php'    );
		}
	}

	/**
	 * Sets up main plugin actions and filters.
	 *
	 * @since  1.0.0
	 * @access private
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

		load_plugin_textdomain( 'members', false, trailingslashit( dirname( plugin_basename( __FILE__ ) ) ) . 'lang' );
	}

	/**
	 * Method that runs only when the plugin is activated.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function activation() {

		// Check PHP version requirements.
		if ( version_compare( PHP_VERSION, $this->php_version, '<' ) ) {

			// Make sure the plugin is deactivated.
			deactivate_plugins( plugin_basename( __FILE__ ) );

			// Add an error message and die.
			wp_die( $this->get_min_php_message() );
		}

		// Get the administrator role.
		$role = get_role( 'administrator' );

		// If the administrator role exists, add required capabilities for the plugin.
		if ( ! empty( $role ) ) {

			$role->add_cap( 'restrict_content' ); // Edit per-post content permissions.
			$role->add_cap( 'list_roles'       ); // View roles in backend.

			// Do not allow administrators to edit, create, or delete roles
			// in a multisite setup. Super admins should assign these manually.
			if ( ! is_multisite() ) {
				$role->add_cap( 'create_roles' ); // Create new roles.
				$role->add_cap( 'delete_roles' ); // Delete existing roles.
				$role->add_cap( 'edit_roles'   ); // Edit existing roles/caps.
			}
		}
	}

	/**
	 * Returns a message noting the minimum version of PHP required.
	 *
	 * @since  2.0.1
	 * @access private
	 * @return void
	 */
	private function get_min_php_message() {

		return sprintf(
			__( 'Members requires PHP version %1$s. You are running version %2$s. Please upgrade and try again.', 'members' ),
			$this->php_version,
			PHP_VERSION
		);
	}

	/**
	 * Outputs the admin notice that the user needs to upgrade their PHP version. It also
	 * auto-deactivates the plugin.
	 *
	 * @since  2.0.1
	 * @access public
	 * @return void
	 */
	public function php_admin_notice() {

		// Output notice.
		printf(
			'<div class="notice notice-error is-dismissible"><p><strong>%s</strong></p></div>',
			esc_html( $this->get_min_php_message() )
		);

		// Make sure the plugin is deactivated.
		deactivate_plugins( plugin_basename( __FILE__ ) );
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
