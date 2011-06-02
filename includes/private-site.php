<?php
/**
 * Handles the private site and private feed features of the plugin.  If private site is selected in the plugin settings,
 * the plugin will redirect all non-logged-in users to the login page.  If private feed is selected, all content is blocked 
 * from feeds from the site.
 *
 * @package Members
 * @subpackage Functions
 */

/* Redirects users to the login page. */
add_action( 'template_redirect', 'members_please_log_in', 1 );

/* Disable content in feeds if the feed should be private. */
add_filter( 'the_content_feed', 'members_private_feed' );
add_filter( 'the_excerpt_rss', 'members_private_feed' );
add_filter( 'comment_text_rss', 'members_private_feed' );

/**
 * Redirects users that are not logged in to the 'wp-login.php' page.
 *
 * @since 0.1.0
 * @uses is_user_logged_in() Checks if the current user is logged in.
 * @uses auth_redirect() Redirects people that are not logged in to the login page.
 */
function members_please_log_in() {

	/* Check if the private blog feature is active. */
	if ( members_get_setting( 'private_blog' ) ) {

		/* If using BuddyPress and on the register page, don't do anything. */
		if ( function_exists( 'bp_is_current_component' ) && bp_is_current_component( 'register' ) )
			return;

		/* Else, if the user is not logged in, redirect to the login page. */
		elseif ( !is_user_logged_in() )
			auth_redirect();
	}
}

/**
 * Blocks feed items if the user has selected the private feed feature.
 *
 * @since 0.2.0
 * @param string $content The post or comment feed content.
 * @return string $content Returns either the content or an error message.
 */
function members_private_feed( $content ) {

	if ( members_get_setting( 'private_feed' ) )
		$content = members_get_setting( 'private_feed_error' );

	return $content;
}

?>