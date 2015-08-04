<?php

/**
 * Plugin Name: Manage WordPress Posts Using Bulk Edit and Quick Edit
 * Description: This is the code for a tutorial WP Dreamer wrote about managing WordPress posts using bulk and quick edit.
 * Author: WP Dreamer
 * Author URI: http://wpdreamer.com/2012/03/manage-wordpress-posts-using-bulk-edit-and-quick-edit/
 */

add_filter( 'manage_posts_columns', 'manage_members_posts_columns', 10, 1 );
function manage_members_posts_columns( $columns ) {
	$columns['content_permissions_column'] = 'Content Permission';

	return $columns;
}

add_action( 'manage_posts_custom_column', 'manage_members_posts_custom_column', 10, 2 );
function manage_members_posts_custom_column( $column_name, $post_id ) {
	global $wp_roles;
	
	switch( $column_name ) {
		case 'content_permissions_column':

			/* Get the roles saved for the post. */
			$roles = get_post_meta( $post_id, '_members_access_role', false );

			echo '<a title="';
			foreach( $wp_roles->role_names as $role => $name ) { 
				if ( is_array( $roles ) && in_array( $role, $roles ) ) {
					echo esc_html( $name ).'&#13;';
				}
			}
			echo '"><span class="comment-count">' . count( $roles ) . '</span></a>';

			foreach( $roles as $role ) {
				echo '<input type="hidden" name="' . $role . '" value="' . $role . '">';
			}

		break;
	}
}

add_action( 'quick_edit_custom_box', 'quick_edit_content_permissions_box', 10, 2 );
function quick_edit_content_permissions_box( $column_name, $post_type ) {
	global $post, $wp_roles;
		
	switch( $column_name ) {
		case 'content_permissions_column':
		?>
			
			<fieldset class="inline-edit-col-right inline-edit-permission">
				<input type="hidden" name="content_permissions_meta_nonce" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
				<div class="inline-edit-col">
					<span class="title inline-edit-permission-label"><?php echo __( 'Content Permissions', 'members' ) ?></span>
					<ul class="cat-checklist permission-checklist">
						<?php foreach( $wp_roles->role_names as $role => $name ) { ?>
							<li id="permission-<?php echo $role ?>">
								<label class="selectit">
									<input class="role <?php echo $role ?>" type="checkbox" name="<?php echo $role ?>" value="<?php echo $role ?>" >
									<?php echo $name ?>
								</label>
							</li>
						<?php } ?>
					</ul>
				</div>
			</fieldset>
			
		<?php
		break;
	}
}

add_action( 'admin_print_scripts-edit.php', 'enqueue_members_admin_quick_edit_scripts' );
function enqueue_members_admin_quick_edit_scripts() {
	wp_enqueue_script( 'manage-wp-posts-using-bulk-quick-edit', trailingslashit( MEMBERS_URI ) . 'js/quick-edit.js', array( 'jquery', 'inline-edit-post' ), '20150804', true );
}

add_action( 'save_post', 'save_members_quick_edit', 10, 2 );
function save_members_quick_edit( $post_id, $post ) {
	global $wp_roles;
	
	if ( empty( $_POST ) ) {
		return $post_id;
	}
	
	/* Verify the nonce. */
	if ( !isset( $_POST['content_permissions_meta_nonce'] ) || !wp_verify_nonce( $_POST['content_permissions_meta_nonce'], plugin_basename( __FILE__ ) ) ) {
		return false;
	}
	
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
		return;
	}
	
	/* Don't save if the post is only a revision. */
	if ( 'revision' == $post->post_type ) {
		return;
	}
	
	/* Get the roles saved for the post. */
	$roles = get_post_meta( $post_id, '_members_access_role', false );
	
	foreach ( $wp_roles->role_names as $role => $name ) {
		if ( isset( $_POST[$role] ) ) {
			if ( !in_array( $role, $roles ) ) {
				/* If a new meta value was added and there was no previous value, add it. */
				add_post_meta( $post_id, '_members_access_role', $role );
			}
		} else {
			/* If there is no new meta value but an old value exists, delete it. */
			delete_post_meta( $post_id, '_members_access_role', $role );
		}
	}
}
