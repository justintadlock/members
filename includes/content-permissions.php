<?php
/**
 * Handles permissions for post content, post excerpts, and post comments.  This is based on whether a user 
 * has permission to view a post according to the settings provided by the plugin.
 *
 * @package Members
 * @subpackage Functions
 */

/* Filter the content and exerpts. */
add_filter( 'the_content', 'members_content_permissions_protect' );
add_filter( 'get_the_excerpt', 'members_content_permissions_protect' );
add_filter( 'the_excerpt', 'members_content_permissions_protect' );
add_filter( 'the_content_feed', 'members_content_permissions_protect' );
add_filter( 'comment_text_rss', 'members_content_permissions_protect' );

/* Filter the comments template to make sure comments aren't shown to users without access. */
add_filter( 'comments_template', 'members_content_permissions_comments' );

/**
 * Disables the comments template if a user doesn't have permission to view the post the comments are 
 * associated with.
 *
 * @since 0.1.0
 * @param string $template The Comments template.
 * @return string $template
 */
function members_content_permissions_comments( $template ) {

	/* Check if the current user has permission to view the comments' post. */
	if ( !members_can_current_user_view_post( get_queried_object_id() ) ) {

		/* Look for a 'comments-no-access.php' template in the parent and child theme. */
		$has_template = locate_template( array( 'comments-no-access.php' ) );

		/* If the template was found, use it.  Otherwise, fall back to the Members comments.php template. */
		$template = ( !empty( $has_template ) ? $has_template : MEMBERS_INCLUDES . 'comments.php' );
	}

	/* Return the comments template filename. */
	return $template;
}

/**
 * Denies/Allows access to view post content depending on whether a user has permission to view the content.
 *
 * @since 0.1.0
 * @param string $content The content of a post.
 * @param string $content The content of a post or an error message.
 */
function members_content_permissions_protect( $content ) {

	/* If the current user can view the post, return the post content. */
	if ( members_can_current_user_view_post( get_the_ID() ) )
		return $content;

	/* Return an error message at this point. */
	return members_get_post_error_message( get_the_ID() );
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

/**
 * Conditional tag to check if a user can view a specific post.
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

	/* Get the post object. */
	$post = get_post( $post_id );

	/* If the post author or a user with the 'restrict_content' cap is viewing the post, return the content. */
	if ( $post->post_author == $user_id || current_user_can( 'restrict_content' ) )
		return true;

	/* Get the roles selected by the user. */
	$roles = get_post_meta( $post_id, '_members_access_role', false );

	/* Check if there are any old roles with the '_role' meta key. */
	if ( empty( $roles ) )
		$roles = members_convert_old_post_meta( $post_id );

	/* If we have an array of roles, let's get to work. */
	if ( !empty( $roles ) && is_array( $roles ) ) {

		/* If viewing a feed or the user's not logged in, assume it's blocked at this point. */
		if ( is_feed() || !is_user_logged_in() )
			return false;

		/* Loop through each role and return true if the user has one of the roles. */
		foreach ( $roles as $role ) {
			if ( current_user_can( $role ) )
				return true;
		}

		/* Return an error message if the user didn't have one of the selected roles. */
		return false;
	}

	/* Assume the content isn't blocked at this point and return true. */
	return true;
}

/**
 * Gets the error message to display for users who do not have access to view the given post.
 *
 * @since 0.2.0
 * @param int $post_id The ID of the post to get the error message for.
 * @return string $return The error message.
 */
function members_get_post_error_message( $post_id ) {

	/* Get the error message for the specific post. */
	$access = get_post_meta( $post_id, '_members_access_error_message', true );

	/* If an error message is found, return it. */
	if ( !empty( $access ) )
		$return = wpautop( $access );

	/* If no error message is found, return the default message. */
	else
		$return = wpautop( members_get_setting( 'content_permissions_error' ) );

	/* Return the error message. */
	return $return;
}

/**
 * Converts the meta values of the old '_role' post meta key to the newer '_members_access_role' meta 
 * key.  The reason for this change is to avoid any potential conflicts with other plugins/themes.  We're 
 * now using a meta key that is extremely specific to the Members plugin.
 *
 * @since 0.2.0
 * @param int $post_id The ID of the post to convert the post meta for.
 * @return array|bool $old_roles|false Returns the array of old roles or false for everything else.
 */
function members_convert_old_post_meta( $post_id ) {

	/* Check if there are any meta values for the '_role' meta key. */
	$old_roles = get_post_meta( $post_id, '_role', false );

	/* If roles were found, let's convert them. */
	if ( !empty( $old_roles ) ) {

		/* Delete the old '_role' post meta. */
		delete_post_meta( $post_id, '_role' );

		/* Check if there are any roles for the '_members_access_role' meta key. */
		$new_roles = get_post_meta( $post_id, '_members_access_role', false );

		/* If new roles were found, don't do any conversion. */
		if ( empty( $new_roles ) ) {

			/* Loop through the old meta values for '_role' and add them to the new '_members_access_role' meta key. */
			foreach ( $old_roles as $role )
				add_post_meta( $post_id, '_members_access_role', $role, false );

			/* Return the array of roles. */
			return $old_roles;
		}
	}

	/* Return false if we get to this point. */
	return false;
}

?>