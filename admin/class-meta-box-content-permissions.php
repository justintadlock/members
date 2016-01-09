<?php
/**
 * Content permissions meta box.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2016, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Class to handle the content permissios meta box and saving the meta.
 *
 * @since  1.0.0
 * @access public
 */
final class Members_Meta_Box_Content_Permissions {

	/**
	 * Holds the instances of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance;

	/**
	 * Sets up the appropriate actions.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return void
	 */
	protected function __construct() {

		// If content permissions is disabled, bail.
		if ( ! members_content_permissions_enabled() )
			return;

		add_action( 'load-post.php',     array( $this, 'load' ) );
		add_action( 'load-post-new.php', array( $this, 'load' ) );
	}

	/**
	 * Fires on the page load hook to add actions specifically for the post and
	 * new post screens.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function load() {

		// Enqueue scripts/styles.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );

		// Add custom meta boxes.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		// Save metadata on post save.
		add_action( 'save_post', array( $this, 'update' ), 10, 2 );
	}

	/**
	 * Enqueues scripts styles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {

		wp_enqueue_style( 'members-admin' );
	}

	/**
	 * Adds the meta box.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $post_type
	 * @return void
	 */
	public function add_meta_boxes( $post_type ) {

		// If the current user can't restrict content, bail.
		if ( ! current_user_can( 'restrict_content' ) )
			return;

		// Get the post type object.
		$type = get_post_type_object( $post_type );

		// If this is a public post type, add the meta box.
		// Note that we're disabling for attachments b/c users get confused between "content" and "file".
		if ( 'attachment' !== $type->name && $type->public )
			add_meta_box( 'members-cp', esc_html__( 'Content Permissions', 'members' ), array( $this, 'meta_box' ), $post_type, 'advanced', 'high' );
	}

	/**
	 * Outputs the meta box HTML.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $post
	 * @global object  $wp_roles
	 * @return void
	 */
	public function meta_box( $post ) {
		global $wp_roles;

		// Get roles and sort.
		 $_wp_roles = $wp_roles->role_names;
		asort( $_wp_roles );

		// Get the roles saved for the post.
		$roles = get_post_meta( $post->ID, '_members_access_role', false );

		// Convert old post meta to the new system if no roles were found.
		if ( empty( $roles ) )
			$roles = members_convert_old_post_meta( $post->ID );

		// Nonce field to validate on save.
		wp_nonce_field( 'members_cp_meta_nonce', 'members_cp_meta' );

		// Hook for firing at the top of the meta box.
		do_action( 'members_cp_meta_box_before', $post ); ?>

		<p>
			<?php esc_html_e( "Limit access to this post's content to users of the selected roles.", 'members' ); ?>
		</p>

		<div class="members-cp-role-list-wrap">

			<ul class="members-cp-role-list">

			<?php foreach ( $_wp_roles as $role => $name ) : ?>
				<li>
					<label>
						<input type="checkbox" name="members_access_role[]" <?php checked( is_array( $roles ) && in_array( $role, $roles ) ); ?> value="<?php echo esc_attr( $role ); ?>" />
						<?php echo esc_html( translate_user_role( $name ) ); ?>
					</label>
				</li>
			<?php endforeach; ?>

			</ul>
		</div>

		<p class="howto">
			<?php printf( esc_html__( 'If no roles are selected, everyone can view the content. The post author, any users who can edit this post, and users with the %s capability can view the content regardless of role.', 'members' ), '<code>restrict_content</code>' ); ?>
		</p>

		<p>
			<label for="members_access_error"><?php esc_html_e( 'Custom error message:', 'members' ); ?></label>
			<textarea class="widefat" id="members_access_error" name="members_access_error" rows="6"><?php echo esc_textarea( get_post_meta( $post->ID, '_members_access_error', true ) ); ?></textarea>
			<span class="howto"><?php _e( 'Message shown to users that do not have permission to view the post.', 'members' ); ?></span>
		</p><?php

		// Hook that fires at the end of the meta box.
		do_action( 'members_cp_meta_box_after', $post );
	}

	/**
	 * Saves the post meta.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  int     $post_id
	 * @param  object  $post
	 * @return void
	 */
	public function update( $post_id, $post = '' ) {

		// Fix for attachment save issue in WordPress 3.5.
		// @link http://core.trac.wordpress.org/ticket/21963
		if ( ! is_object( $post ) )
			$post = get_post();

		// Verify the nonce.
		if ( ! isset( $_POST['members_cp_meta'] ) || ! wp_verify_nonce( $_POST['members_cp_meta'], 'members_cp_meta_nonce' ) )
			return;

		/* === Roles === */

		// Get the current roles.
		$current_roles = members_get_post_roles( $post_id );

		// Get the new roles.
		$new_roles = isset( $_POST['members_access_role'] ) ? $_POST['members_access_role'] : '';

		// If we have an array of new roles, set the roles.
		if ( is_array( $new_roles ) )
			members_set_post_roles( $post_id, array_map( 'members_sanitize_role', $new_roles ) );

		// Else, if we have current roles but no new roles, delete them all.
		elseif ( !empty( $current_roles ) )
			members_delete_post_roles( $post_id );

		/* === Error Message === */

		// Get the old access message.
		$old_message = members_get_post_access_message( $post_id );

		// Get the new message.
		$new_message = isset( $_POST['members_access_error'] ) ? stripslashes( wp_filter_post_kses( addslashes( $_POST['members_access_error'] ) ) ) : '';

		// If we have don't have a new message but do have an old one, delete it.
		if ( '' == $new_message && $old_message )
			members_delete_post_access_message( $post_id );

		// If the new message doesn't match the old message, set it.
		else if ( $new_message !== $old_message )
			members_set_post_access_message( $post_id, $new_message );
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		if ( ! self::$instance )
			self::$instance = new self;

		return self::$instance;
	}
}

Members_Meta_Box_Content_Permissions::get_instance();
