<?php
/**
 * Shortcodes for use within posts and other shortcode-aware areas.
 *
 * @package Members
 * @subpackage Functions
 */

/* Add shortcodes. */
add_action( 'init', 'members_register_shortcodes' );

/**
 * Registers shortcodes.
 *
 * @since 0.2.0
 */
function members_register_shortcodes() {

	/* Add the [login-form] shortcode. */
	add_shortcode( 'login-form', 'members_login_form_shortcode' );

	/* Add the [access] shortcode. */
	add_shortcode( 'access', 'members_access_check_shortcode' );

	/* Add the [feed] shortcode. */
	add_shortcode( 'feed', 'members_access_check_shortcode' );

	/* Add the [is_user_logged_in] shortcode. */
	add_shortcode( 'is_user_logged_in', 'members_is_user_logged_in_shortcode' );

	/* @deprecated 0.2.0. */
	add_shortcode( 'get_avatar', 'members_get_avatar_shortcode' );
	add_shortcode( 'avatar', 'members_get_avatar_shortcode' );
	/* === */
}

/**
 * Displays content if the user viewing it is currently logged in. This also blocks content from showing 
 * in feeds.
 *
 * @since 0.1.0
 * @param $attr array Attributes for the shortcode (not used).
 * @param $content string The content located between the opening and closing of the shortcode.
 * @return $content string The content to be shown.
 */
function members_is_user_logged_in_shortcode( $attr, $content = null ) {

	/* If it is a feed or the user is not logged in, return nothing. */
	if ( is_feed() || !is_user_logged_in() || is_null( $content ) )
		return '';

	/* Return the content. */
	return do_shortcode( $content );
}

/**
 * Content that should only be shown in feed readers.  Can be useful for displaying feed-specific items.
 *
 * @since 0.1.0
 * @param $attr array Attributes for the shortcode (not used).
 * @param $content string The content located between the opening and closing of the shortcode.
 * @return $content string The content to be shown.
 */
function members_feed_shortcode( $attr, $content = null ) {

	/* If not feed or no content exists, return nothing. */
	if ( !is_feed() || is_null( $content ) )
		return '';

	/* Return the content. */
	return do_shortcode( $content );
}

/**
 * Provide/restrict access to specific roles or capabilities. This content should not be shown in feeds.  Note that 
 * capabilities are checked first.  If a capability matches, any roles added will *not* be checked.  Users should 
 * choose between using either capabilities or roles for the check rather than both.  The best option is to always 
 * use a capability
 *
 * @since 0.1.0
 * @param $attr array The shortcode attributes.
 * @param $content string The content that should be shown/restricted.
 * @return $content string The content if it should be shown.  Else, return nothing.
 */
function members_access_check_shortcode( $attr, $content = null ) {

	/* Set up the default attributes. */
	$defaults = array(
		'capability' => '',	// Single capability or comma-separated multiple capabilities
		'role' => '',	// Single role or comma-separated multiple roles
	);

	/* Merge the input attributes and the defaults. */
	extract( shortcode_atts( $defaults, $attr ) );

	/* If there's no content or if viewing a feed, return an empty string. */
	if ( is_null( $content ) || is_feed() )
		return '';

	/* If the current user has the capability, show the content. */
	if ( !empty( $capability ) ) {

		/* Get the capabilities. */
		$caps = explode( ',', $capability );

		/* Loop through each capability. */
		foreach ( $caps as $cap ) {

			/* If the current user can perform the capability, return the content. */
			if ( current_user_can( trim( $cap ) ) )
				return do_shortcode( $content );
		}
	}

	/* If the current user has the role, show the content. */
	if ( !empty( $role ) ) {

		/* Get the roles. */
		$roles = explode( ',', $role );

		/* Loop through each of the roles. */
		foreach ( $roles as $role ) {

			/* If the current user has the role, return the content. */
			if ( current_user_can( trim( $role ) ) )
				return do_shortcode( $content );
		}
	}

	/* Return an empty string if we've made it to this point. */
	return '';
}

/**
 * Displays a login form.
 *
 * @since 0.1.0
 * @uses wp_login_form() Displays the login form.
 */
function members_login_form_shortcode() {
	return wp_login_form( array( 'echo' => false ) );
}

?>