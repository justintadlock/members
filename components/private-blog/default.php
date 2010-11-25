<?php
/**
 * The Private Blog component is for making your site completely private to people that are 
 * not logged into the site.  If not logged in, it will redirect all people to the 'wp-login.php' page.
 *
 * @todo Make sure 'blog_public' is set to true.
 * @todo Disable content from feeds or add an additional feed component.
 *
 * @package Members
 * @subpackage Components
 */

/* Redirects users to the login page. */
add_action( 'template_redirect', 'members_please_log_in' );

/**
 * Redirects users that are not logged in to the 'wp-login.php' page.
 *
 * @since 0.1
 * @uses is_user_logged_in() Checks if the current user is logged in.
 * @uses auth_redirect() Redirects people that are not logged in to the login page.
 */
function members_please_log_in() {
	if ( !is_user_logged_in() && !strpos( $_SERVER['SCRIPT_NAME'], 'wp-login.php' ) )
		auth_redirect();
}

?>