<?php
/**
 * Functions that may be used across a variety of components, so they should always
 * be loaded for use on the front end of the site.
 *
 * @todo Decide whether Template Tags is a useful enough component to stand alone.
 * The Widgets and Shortcodes components both require the use of these functions here,
 * which should be template tags.
 *
 * @package Members
 */

/**
 * Displays the login form.
 *
 * @since 0.1.0
 * @deprecated 0.2.0 Use wp_login_form() instead.
 */
function members_login_form() {
	wp_login_form( array( 'echo' => true ) );
}

/**
 * Creates the login form.
 *
 * @since 0.1.0
 * @deprecated 0.2.0 Use wp_login_form() instead.
 */
function members_get_login_form( $args = array() ) {
	wp_login_form( array( echo => 'false' ) );
}

/**
 * Function for listing users like the WordPress function currently uses for authors.
 *
 * Eventually, I hope to remove this function in favor of wp_list_users():
 * @link http://core.trac.wordpress.org/ticket/15145
 *
 * @since 0.1
 * @uses get_users()
 */
function members_list_users( $args = array() ) {

	$output = '';
	$users = get_users( $args );

	if ( !empty( $users ) ) {

		$output .= '<ul class="xoxo members-list-users">';

		foreach ( $users as $user ) {

			$url = get_author_posts_url( $author->ID, $author->user_nicename );

			$class = "user-{$user->ID}";
			if ( is_author( $user->ID ) )
				$class .= ' current-user';

			$output .= "<li class='{$class}'><a href='{$url}' title='" . esc_attr( $user->display_name ) . "'>{$user->display_name}</a></li>\n";
		}

		$output .= '</ul>';
	}

	$output = apply_filters( 'members_list_users', $output );

	if ( empty( $args['echo'] ) )
		return $output;

	echo $output;
}

?>