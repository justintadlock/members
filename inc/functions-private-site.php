<?php
/**
 * Handles the private site and private feed features of the plugin.  If private site is
 * selected in the plugin settings, the plugin will redirect all non-logged-in users to the
 * login page.  If private feed is selected, all content is blocked from feeds from the site.
 *
 * @package    Members
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2016, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Redirects users to the login page.
add_action( 'template_redirect', 'members_please_log_in', 0 );

# Disable content in feeds if the feed should be private.
add_filter( 'the_content_feed', 'members_private_feed', 95 );
add_filter( 'the_excerpt_rss',  'members_private_feed', 95 );
add_filter( 'comment_text_rss', 'members_private_feed', 95 );

# Filters for the feed error message.
add_filter( 'members_feed_error_message', array( $GLOBALS['wp_embed'], 'run_shortcode' ),   5 );
add_filter( 'members_feed_error_message', array( $GLOBALS['wp_embed'], 'autoembed'     ),   5 );
add_filter( 'members_feed_error_message',                              'wptexturize',       10 );
add_filter( 'members_feed_error_message',                              'convert_smilies',   15 );
add_filter( 'members_feed_error_message',                              'convert_chars',     20 );
add_filter( 'members_feed_error_message',                              'wpautop',           25 );
add_filter( 'members_feed_error_message',                              'do_shortcode',      30 );
add_filter( 'members_feed_error_message',                              'shortcode_unautop', 35 );

/**
 * Conditional tag to see if we have a private blog.
 *
 * @since  1.0.0
 * @access public
 * @return bool
 */
function members_is_private_blog() {
	return members_get_setting( 'private_blog' );
}

/**
 * Conditional tag to see if we have a private feed.
 *
 * @since  1.0.0
 * @access public
 * @return bool
 */
function members_is_private_feed() {
	return members_get_setting( 'private_feed' );
}

/**
 * Redirects users that are not logged in to the 'wp-login.php' page.
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function members_please_log_in() {

	// Check if the private blog feature is active and if the user is not logged in.
	if ( members_is_private_blog() && ! is_user_logged_in() ) {

		// If using BuddyPress and on the register page, don't do anything.
		if ( function_exists( 'bp_is_current_component' ) && bp_is_current_component( 'register' ) )
			return;

		// Redirect to the login page.
		auth_redirect();
		exit;
	}
}

/**
 * Blocks feed items if the user has selected the private feed feature.
 *
 * @since  0.2.0
 * @access public
 * @param  string  $content
 * @return string
 */
function members_private_feed( $content ) {

	return members_is_private_feed() ? members_get_private_feed_message() : $content;
}

/**
 * Returns the private feed error message.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function members_get_private_feed_message() {

	return apply_filters( 'members_feed_error_message', members_get_setting( 'private_feed_error' ) );
}
