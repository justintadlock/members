<?php
/**
 * Plugin Name: Members
 * Plugin URI: http://themehybrid.com/plugins/members
 * Description: A user, role, and content management plugin for controlling permissions and access. A plugin for making WordPress a more powerful <acronym title="Content Management System">CMS</acronym>.
 * Version: 0.2.5
 * Author: Justin Tadlock
 * Author URI: http://justintadlock.com
 *
 * The members plugin was created because the WordPress community is lacking a solid permissions
 * plugin that is both open source and works completely within the confines of the APIs in WordPress.
 * But, the plugin is so much more than just a plugin to control permissions.  It is meant to extend
 * WordPress by making user, role, and content management as simple as using WordPress itself.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package Members
 * @version 0.2.5
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2009 - 2015, Justin Tadlock
 * @link http://justintadlock.com/archives/2009/09/17/members-wordpress-plugin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
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
		return __( 'Members', 'members' );
	}

	/**
	 * Magic method to keep the object from being cloned.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Whoah, partner!', 'members' ), '1.0.0' );
	}

	/**
	 * Magic method to keep the object from being unserialized.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Whoah, partner!', 'members' ), '1.0.0' );
	}

	/**
	 * Magic method to prevent a fatal error when calling a method that doesn't exist.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return null
	 */
	public function __call( $method = '', $args = array() ) {
		_doing_it_wrong( "Members_Plugin::{$method}", __( 'Method does not exist.', 'members' ), '1.0.0' );
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

		$this->dir_path = trailingslashit( plugin_dir_path( __FILE__ ) );
		$this->dir_uri  = trailingslashit( plugin_dir_url(  __FILE__ ) );

		$this->inc_dir   = trailingslashit( $this->dir_path . 'includes' );
		$this->admin_dir = trailingslashit( $this->dir_path . 'admin'    );

		$this->css_uri = trailingslashit( $this->dir_uri . 'css' );
		$this->js_uri  = trailingslashit( $this->dir_uri . 'js'  );

		/* === Deprecated === */

		global $members;

		/* Set up an empty class for the global $members object. */
		$members = new stdClass;

		/* Set the version number of the plugin. */
		define( 'MEMBERS_VERSION', '0.2.5' );

		/* Set the database version number of the plugin. */
		define( 'MEMBERS_DB_VERSION', 2 );

		/* Set constant path to the members plugin directory. */
		define( 'MEMBERS_DIR',      $this->dir_path  );
		define( 'MEMBERS_URI',      $this->dir_uri   );
		define( 'MEMBERS_INCLUDES', $this->inc_dir   );
		define( 'MEMBERS_ADMIN',    $this->admin_dir );
	}

	/**
	 * Loads files needed by the plugin.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	private function includes() {

		// Load includes files.
		require_once( $this->inc_dir . 'functions.php'           );
		require_once( $this->inc_dir . 'update.php'              );
		require_once( $this->inc_dir . 'deprecated.php'          );
		require_once( $this->inc_dir . 'admin-bar.php'           );
		require_once( $this->inc_dir . 'capabilities.php'        );
		require_once( $this->inc_dir . 'content-permissions.php' );
		require_once( $this->inc_dir . 'private-site.php'        );
		require_once( $this->inc_dir . 'roles.php'               );
		require_once( $this->inc_dir . 'shortcodes.php'          );
		require_once( $this->inc_dir . 'template.php'            );
		require_once( $this->inc_dir . 'widgets.php'             );
		require_once( $this->inc_dir . 'class-role.php'             );
		require_once( $this->inc_dir . 'class-role-factory.php'             );

		// Load admin files.
		if ( is_admin() ) {
			require_once( $this->admin_dir . 'admin.php'          );
			require_once( $this->admin_dir . 'class-settings.php' );
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

		/* Load the translation of the plugin. */
		load_plugin_textdomain( 'members', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Method that runs only when the plugin is activated.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function activation() {

		/* Get the administrator role. */
		$role = get_role( 'administrator' );

		/* If the administrator role exists, add required capabilities for the plugin. */
		if ( !empty( $role ) ) {

			/* Role management capabilities. */
			$role->add_cap( 'list_roles' );
			$role->add_cap( 'create_roles' );
			$role->add_cap( 'delete_roles' );
			$role->add_cap( 'edit_roles' );

			/* Content permissions capabilities. */
			$role->add_cap( 'restrict_content' );
		}

		/**
		 * If the administrator role does not exist for some reason, we have a bit of a problem
		 * because this is a role management plugin and requires that someone actually be able to
		 * manage roles.  So, we're going to create a custom role here.  The site administrator can
		 * assign this custom role to any user they wish to work around this problem.  We're only
		 * doing this for single-site installs of WordPress.  The 'super admin' has permission to do
		 * pretty much anything on a multisite install.
		 */
		elseif ( empty( $role ) && !is_multisite() ) {

			/* Add the 'members_role_manager' role with limited capabilities. */
			add_role(
				'members_role_manager',
				_x( 'Role Manager', 'role', 'members' ),
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
 * Gets the instance of the Members_Plugin class.  This function is useful for quickly grabbing data
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
