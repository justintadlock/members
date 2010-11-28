<?php
/**
 * The Shortcodes component provides additional [shortcodes] for use within posts/pages
 * and any other shortcode-capable area.
 *
 * @todo need a [hide] shortcode or allow [access] to do the opposite.
 *
 * @package Members
 * @subpackage Components
 */

/* Add shortcodes. */
add_action( 'init', 'members_component_shortcodes_register_shortcodes' );

/**
 * Registers shortcodes for the shortcodes component.
 *
 * @since 0.2.0
 */
function members_component_shortcodes_register_shortcodes() {
	add_shortcode( 'login-form', 'members_login_form_shortcode' );
	add_shortcode( 'access', 'members_access_check_shortcode' );
	add_shortcode( 'feed', 'members_access_check_shortcode' );
	add_shortcode( 'is_user_logged_in', 'members_is_user_logged_in_shortcode' );
	add_shortcode( 'get_avatar', 'members_get_avatar_shortcode' );
	add_shortcode( 'avatar', 'members_get_avatar_shortcode' );
}

/**
 * Displays an avatar for any user.  At the very least, an ID or email must
 * be input.  Otherwise, we can't know which avatar to grab.
 *
 * Users should input the code as [get_avatar id="30" alt="Justin Tadlock"].
 *
 * @since 0.1
 * @uses get_avatar() Grabs the users avatar.
 * @param $attr array The shortcode attributes.
 */
function members_get_avatar_shortcode( $attr ) {

	/* Set up our default attributes. */
	$defaults = array(
		'id' => '',
		'email' => '',
		'size' => 96,
		'default' => '',
		'alt' => ''
	);

	/* Merge the input attributes and the defaults. */
	extract( shortcode_atts( $defaults, $attr ) );

	/* If an email was input, use it. */
	if ( $email )
		$id_or_email = $email;

	/* If no email was input, use the ID. */
	else
		$id_or_email = $id;

	/* Return the avatar. */
	return get_avatar( $id_or_email, $size, $default, $alt );
}

/**
 * Displays content if the user viewing it is currently logged in. This also blocks
 * content from showing in feeds.
 *
 * Content needs to be wrapped with this shortcode like 
 * [is_user_logged_in]This is content.[/is_user_logged_in].
 *
 * @todo Provide a filter hook for displaying a "please log in to view" message.
 *
 * @since 0.1
 * @uses is_feed() Checks if the content is currently being shown in a feed.
 * @uses is_user_logged_in() Checks if the current user is logged in.
 * @param $attr array Attributes for the shortcode (not usefule here).
 * @param $content string The content located between the opening and closing of the shortcode.
 * @return $content string The content to be shown.
 */
function members_is_user_logged_in_shortcode( $attr, $content = null ) {

	/* If it is a feed or the user is not logged in, return nothing. */
	if ( !is_feed() || !is_user_logged_in() )
		return '';

	/* Return the content. */
	return do_shortcode( $content );
}

/**
 * Content that should only be shown in feed readers.  Can be useful for
 * displaying feed-specific items.
 *
 * Content should be wrapped like [feed]This is content.[/feed].
 *
 * @since 0.1
 * @uses is_feed() Checks if the content is currently being shown in a feed.
 * @uses is_null() Checks if there is any content.
 * @param $attr array Attributes for the shortcode (not currently useful but may later add a $display/$show parameter).
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
 * Provide/restrict access to specific roles or capabilities. This content should
 * not be shown in feeds.
 *
 * @todo Allow multiple roles and capabilities to be input (comma-separated).
 *
 * Content should be wrapped like [access role="editor"]This is content.[/access].
 *
 * @since 0.1
 * @uses current_user_can() Checks if the current user has the role or capability.
 * @uses is_feed() Checks if we're currently viewing a feed.
 * @param $attr array The shortcode attributes.
 * @param $content string The content that should be shown/restricted.
 * @return $content string The content if it should be shown.  Else, return nothing.
 */
function members_access_check_shortcode( $attr, $content = null ) {

	/* Set up the default attributes. */
	$defaults = array(
		'capability' => '',
		'role' => '',
		'feed' => false,
	);

	/* Merge the input attributes and the defaults. */
	extract( shortcode_atts( $defaults, $attr ) );

	/* If the current user has the input capability, show the content. */
	if ( $capability && current_user_can( $capability ) )
		return do_shortcode( $content );

	/* If the current user has the input role, show the content. */
	elseif ( $role && current_user_can( $role ) )
		return do_shortcode( $content );

	/* If $feed was set to true and we're currently displaying a feed, show the content. */
	elseif ( $feed && 'false' !== $feed && is_feed() )
		return do_shortcode( $content );

	/* If there is no content, return nothing. */
	elseif ( !is_null( $content ) )
		return '';

	/* Return nothing if none of the conditions have been met. */
	return '';
}

/**
 * Displays a login form.
 *
 * @since 0.1
 * @uses wp_login_form() Displays the login form.
 */
function members_login_form_shortcode() {
	return wp_login_form( array( 'echo' => false ) );
}

?>