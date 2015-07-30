<?php
/**
 * General functions file for the plugin.
 *
 * @package Members
 * @subpackage Functions
 */

/**
 * Validates a value as a boolean.  This way, strings such as "true" or "false" will be converted
 * to their correct boolean values.
 *
 * @since  1.0.0
 * @access public
 * @param  mixed   $val
 * @return bool
 */
function members_validate_boolean( $val ) {
	return filter_var( $val, FILTER_VALIDATE_BOOLEAN );
}

/**
 * Function for listing users like the WordPress function currently uses for authors.
 *
 * Eventually, I hope to remove this function in favor of wp_list_users():
 * @link http://core.trac.wordpress.org/ticket/15145
 *
 * @since 0.1.0
 * @uses get_users()
 */
function members_list_users( $args = array() ) {

	$output = '';
	$users = get_users( $args );

	if ( !empty( $users ) ) {

		$output .= '<ul class="xoxo members-list-users">';

		foreach ( $users as $user ) {

			$url = get_author_posts_url( $author->ID, $author->user_nicename );

			$class = esc_attr( "user-{$user->ID}" );
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
