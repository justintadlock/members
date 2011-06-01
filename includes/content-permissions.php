<?php
/**
 * Handles permissions for post content, post excerpts, and post comments.  This is based on whether a user 
 * has permission to view a post according to the settings provided by the plugin.
 *
 * @package Members
 * @subpackage Functions
 */

/* Enable the content permissions features on the front end of the site. */
add_action( 'after_setup_theme', 'members_enable_content_permissions', 1 );

/**
 * Adds required filters for the content permissions feature if it is active.
 *
 * @since 0.2.0
 */
function members_enable_content_permissions() {

	/* Only add filters if the content permissions feature is enabled and we're not in the admin. */
	if ( members_get_setting( 'content_permissions' ) && !is_admin() ) {

		/* Filter the content and exerpts. */
		add_filter( 'the_content', 'members_content_permissions_protect' );
		add_filter( 'get_the_excerpt', 'members_content_permissions_protect' );
		add_filter( 'the_excerpt', 'members_content_permissions_protect' );
		add_filter( 'the_content_feed', 'members_content_permissions_protect' );
		add_filter( 'comment_text_rss', 'members_content_permissions_protect' );

		/* Filter the comments template to make sure comments aren't shown to users without access. */
		add_filter( 'comments_template', 'members_content_permissions_comments' );

		/* Use WP formatting filters on the post error message. */
		add_filter( 'members_post_error_message', 'wptexturize' );
		add_filter( 'members_post_error_message', 'convert_smilies' );
		add_filter( 'members_post_error_message', 'convert_chars' );
		add_filter( 'members_post_error_message', 'wpautop' );
		add_filter( 'members_post_error_message', 'shortcode_unautop' );
		add_filter( 'members_post_error_message', 'do_shortcode' );
	}
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

		/* Allow devs to overwrite the comments template. */
		$template = apply_filters( 'members_comments_template', $template );
	}

	/* Return the comments template filename. */
	return $template;
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
 * Conditional tag to check if a user can view a specific post.  A user cannot view a post if their user role has 
 * not been selected in the 'Content Permissions' meta box on the edit post screen in the admin.  Non-logged in 
 * site visitors cannot view posts if roles were seletected.  If no roles were selected, all users and site visitors 
 * can view the content.
 *
 * There are exceptions to this rule though.  The post author, any user with the 'restrict_content' capability, 
 * and users that have the ability to edit the post can all view the post, even if their role was not granted 
 * permission to view the post.
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

	/* Get the roles selected by the user. */
	$roles = get_post_meta( $post_id, '_members_access_role', false );

	/* Check if there are any old roles with the '_role' meta key. */
	if ( empty( $roles ) )
		$roles = members_convert_old_post_meta( $post_id );

	/* If we have an array of roles, let's get to work. */
	if ( !empty( $roles ) && is_array( $roles ) ) {

		/* If viewing a feed or if the user's not logged in, assume it's blocked at this point. */
		if ( is_feed() || !is_user_logged_in() )
			return false;

		/* Get the post object. */
		$post = get_post( $post_id );

		/* Get the post type object. */
		$post_type = get_post_type_object( $post->post_type );

		/* If the post author, the current user can edit the post, or the current user can 'restrict_content', return true. */
		if ( $post->post_author == $user_id || current_user_can( 'restrict_content' ) || current_user_can( $post_type->cap->edit_post, $post_id ) )
			return true;

		/* Loop through each role and return true if the user has one of the roles. */
		foreach ( $roles as $role ) {
			if ( current_user_can( $role ) )
				return true;
		}

		/* Return an error message if the user doesn't have one of the selected roles. */
		return false;
	}

	/* Assume the content isn't blocked at this point and return true. */
	return true;
}

/**
 * Gets the error message to display for users who do not have access to view the given post.  The function first 
 * checks to see if a custom error message has been written for the specific post.  If not, it loads the error 
 * message set on the plugins settings page.
 *
 * @since 0.2.0
 * @param int $post_id The ID of the post to get the error message for.
 * @return string $return The error message.
 */
function members_get_post_error_message( $post_id ) {

	/* Get the error message for the specific post. */
	$error_message = get_post_meta( $post_id, '_members_access_error', true );

	/* If an error message is found, return it. */
	if ( !empty( $error_message ) )
		$return = $error_message;

	/* If no error message is found, return the default message. */
	else
		$return = members_get_setting( 'content_permissions_error' );

	/* Return the error message. */
	return apply_filters( 'members_post_error_message', $return );
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