<?php
/**
 * Shortcodes for use within posts and other shortcode-aware areas.
 *
 * @package    Members
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Add shortcodes.
add_action( 'init', 'members_register_shortcodes' );

/**
 * Registers shortcodes.
 *
 * @since  0.2.0
 * @access public
 * @return void
 */
function members_register_shortcodes() {

	// Add the `[members_login_form]` shortcode.
	add_shortcode( 'members_login_form', 'members_login_form_shortcode' );
	add_shortcode( 'login-form',         'members_login_form_shortcode' ); // @deprecated 1.0.0

	// Add the `[members_access]` shortcode.
	add_shortcode( 'members_access', 'members_access_check_shortcode' );
	add_shortcode( 'access',         'members_access_check_shortcode' ); // @deprecated 1.0.0

	// Add the `[members_feed]` shortcode.
	add_shortcode( 'members_feed', 'members_feed_shortcode' );
	add_shortcode( 'feed',         'members_feed_shortcode' ); // @deprecated 1.0.0

	// Add the `[members_logged_in]` shortcode.
	add_shortcode( 'members_logged_in', 'members_is_user_logged_in_shortcode' );
	add_shortcode( 'is_user_logged_in', 'members_is_user_logged_in_shortcode' ); // @deprecated 1.0.0

	// Add the `[members_not_logged_in]` shortcode.
	add_shortcode( 'members_not_logged_in', 'members_not_logged_in_shortcode' );

	// @deprecated 0.2.0.
	add_shortcode( 'get_avatar', 'members_get_avatar_shortcode' );
	add_shortcode( 'avatar',     'members_get_avatar_shortcode' );
}

/**
 * Displays content if the user viewing it is currently logged in. This also blocks content
 * from showing in feeds.
 *
 * @since  0.1.0
 * @access public
 * @param  array   $attr
 * @param  string  $content
 * @return string
 */
function members_is_user_logged_in_shortcode( $attr, $content = null ) {

	return is_feed() || ! is_user_logged_in() || is_null( $content ) ? '' : do_shortcode( $content );
}

/**
 * Displays content if the user viewing it is not currently logged in.
 *
 * @since  2.0.0
 * @access public
 * @param  array   $attr
 * @param  string  $content
 * @return string
 */
function members_not_logged_in_shortcode( $attr, $content = null ) {

	return is_user_logged_in() || is_null( $content ) ? '' : do_shortcode( $content );
}

/**
 * Content that should only be shown in feed readers.  Can be useful for displaying
 * feed-specific items.
 *
 * @since  0.1.0
 * @access public
 * @param  array   $attr
 * @param  string  $content
 * @return string
 */
function members_feed_shortcode( $attr, $content = null ) {

	return ! is_feed() || is_null( $content ) ? '' : do_shortcode( $content );
}

/**
 * Provide/restrict access to specific roles or capabilities. This content should not be shown
 * in feeds.  Note that capabilities are checked first.  If a capability matches, any roles
 * added will *not* be checked.  Users should choose between using either capabilities or roles
 * for the check rather than both.  The best option is to always use a capability.
 *
 * @since  0.1.0
 * @access public
 * @param  array   $attr
 * @param  string  $content
 * @return string
 */
function members_access_check_shortcode( $attr, $content = null ) {

	// If there's no content or if viewing a feed, return an empty string.
	if ( is_null( $content ) || is_feed() )
		return '';

	$user_can = false;

	// Set up the default attributes.
	$defaults = array(
		'capability' => '',  // Single capability or comma-separated multiple capabilities.
		'role'       => '',  // Single role or comma-separated multiple roles.
		'operator'   => 'or' // Only the `!` operator is supported for now.  Everything else falls back to `or`.
	);

	// Merge the input attributes and the defaults.
	$attr = shortcode_atts( $defaults, $attr, 'members_access' );

	// Get the operator.
	$operator = strtolower( $attr['operator'] );

	// If the current user has the capability, show the content.
	if ( $attr['capability'] ) {

		// Get the capabilities.
		$caps = explode( ',', $attr['capability'] );

		if ( '!' === $operator )
			return members_current_user_can_any( $caps ) ? '' : do_shortcode( $content );

		return members_current_user_can_any( $caps ) ? do_shortcode( $content ) : '';
	}

	// If the current user has the role, show the content.
	if ( $attr['role'] ) {

		// Get the roles.
		$roles = explode( ',', $attr['role'] );

		if ( '!' === $operator )
			return members_current_user_has_role( $roles ) ? '' : do_shortcode( $content );

		return members_current_user_has_role( $roles ) ? do_shortcode( $content ) : '';
	}

	// Return an empty string if we've made it to this point.
	return '';
}

/**
 * Displays a login form.
 *
 * @since  0.1.0
 * @access public
 * @return string
 */
function members_login_form_shortcode() {

	return wp_login_form( array( 'echo' => false ) );
}
