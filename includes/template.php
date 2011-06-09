<?php
/**
 * @package Members
 * @subpackage Functions
 */

/**
 * Conditional tag to check if a user can view a specific post.  A user cannot view a post if their user role has 
 * not been selected in the 'Content Permissions' meta box on the edit post screen in the admin.  Non-logged in 
 * site visitors cannot view posts if roles were seletected.  If no roles were selected, all users and site visitors 
 * can view the content.
 *
 * There are exceptions to this rule though.  The post author, any user with the 'restrict_content' capability, 
 * and users that have the ability to edit the post can always view the post, even if their role was not granted 
 * permission to view it.
 *
 * @todo See how feasible it is to just use the normal user_can() WordPress function to check against a meta 
 * capability such as 'members_view_post' while hooking into 'map_meta_cap' or 'user_has_cap' to roll custom 
 * plugin handling for this. This would just be a wrapper tag.
 *
 * @since 0.2.0
 * @param int $user_id The ID of the user to check.
 * @param int $post_id The ID of the post to check.
 * @return bool True if the user can view the post. False if the user cannot view the post.
 */
function members_can_user_view_post( $user_id, $post_id = '' ) {

	/* If no post ID is given, assume we're in The Loop and get the current post's ID. */
	if ( empty( $post_id ) )
		$post_id = get_the_ID();

	/* Assume the user can view the post at this point. */
	$can_view = true;

	/**
	 * The plugin is only going to handle permissions if the 'content permissions' feature is active.  If 
	 * not active, the user can always view the post.  However, developers can roll their own handling of
	 * this and filter 'members_can_user_view_post'.
	 */
	if ( members_get_setting( 'content_permissions' ) ) {

		/* Get the roles selected by the user. */
		$roles = get_post_meta( $post_id, '_members_access_role', false );

		/* Check if there are any old roles with the '_role' meta key. */
		if ( empty( $roles ) )
			$roles = members_convert_old_post_meta( $post_id );

		/* If we have an array of roles, let's get to work. */
		if ( !empty( $roles ) && is_array( $roles ) ) {

			/**
			 * Since specific roles were given, let's assume the user can't view the post at 
			 * this point.  The rest of this functionality should try to disprove this.
			 */
			$can_view = false;

			/* Get the post object. */
			$post = get_post( $post_id );

			/* Get the post type object. */
			$post_type = get_post_type_object( $post->post_type );

			/* If viewing a feed or if the user's not logged in, assume it's blocked at this point. */
			if ( is_feed() || !is_user_logged_in() ) {
				$can_view = false;
			}

			/* If the post author, the current user can edit the post, or the current user can 'restrict_content', return true. */
			elseif ( $post->post_author == $user_id || user_can( $user_id, 'restrict_content' ) || user_can( $user_id, $post_type->cap->edit_post, $post_id ) ) {
				$can_view = true;
			}

			/* Else, let's check the user's role against the selected roles. */
			else {

				/* Loop through each role and set $can_view to true if the user has one of the roles. */
				foreach ( $roles as $role ) {
					if ( user_can( $user_id, $role ) )
						$can_view = true;
				}
			}
		}
	}

	/* Allow developers to overwrite the final return value. */
	return apply_filters( 'members_can_user_view_post', $can_view, $user_id, $post_id );
}

/**
 * Wrapper function for the members_can_user_view_post() function. This function checks if the currently 
 * logged-in user can view the content of a specific post.
 *
 * @since 0.2.0
 * @param int $post_id The ID of the post to check.
 * @return bool True if the user can view the post. False if the user cannot view the post.
 */
function members_can_current_user_view_post( $post_id = '' ) {

	/* Get the current user object. */
	$current_user = wp_get_current_user();

	/* Return the members_can_user_view_post() function, which returns true/false. */
	return members_can_user_view_post( $current_user->ID, $post_id );
}

?>