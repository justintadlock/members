<?php
/**
 * Plugin Name: Members
 * Plugin URI: http://justintadlock.com/archives/2009/09/17/members-wordpress-plugin
 * Description: A user, role, and content management plugin for controlling permissions and access. A plugin for making WordPress a more powerful <acronym title="Content Management System">CMS</acronym>.
 * Version: 0.1.1
 * Author: Justin Tadlock
 * Author URI: http://justintadlock.com
 *
 * The members plugin was created because the WordPress community is lacking
 * a solid permissions plugin that is both open source and works completely within the 
 * confines of the APIs in WordPress.  But, the plugin is so much more than just a
 * plugin to control permissions.  It is meant to extend WordPress by making user, 
 * role, and content management as simple as using the system altogether.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package Members
 */

/* Set constant path to the members plugin directory. */
define( 'MEMBERS_DIR', plugin_dir_path( __FILE__ ) );

/* Set constant path to the members plugin URL. */
define( 'MEMBERS_URL', plugin_dir_url( __FILE__ ) );

/* Set constant path to the members components directory. */
define( 'MEMBERS_COMPONENTS', MEMBERS_DIR . 'components' );

/* Launch the plugin. */
add_action( 'plugins_loaded', 'members_plugin_init' );

/**
 * Initialize the plugin.  This function loads the required files needed for the plugin
 * to run in the proper order.  Mostly, it's the function that brings the components
 * system into play.
 *
 * @since 0.1
 */
function members_plugin_init() {
	
	/* Load the translation of the plugin. */
	load_plugin_textdomain( 'members', false, 'members/languages' );

	/* Load global functions for the WordPress admin. */
	if ( is_admin() )
		require_once( MEMBERS_DIR . 'functions-admin.php' );

	/* Load global functions for the front end of the site. */
	else
		require_once( MEMBERS_DIR . 'functions.php' );

	/* Load the components system, which is the file that has all the components-related functions. */
	require_once( MEMBERS_DIR . 'components.php' );

	/* Members components settings page. */
	add_action( 'admin_menu', 'members_settings_page_init' );
	add_action( 'admin_init', 'members_register_settings' );

	/* Set up globals. */
	add_action( 'init', 'members_core_globals_setup', 0 );

	/* Available action hook if needed by another plugin/theme to run additional functions. */
	do_action( 'members_init' );
}

/**
 * Set up the $members global variable. Since we'll need to have several
 * different variables, it just makes sense to put them all into one place.
 * Other functions will hook onto this (e.g., $members-registered_components).
 * 
 * @since 0.1
 * @global $members object The global members object.
 * @global $current_user object The currently logged-in user.
 */
function members_core_globals_setup() {
	global $members, $current_user;

	/* Get the currently logged-in user. */
	$current_user = wp_get_current_user();

	/* Add the currently logged-in user to our global object. */
	$members->current_user = $current_user;
	
	/* Get all active components */
	$members->active_components = get_option( 'members_settings' );
}

/**
 * Creates the members settings/components page.
 *
 * @since 0.1
 * @uses add_submenu_page() Creates a submenu for the Settings menu item.
 */
function members_settings_page_init() {
	global $members;
	
	$members->settings_page = add_submenu_page( 'options-general.php', __('Members Components', 'members'), __('Members Components', 'members'), 'activate_plugins', 'members-components', 'members_settings_page' );
}

/**
 * Registers the plugin settings, which will become an array of activated components.
 *
 * @since 0.1
 * @uses register_setting() Function for registering a setting in WordPress.
 */
function members_register_settings() {
	register_setting( 'members_plugin_settings', 'members_settings', 'members_settings_validate' );
}

/**
 * Loads the admin screen page for selecting components for use with the plugin.
 *
 * @since 0.1
 */
function members_settings_page() {	
	require_once( MEMBERS_DIR . 'settings.php' );
}

/**
 * Validates the members settings.  Since the settings is just a list of components,
 * all we need to do here is loop through the array and check for true/false.
 *
 * @since 0.1
 * @param $input array Values sent by the settings page.
 * @return $input array Validated values to return.
 */
function members_settings_validate( $input ) {
	if ( !is_array( $input ) )
		return $input;
	
	foreach ( $input as $key => $value ) {

		/* Disable old edit_roles and new_roles components. */
		if ( 'edit_roles' == $input[$key] || 'new_roles' == $input[$key] )
			$input[$key] = false;
		else
			$input[$key] = ( $input[$key] == 1 ? 1 : 0 );
	}

	return $input;
}

?>