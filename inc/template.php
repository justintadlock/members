<?php
/**
 * Template-related functions for theme authors.
 *
 * @package    Members
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2016, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Conditional tag to check if a user can view a specific post.  A user cannot view a post if their
 * user role has not been selected in the 'Content Permissions' meta box on the edit post screen in
 * the admin.  Non-logged in site visitors cannot view posts if roles were selected.  If no roles
 * were selected, all users and site visitors can view the content.
 *
 * There are exceptions to this rule though.  The post author, any user with the `restrict_content`
 * capability, and users that have the ability to edit the post can always view the post, even if
 * their role was not granted permission to view it.
 *
 * @since  0.2.0
 * @access public
 * @param  int     $user_id
 * @param  int     $post_id
 * @return bool
 */
function members_can_user_view_post( $user_id, $post_id = '' ) {

	// If no post ID is given, assume we're in The Loop and get the current post's ID.
	if ( ! $post_id )
		$post_id = get_the_ID();

	// Assume the user can view the post at this point. */
	$can_view = true;

	// The plugin is only going to handle permissions if the 'content permissions' feature
	// is active.  If not active, the user can always view the post.  However, developers
	// can roll their own handling of this and filter `members_can_user_view_post`.
	if ( members_content_permissions_enabled() ) {

		// Get the roles selected by the user.
		$roles = members_get_post_roles( $post_id );

		// Check if there are any old roles with the '_role' meta key.
		if ( empty( $roles ) )
			$roles = members_convert_old_post_meta( $post_id );

		// If we have an array of roles, let's get to work.
		if ( ! empty( $roles ) && is_array( $roles ) ) {

			// Since specific roles were given, let's assume the user can't view
			// the post at this point.  The rest of this functionality should try
			// to disprove this.
			$can_view = false;

			// Get the post object.
			$post = get_post( $post_id );

			// Get the post type object.
			$post_type = get_post_type_object( $post->post_type );

			// If viewing a feed or if the user's not logged in, assume it's blocked at this point.
			if ( is_feed() || ! is_user_logged_in() ) {
				$can_view = false;
			}

			// If the post author, the current user can edit the post, or the current user can 'restrict_content', return true.
			elseif ( $post->post_author == $user_id || user_can( $user_id, 'restrict_content' ) || user_can( $user_id, $post_type->cap->edit_post, $post_id ) ) {
				$can_view = true;
			}

			// Else, let's check the user's role against the selected roles.
			else {

				// Loop through each role and set $can_view to true if the user has one of the roles.
				foreach ( $roles as $role ) {

					if ( members_user_has_role( $user_id, $role ) ) {
						$can_view = true;
						break;
					}
				}
			}
		}
	}

	// Set the check for the parent post based on whether we have permissions for this post.
	$check_parent = empty( $roles ) && $can_view;

	// Set to `FALSE` to avoid hierarchical checking.
	if ( apply_filters( 'members_check_parent_post_permission', $check_parent, $post_id, $user_id ) ) {

		$parent_id = get_post( $post_id )->post_parent;

		// If the post has a parent, check if the user has permission to view it.
		if ( 0 < $parent_id )
			$can_view = members_can_user_view_post( $user_id, $parent_id );
	}

	// Allow developers to overwrite the final return value.
	return apply_filters( 'members_can_user_view_post', $can_view, $user_id, $post_id );
}

/**
 * Wrapper function for the members_can_user_view_post() function. This function checks if the
 * currently logged-in user can view the content of a specific post.
 *
 * @since  0.2.0
 * @access public
 * @param  int    $post_id
 * @return bool
 */
function members_can_current_user_view_post( $post_id = '' ) {

	return members_can_user_view_post( get_current_user_id(), $post_id );
}

/**
 * Function for listing users like the WordPress function currently uses for authors.
 *
 * @link   http://core.trac.wordpress.org/ticket/15145
 * @since  0.1.0
 * @access public
 * @param  array  $args
 * @return string
 */
function members_list_users( $args = array() ) {

	$output = '';
	$users  = get_users( $args );

	if ( ! empty( $users ) ) {

		foreach ( $users as $user ) {

			$url = get_author_posts_url( $user->ID, $user->user_nicename );

			$class = sanitize_html_class( "user-{$user->ID}" );

			if ( is_author( $user->ID ) )
				$class .= ' current-user';

			$output .= sprintf( '<li class="%s"><a href="%s">%s</a></li>', esc_attr( $class ), esc_url( $url ), esc_html( $user->display_name ) );
		}

		$output = sprintf( '<ul class="xoxo members-list-users">%s</ul>', $output );
	}

	$output = apply_filters( 'members_list_users', $output );

	if ( empty( $args['echo'] ) )
		return $output;

	echo $output;
}
