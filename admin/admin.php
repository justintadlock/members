<?php
/**
 * @todo Fix all messages and make sure they're working correctly.
 *
 * @package Members
 * @subpackage Admin
 */

/* Set up the administration functionality. */
add_action( 'admin_menu', 'members_admin_setup' );

/* Add message when no role has the 'create_roles' capability. */
add_action( 'members_pre_components_form', 'members_message_no_create_roles' );
add_action( 'members_pre_new_role_form', 'members_message_no_create_roles' );

/* Actions added by the Edit Roles component. */
add_action( 'members_pre_components_form', 'members_message_no_edit_roles' );
add_action( 'members_pre_edit_role_form', 'members_message_no_edit_roles' );
add_action( 'members_pre_edit_roles_form', 'members_message_no_edit_roles' );

/* Add messages to the components form. */
add_action( 'members_pre_components_form', 'members_message_no_restrict_content' );

/**
 * Sets up any functionality needed in the admin.
 *
 * @since 0.2.0
 */
function members_admin_setup() {
	global $members;

	/* If the role manager feature is active, add its admin pages. */
	if ( members_get_setting( 'role_manager' ) ) {

		/* Capability to manage roles.  Users need to change this on initial setup by giving at least one role the 'edit_roles' capability. */
		if ( members_check_for_cap( 'edit_roles' ) )
			$edit_roles_cap = 'edit_roles';
		else
			$edit_roles_cap = 'edit_users';

		/* Create the Manage Roles page. */
		$members->edit_roles_page = add_submenu_page( 'users.php', __( 'Roles', 'members' ), __( 'Roles', 'members' ), $edit_roles_cap, 'roles', 'members_edit_roles_page' );

		/* Create the New Role page. */
		$members->new_roles_page = add_submenu_page( 'users.php', __( 'Add New Role', 'members' ), __( 'Add New Role', 'members' ), 'create_roles', 'new-role', 'members_new_role_page' );
	}

	/* Load post meta boxes on the post editing screen. */
	add_action( 'load-post.php', 'members_admin_load_post_meta_boxes' );

	add_action( 'admin_enqueue_scripts', 'members_admin_enqueue_style' );
	add_action( 'admin_enqueue_scripts', 'members_admin_enqueue_scripts' );
}

/**
 * @since 0.2.0
 */
function members_admin_enqueue_style( $hook_suffix ) {

	$pages = array(
		'users_page_roles',
		'users_page_new-role',
		'settings_page_members-settings'
	);

	if ( in_array( $hook_suffix, $pages ) )
		wp_enqueue_style( 'members-admin', trailingslashit( MEMBERS_URI ) . 'css/admin.css', false, '20110525', 'screen' );
}

/**
 * @since 0.2.0
 */
function members_admin_enqueue_scripts( $hook_suffix ) {

	$pages = array(
		'users_page_roles',
		'users_page_new-role'
	);

	if ( in_array( $hook_suffix, $pages ) )
		wp_enqueue_script( 'members-admin', trailingslashit( MEMBERS_URI ) . 'js/admin.js', array( 'jquery' ), '20110525', true );
}

/**
 * Loads meta boxes for the post editing screen.
 *
 * @since 0.2.0
 */
function members_admin_load_post_meta_boxes() {

	/* If the content permissions component is active, load its post meta box. */
	if ( members_get_setting( 'content_permissions' ) )
		require_once( MEMBERS_ADMIN . 'meta-box-post-content-permissions.php' );
}

/**
 * Loads the role manager main page (Roles).
 *
 * @since 0.1.0
 */
function members_edit_roles_page() {
	require_once( MEMBERS_ADMIN . 'roles.php' );
}

/**
 * Loads the New Role page.
 *
 * @since 0.1.0
 */
function members_new_role_page() {
	require_once( MEMBERS_ADMIN . 'role-new.php' );
}

/**
 * Returns an array of capabilities that should be set on the New Role admin screen.  By default, the only 
 * capability checked is 'read' because it's fairly common.
 *
 * @since 0.1.0
 * @return $capabilities array Default capabilities for new roles.
 */
function members_new_role_default_capabilities() {

	$capabilities = array( 'read' );

	/* Filters should return an array. */
	return apply_filters( 'members_new_role_default_capabilities', $capabilities );
}

/**
 * Message to show when a single role has been deleted.
 *
 * @since 0.1.0
 */
function members_message_role_deleted() {
	members_admin_message( '', __( 'Role deleted.', 'members' ) );
}

/**
 * Message to show when multiple roles have been deleted (bulk delete).
 *
 * @since 0.1.0
 */
function members_message_roles_deleted() {
	members_admin_message( '', __( 'Selected roles deleted.', 'members' ) );
}

/**
 * Message to show when no role has the 'edit_roles' capability.
 *
 * @since 0.1.0
 */
function members_message_no_edit_roles() {

	if ( members_get_setting( 'role_manager' ) && !members_check_for_cap( 'edit_roles' ) )
		members_admin_message( '', __( 'No role currently has the <code>edit_roles</code> capability.  Please add this to each role that should be able to manage/edit roles. If you do not change this, any user that has the <code>edit_users</code> capability will be able to edit roles.', 'members' ) );
}

/**
 * Displays a message if the New Roles component is active and no roles have the 'create_roles' capability.
 *
 * @since 0.1.0
 */
function members_message_no_create_roles() {

	if ( members_get_setting( 'role_manager' ) && !members_check_for_cap( 'create_roles' ) )
		members_admin_message( '', __( 'To create new roles, you must give the <code>create_roles</code> capability to at least one role.', 'members' ) );
}

/**
 * @since 0.1.0
 */
function members_message_no_restrict_content() {

	if ( members_get_setting( 'content_permissions' ) && !members_check_for_cap( 'restrict_content' ) )
		members_admin_message( '', __( 'No role currently has the <code>restrict_content</code> capability.  To use the <em>Content Permissions</em> component, at least one role must have this capability.', 'members' ) );
}

/**
 * A function for displaying messages in the admin.  It will wrap the message in the appropriate <div> with the 
 * custom class entered.  The updated class will be added if no $class is given.
 *
 * @since 0.1.0
 * @param $class string Class the <div> should have.
 * @param $message string The text that should be displayed.
 */
function members_admin_message( $class = 'updated', $message = '' ) {

	echo '<div class="' . ( !empty( $class ) ? esc_attr( $class ) : 'updated' ) . '"><p><strong>' . $message . '</strong></p></div>';
}

/**
 * Members plugin nonce function.  This is to help with securely making sure forms have been processed 
 * from the correct place.
 *
 * @since 0.1.0
 * @param $action string Additional action to add to the nonce.
 */
function members_get_nonce( $action = '' ) {
	if ( $action )
		return "members-component-action_{$action}";
	else
		return "members-plugin";
}

/**
 * @since 0.2.0
 */
function members_delete_role( $role ) {

		/* Get all users with the role to be deleted. */
		$wp_user_search = new WP_User_Search( '', '', $role );
		$change_users = $wp_user_search->get_results();

		/* If there are users with the role we're deleting, loop through them, remove the role, and set the default role. */
		if ( isset( $change_users ) && is_array( $change_users ) ) {
			foreach( $change_users as $move_user ) {
				$new_user = new WP_User( $move_user );

				/* If the user has the role, remove it and set the default. Do we need this check? */
				if ( $new_user->has_cap( $role ) ) {
					$new_user->remove_role( $role );
					$new_user->set_role( $default_role );
				}
			}
		}

		/* Remove the role. */
		remove_role( $role );
}

/**
 * @since 0.2.0
 */
function members_get_user_meta_keys() {
	global $wpdb;

	$keys = $wpdb->get_col( "SELECT meta_key FROM $wpdb->usermeta GROUP BY meta_key ORDER BY meta_key" );

	return $keys;
}

?>