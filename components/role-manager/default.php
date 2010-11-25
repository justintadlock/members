<?php
/**
 * The Role Manager component allows users to create, edit, and delete roles for use on
 * their site.
 *
 * @package Members
 * @subpackage Components
 */

/* Add the Edit Roles and New Roles page to the admin. */
add_action( 'admin_menu', 'members_component_load_role_manager' );

/* Add message when no role has the 'create_roles' capability. */
add_action( 'members_pre_components_form', 'members_message_no_create_roles' );
add_action( 'members_pre_new_role_form', 'members_message_no_create_roles' );

/* Actions added by the Edit Roles component. */
add_action( 'members_pre_components_form', 'members_message_no_edit_roles' );
add_action( 'members_pre_edit_role_form', 'members_message_no_edit_roles' );
add_action( 'members_pre_edit_roles_form', 'members_message_no_edit_roles' );

/**
 * Loads the settings pages for the Roles and New Roles components.  For a logged-in
 * user to see the New Roles page, they must have the 'create_roles' capability.
 * In order to gain this capability, one should edit a role to give it this capability
 *
 * @since 0.2
 * @global $members
 * @uses add_submenu_page() Adds a submenu to the users menu.
 */
function members_component_load_role_manager() {
	global $members;

	/* Capability to manage roles.  Users need to change this on initial setup by giving at least one role the 'edit_roles' capability. */
	if ( members_check_for_cap( 'edit_roles' ) )
		$edit_roles_cap = 'edit_roles';
	else
		$edit_roles_cap = 'edit_users';

	/* Create the Manage Roles page. */
	$members->edit_roles_page = add_submenu_page( 'users.php', __('Roles', 'members'), __('Roles', 'members'), $edit_roles_cap, 'roles', 'members_edit_roles_page' );

	/* Create the New Role page. */
	$members->new_roles_page = add_submenu_page( 'users.php', __('New Role', 'members'), __('New Role', 'members'), 'create_roles', 'new-role', 'members_new_role_page' );
}

/**
 * Loads the Manage Roles page.
 * @since 0.1
 */
function members_edit_roles_page() {
	require_once( MEMBERS_COMPONENTS . '/role-manager/manage-roles.php' );
}

/**
 * Loads the New Role page when its needed.
 *
 * @since 0.1
 */
function members_new_role_page() {
	require_once( MEMBERS_COMPONENTS . '/role-manager/new-role.php' );
}

/**
 * Returns an array of capabilities that should be set on the New Role admin screen.
 * By default, the only capability checked is 'read' because it's fairly common.
 *
 * @since 0.1
 * @return $capabilities array Default capabilities for new roles.
 */
function members_new_role_default_capabilities() {

	$capabilities = array( 'read' );

	/* Filters should return an array. */
	return apply_filters( 'members_new_role_default_capabilities', $capabilities );
}

/**
 * Message to show when a single role has been deleted.
 * @since 0.1
 */
function members_message_role_deleted() {
	$message = __('Role deleted.', 'members');
	members_admin_message( '', $message );
}

/**
 * Message to show when multiple roles have been deleted (bulk delete).
 * @since 0.1
 */
function members_message_roles_deleted() {
	$message = __('Selected roles deleted.', 'members');
	members_admin_message( '', $message );
}

/**
 * Message to show when no role has the 'edit_roles' capability.
 * @since 0.1
 */
function members_message_no_edit_roles() {
	if ( is_active_members_component( 'edit_roles' ) && !members_check_for_cap( 'edit_roles' ) ) {
		$message = __('No role currently has the <code>edit_roles</code> capability.  Please add this to each role that should be able to manage/edit roles. If you do not change this, any user that has the <code>edit_users</code> capability will be able to edit roles.', 'members');
		members_admin_message( '', $message );
	}
}

/**
 * Displays a message if the New Roles component is active and no 
 * roles have the 'create_roles' capability.
 *
 * @since 0.1
 */
function members_message_no_create_roles() {
	if ( is_active_members_component( 'new_roles' ) && !members_check_for_cap( 'create_roles' ) ) {
		$message = __('To create new roles, you must give the <code>create_roles</code> capability to at least one role.', 'members');
		members_admin_message( '', $message );
	}
}

?>