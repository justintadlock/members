<?php
/**
 * @todo Add inline styles the the admin.css stylesheet.
 *
 * @package Members
 * @subpackage Admin
 */

/* Adds the content permissions meta box to the 'add_meta_boxes' hook. */
add_action( 'add_meta_boxes', 'members_content_permissions_create_meta_box' );

/* Saves the content permissions metabox data to a custom field. */
add_action( 'save_post', 'members_content_permissions_save_meta', 10, 2 );
add_action( 'add_attachment', 'members_content_permissions_save_meta' );
add_action( 'edit_attachment', 'members_content_permissions_save_meta' );

/**
 * @since 0.1.0
 */
function members_content_permissions_create_meta_box() {

	/* Only add the meta box if the current user has the 'restrict_content' capability. */
	if ( current_user_can( 'restrict_content' ) ) {

		/* Get all available public post types. */
		$post_types = get_post_types( array( 'public' => true ), 'objects' );

		/* Loop through each post type, adding the meta box for each type's post editor screen. */
		foreach ( $post_types as $type )
			add_meta_box( 'content-permissions-meta-box', __( 'Content Permissions', 'members' ), 'members_content_permissions_meta_box', $type->name, 'advanced', 'high' );
	}
}

/**
 * @since 0.1.0
 */
function members_content_permissions_meta_box( $object, $box ) {
	global $wp_roles;

	/* Get the roles saved for the post. */
	$roles = get_post_meta( $object->ID, '_members_access_role', false );

	/* Convert old post meta to the new system if no roles were found. */
	if ( empty( $roles ) )
		$roles = members_convert_old_post_meta( $object->ID );
	?>

	<input type="hidden" name="content_permissions_meta_nonce" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />

	<div style="overflow: hidden; margin-left: 5px;">

		<p>
		<?php _e( "Limit access to this post's content to users of the selected roles.", 'members' ); ?>
		</p>

		<?php

		/* Loop through each of the available roles. */
		foreach ( $wp_roles->role_names as $role => $name ) {
			$checked = false;

			/* If the role has been selected, make sure it's checked. */
			if ( is_array( $roles ) && in_array( $role, $roles ) )
				$checked = ' checked="checked" '; ?>

			<div style="width: 32%; float: left; margin: 0 0 5px 0;">
				<label for="members_access_role-<?php echo $role; ?>">
					<input type="checkbox" name="members_access_role[<?php echo $role; ?>]" id="members_access_role-<?php echo $role; ?>" <?php echo $checked; ?> value="<?php echo $role; ?>" /> 
					<?php echo esc_html( $name ); ?>
				</label>
			</div>
		<?php } ?>

	</div>

	<p style="clear: left;">
		<span class="howto"><?php printf( __( 'If no roles are selected, everyone can view the content. The post author, any users who can edit this post, and users with the %s capability can view the content regardless of role.', 'members' ), '<code>restrict_content</code>' ); ?></span>
	</p>

	<p>
		<label for="members_access_error"><?php _e( 'Custom error messsage:', 'members' ); ?></label>
		<textarea id="members_access_error" name="members_access_error" cols="60" rows="2" tabindex="30" style="width: 99%;"><?php echo esc_html( get_post_meta( $object->ID, '_members_access_error', true ) ); ?></textarea>
		<br />
		<span class="howto"><?php _e( 'Message shown to users that do no have permission to view the post.', 'members' ); ?></span>
	</p>

<?php
}

/**
 * @since 0.1.0
 */
function members_content_permissions_save_meta( $post_id, $post = '' ) {
	global $wp_roles;

	/* Fix for attachment save issue in WordPress 3.5. @link http://core.trac.wordpress.org/ticket/21963 */
	if ( !is_object( $post ) )
		$post = get_post();

	/* Verify the nonce. */
	if ( !isset( $_POST['content_permissions_meta_nonce'] ) || !wp_verify_nonce( $_POST['content_permissions_meta_nonce'], plugin_basename( __FILE__ ) ) )
		return false;

	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
	if ( defined('DOING_AJAX') && DOING_AJAX ) return;
	if ( defined('DOING_CRON') && DOING_CRON ) return;

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	/* Don't save if the post is only a revision. */
	if ( 'revision' == $post->post_type )
		return;

	$meta_values = get_post_meta( $post_id, '_members_access_role', false );

	if ( isset( $_POST['members_access_role'] ) && is_array( $_POST['members_access_role'] ) ) {

		foreach ( $_POST['members_access_role'] as $role ) {
			if ( !in_array( $role, $meta_values ) )
				add_post_meta( $post_id, '_members_access_role', $role, false );
		}

		foreach ( $wp_roles->role_names as $role => $name ) {
			if ( !in_array( $role, $_POST['members_access_role'] ) && in_array( $role, $meta_values ) )
				delete_post_meta( $post_id, '_members_access_role', $role );
		}
	}
	elseif ( !empty( $meta_values ) ) {
		delete_post_meta( $post_id, '_members_access_role' );
	}

	$meta = array(
		'_members_access_error' => esc_html( $_POST['members_access_error'] )
	);

	foreach ( $meta as $meta_key => $new_meta_value ) {

		/* Get the meta value of the custom field key. */
		$meta_value = get_post_meta( $post_id, $meta_key, true );

		/* If a new meta value was added and there was no previous value, add it. */
		if ( $new_meta_value && '' == $meta_value )
			add_post_meta( $post_id, $meta_key, $new_meta_value, true );

		/* If the new meta value does not match the old value, update it. */
		elseif ( $new_meta_value && $new_meta_value != $meta_value )
			update_post_meta( $post_id, $meta_key, $new_meta_value );

		/* If there is no new meta value but an old value exists, delete it. */
		elseif ( '' == $new_meta_value && $meta_value )
			delete_post_meta( $post_id, $meta_key, $meta_value );
	}

}

?>