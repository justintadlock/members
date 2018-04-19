<?php
/**
 * Loads and enables the widgets for the plugin.
 *
 * @package    Members
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2018, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Hook widget registration to the 'widgets_init' hook.
add_action( 'widgets_init', 'members_register_widgets' );

/**
 * Registers widgets for the plugin.
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
function members_register_widgets() {

	// If the login form widget is enabled.
	if ( members_login_widget_enabled() ) {

		require_once( members_plugin()->dir . 'inc/class-widget-login.php' );

		register_widget( '\Members\Widget_Login' );
	}

	// If the users widget is enabled.
	if ( members_users_widget_enabled() ) {

		require_once( members_plugin()->dir . 'inc/class-widget-users.php' );

		register_widget( '\Members\Widget_Users' );
	}
}
