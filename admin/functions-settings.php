<?php
/**
 * Handles settings functionality.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2018, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Register settings views.
add_action( 'members_register_settings_views', 'members_register_default_settings_views', 5 );

/**
 * Registers the plugin's built-in settings views.
 *
 * @since  2.0.0
 * @access public
 * @param  object  $manager
 * @return void
 */
function members_register_default_settings_views( $manager ) {

	// Bail if not on the settings screen.
	if ( 'members-settings' !== $manager->name )
		return;

	// Register general settings view (default view).
	$manager->register_view(
		new \Members\Admin\View_General(
			'general',
			array(
				'label'    => esc_html__( 'General', 'members' ),
				'priority' => 0
			)
		)
	);

	// Register add-ons view.
	$manager->register_view(
		new \Members\Admin\View_Addons(
			'add-ons',
			array(
				'label'    => esc_html__( 'Add-Ons', 'members' ),
				'priority' => 95
			)
		)
	);

	// Register add-ons view.
	$manager->register_view(
		new \Members\Admin\View_Donate(
			'donate',
			array(
				'label'    => esc_html__( 'Help Fund Version 3.0', 'members' ),
				'priority' => 100
			)
		)
	);
}

/**
 * Conditional function to check if on the plugin's settings page.
 *
 * @since  2.0.0
 * @access public
 * @return bool
 */
function members_is_settings_page() {

	$screen = get_current_screen();

	return is_object( $screen ) && 'settings_page_members-settings' === $screen->id;
}

/**
 * Returns the URL to the settings page.
 *
 * @since  2.0.0
 * @access public
 * @return string
 */
function members_get_settings_page_url() {

	return add_query_arg( array( 'page' => 'members-settings' ), admin_url( 'options-general.php' ) );
}

/**
 * Returns the URL to a settings view page.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $view
 * @return string
 */
function members_get_settings_view_url( $view ) {

	return add_query_arg( array( 'view' => sanitize_key( $view ) ), members_get_settings_page_url() );
}

/**
 * Returns the current settings view name.
 *
 * @since  2.0.0
 * @access public
 * @return string
 */
function members_get_current_settings_view() {

	if ( ! members_is_settings_page() )
		return '';

	return isset( $_GET['view'] ) ? sanitize_key( $_GET['view'] ) : 'general';
}

/**
 * Conditional function to check if on a specific settings view page.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $view
 * @return bool
 */
function members_is_settings_view( $view = '' ) {

	return members_is_settings_page() && $view === members_get_current_settings_view();
}
