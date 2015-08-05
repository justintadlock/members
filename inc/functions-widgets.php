<?php
/**
 * Loads and enables the widgets for the plugin.
 *
 * @package Members
 * @subpackage Functions
 */

/* Hook widget registration to the 'widgets_init' hook. */
add_action( 'widgets_init', 'members_register_widgets' );

/**
 * Registers widgets for the plugin.
 *
 * @since 0.2.0
 */
function members_register_widgets() {

	/* If the login form widget is enabled. */
	if ( members_get_setting( 'login_form_widget' ) ) {

		/* Load the login form widget file. */
		require_once( members_plugin()->inc_dir . 'class-widget-login.php' );

		/* Register the login form widget. */
		register_widget( 'Members_Widget_Login' );
	}

	/* If the users widget is enabled. */
	if ( members_get_setting( 'users_widget' ) ) {

		/* Load the users widget file. */
		require_once( members_plugin()->inc_dir . 'class-widget-users.php' );

		/* Register the users widget. */
		register_widget( 'Members_Widget_users' );
	}
}
