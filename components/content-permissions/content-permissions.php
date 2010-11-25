<?php
/**
 * The Content Permissions component was created so that access to specific parts of a site
 * can be granted or denied.  This is the component that gives truly fine-grained control over
 * who can see what content on the front end of the site.
 *
 * Current features of the Content Permissions component:
 * 	- Block content on a post-by-post (or page) basis according to user role.
 *
 * This feature set should eventually include the ability to block access to taxonomies and
 * attachments to be truly useful.
 *
 * @todo Check and test feeds and filter if necessary.
 * @todo Make sure comments aren't shown anywhere.
 * @todo Remove pages from wp_list_pages() and wp_page_menu().
 * @todo Cover ALL the bases.  If something's restricted, make sure it stays that way.
 *
 * @package Members
 * @subpackage Components
 */

/** 
 * Adds the content permissions meta box to the post/page edit screen
 * if the current user has the 'restrict_content' capability.
 */
add_action( 'admin_menu', 'members_content_permissions_create_meta_box' );

/* Saves the content permissions metabox data to a custom field. */
add_action( 'save_post', 'members_content_permissions_save_meta', 1, 2 );

/* Add messages to the components form. */
add_action( 'members_pre_components_form', 'members_message_no_restrict_content' );

/* Filter the content and exerpts. */
add_filter( 'the_content', 'members_content_permissions_protect' );
add_filter( 'get_the_excerpt', 'members_content_permissions_protect' );
add_filter( 'the_excerpt', 'members_content_permissions_protect' );

/* Filter the comments template to make sure comments aren't shown to users without access. */
add_filter( 'comments_template', 'members_content_permissions_comments' );

/**
 * Disables the comments template if the current post has been restricted, unless
 * the user has the role needed to view the content of the post.
 *
 * @todo Allow users to override the "no comments" template if in their theme.
 *
 * @since 0.1
 * @param $template string File URL of the template to display.
 * @return $template string File URL of the template to display.
 */
function members_content_permissions_comments( $template ) {
	global $wp_query;

	$roles = get_post_meta( $wp_query->post->ID, '_role', false );

	if ( !empty( $roles ) && is_array( $roles ) ) {
		foreach( $roles as $role ) {
			if ( !is_feed() && ( current_user_can( $role ) || current_user_can( 'restrict_content' ) ) )
				return $template;
		}
		$template = MEMBERS_COMPONENTS . '/content-permissions/comments.php';
	}
	return $template;
}

/**
 * Displays a message if the Content Permissions component is active but no role
 * has been given the capability of 'restrict_content', which is a required capability to 
 * use the component.
 *
 * @since 0.1
 * @uses is_active_members_component() Checks if the content_permissions component is active.
 * @uses members_check_form_cap() Checks if the restrict_content capability has been given to a role.
 */
function members_message_no_restrict_content() {
	if ( is_active_members_component( 'content_permissions' ) && !members_check_for_cap( 'restrict_content' ) ) {
		$message = __('No role currently has the <code>restrict_content</code> capability.  To use the <em>Content Permissions</em> component, at least one role must have this capability.', 'members');
		members_admin_message( '', $message );
	}
}

/**
 * Disables content passed through the $content variable given the current user's role. The
 * function checks for a custom field key of "Role" and loops through its values, checking
 * if the current user has that particular role.
 *
 * Users with the rescrict_content capability should also be able to see the content.
 * 
 * @since 0.1
 * @uses get_post_meta() Gets the meta values of the "_role" custom field key.
 * @uses current_user_can() Checks if the current user has a particular role (capability).
 * @param $content string The current post's content/excerpt.
 * @return $content string Either the current post's content/excerpt or a content inaccessible message.
 */
function members_content_permissions_protect( $content ) {
	global $post;

	$roles = get_post_meta( $post->ID, '_role', false );

	if ( !empty( $roles ) && is_array( $roles ) ) {
		foreach( $roles as $role ) {
			if ( !is_feed() && ( current_user_can( $role ) || current_user_can( 'restrict_content' ) ) )
				return $content;
		}
		$content = '<p class="restricted alert warning">' . __('Sorry, but you do not have permission to view this content.', 'members') . '</p>';
	}

	return $content;
}

?>