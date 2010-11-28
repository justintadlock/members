<?php
/**
 * Plugin Name: Members
 * Plugin URI: http://justintadlock.com/archives/2009/09/17/members-wordpress-plugin
 * Description: A user, role, and content management plugin for controlling permissions and access. A plugin for making WordPress a more powerful <acronym title="Content Management System">CMS</acronym>.
 * Version: 0.2 Beta
 * Author: Justin Tadlock
 * Author URI: http://justintadlock.com
 *
 * The members plugin was created because the WordPress community is lacking a solid permissions 
 * plugin that is both open source and works completely within the confines of the APIs in WordPress.  
 * But, the plugin is so much more than just a plugin to control permissions.  It is meant to extend 
 * WordPress by making user, role, and content management as simple as using the system altogether.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package Members
 * @version 0.2.0
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2009 - 2010, Justin Tadlock
 * @link http://justintadlock.com/archives/2009/09/17/members-wordpress-plugin
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * @since 0.2.0
 */
class Members_Load {

	/**
	 * PHP4 constructor method.
	 *
	 * @since 0.2.0
	 */
	function Members_Load() {
		$this->__construct();
	}

	/**
	 * PHP5 constructor method.
	 *
	 * @since 0.2.0
	 */
	function __construct() {
		add_action( 'plugins_loaded', array( &$this, 'constants' ), 1 );
		add_action( 'plugins_loaded', array( &$this, 'locale' ), 2 );
		add_action( 'plugins_loaded', array( &$this, 'load' ), 3 );
		add_action( 'plugins_loaded', array( &$this, 'admin' ), 4 );
	}

	/**
	 * Defines constants used by the plugin.
	 *
	 * @since 0.2.0
	 */
	function constants() {

		/* Set constant path to the members plugin directory. */
		define( 'MEMBERS_DIR', plugin_dir_path( __FILE__ ) );

		/* Set constant path to the members plugin URL. */
		define( 'MEMBERS_URI', plugin_dir_url( __FILE__ ) );

		/* Set constant path to the members components directory. */
		define( 'MEMBERS_COMPONENTS', trailingslashit( MEMBERS_DIR ) . 'components' );
	}

	/**
	 * Loads the initial files needed by the plugin.
	 *
	 * @since 0.2.0
	 */
	function load() {
		if ( !is_admin() )
			require_once( MEMBERS_DIR . 'functions.php' );

		/* Load the components system, which is the file that has all the components-related functions. */
		require_once( MEMBERS_DIR . 'components.php' );

		/* Set up globals. */
		add_action( 'after_setup_theme', 'members_core_globals_setup', 0 );
	}

	/**
	 * Loads the translation files.
	 *
	 * @since 0.2.0
	 */
	function locale() {
		/* Load the translation of the plugin. */
		load_plugin_textdomain( 'members', false, 'members/languages' );
	}

	/**
	 * Loads the admin functions and files.
	 *
	 * @since 0.2.0
	 */
	function admin() {

		/* Load global functions for the WordPress admin. */
		if ( is_admin() ) {
			require_once( MEMBERS_DIR . 'functions-admin.php' );

			/* Members components settings page. */
			add_action( 'admin_menu', 'members_settings_page_init' );
			add_action( 'admin_init', 'members_register_settings' );
		}
	}
}

$members_load = new Members_Load();

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
	
	$members->settings_page = add_submenu_page( 'options-general.php', __('Members Components', 'members'), __('Members Components', 'members'), 'manage_options', 'members-components', 'members_settings_page' );
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