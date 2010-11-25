<?php
/**
 * Adds the meta box for the Content Permissions component.  This allows users with
 * the 'restrict_content' capability to restrict posts/pages on a post/page basis. Roles
 * with the 'restrict_content' capability should be able to see all content, regardless
 * of the settings.
 *
 * @package Members
 * @subpackage Components
 */

/**
 * Adds the meta box to the post/page edit screen if the current user has
 * the 'restrict_content' capability.
 *
 * @since 0.1
 * @uses add_meta_box() Creates an additiona meta box.
 */
function members_content_permissions_create_meta_box() {
	if ( current_user_can( 'restrict_content' ) ) {
		add_meta_box( 'content-permissions-meta-box', 'Content Permissions', 'members_content_permissions_meta_box', 'post', 'advanced', 'high' );
		add_meta_box( 'content-permissions-meta-box', 'Content Permissions', 'members_content_permissions_meta_box', 'page', 'advanced', 'high' );
	}
}

/**
 * Controls the display of the content permissions meta box.  This allows users
 * to select roles that should have access to an individual post/page.
 *
 * @since 0.1
 * @global $post
 * @global $wp_roles
 * @param $object
 * @param $box
 */
function members_content_permissions_meta_box( $object, $box ) {
	global $post, $wp_roles; ?>

	<input type="hidden" name="content_permissions_meta_nonce" value="<?php echo wp_create_nonce(plugin_basename(__FILE__)); ?>" />

	<p>
		<label for="roles"><?php _e('<strong>Roles:</strong> Restrict the content to these roles on the front end of the site.  If all boxes are left unchecked, everyone can view the content.', 'members'); ?></label>
	</p>

	<div style="overflow: hidden;">

		<?php

		/* Get the 'Role' meta key. */
		$meta = get_post_meta( $post->ID, '_role', false );

		/* Loop through each of the available roles. */
		foreach ( $wp_roles->role_names as $role => $name ) {
			$checked = false;

			/* If the role has been selected, make sure it's checked. */
			if ( is_array( $meta ) && in_array( $role, $meta ) )
				$checked = ' checked="checked" '; ?>

			<p style="width: 32%; float: left; margin-right: 0;">
				<label for="role-<?php echo $role; ?>">
					<input type="checkbox" name="role[<?php echo $role; ?>]" id="role-<?php echo $role; ?>" <?php echo $checked; ?> value="<?php echo $role; ?>" /> 
					<?php echo str_replace( '|User role', '', $name ); ?>
				</label>
			</p>
		<?php } ?>

	</div><?php
}

/**
 * Saves the content permissions metabox data to a custom field.
 *
 * @since 0.1
 */
function members_content_permissions_save_meta( $post_id, $post ) {
	global $wp_roles;

	/* Only allow users that can edit the current post to submit data. */
	if ( 'post' == $post->post_type && !current_user_can( 'edit_posts', $post_id ) )
		return;

	/* Only allow users that can edit the current page to submit data. */
	elseif ( 'page' == $post->post_type && !current_user_can( 'edit_pages', $post_id ) )
		return;

	/* Don't save if the post is only a revision. */
	if ( 'revision' == $post->post_type )
		return;

	/* Loop through each of the site's available roles. */
	foreach ( $wp_roles->role_names as $role => $name ) {

		/* Get post metadata for the custom field key 'Role'. */
		$meta = (array)get_post_meta( $post_id, '_role', false );

		/* Check if the role was selected. */
		if ( $_POST['role'][$role] ) {

			/* If selected and already saved, continue looping through the roles and do nothing for this role. */
			if ( in_array( $role, $meta ) )
				continue;

			/* If the role was seleted and not already saved, add the role as a new value to the 'Role' custom field. */
			else
				$add = add_post_meta( $post_id, '_role', $role, false );
		}

		/* If role not selected, delete. */
		else
			$delete = delete_post_meta( $post_id, '_role', $role );

	} // End loop through site's roles.
}

?>