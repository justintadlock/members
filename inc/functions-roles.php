<?php
/**
 * Role-related functions that extend the built-in WordPress Roles API.
 *
 * @package    Members
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Returns the instance of the `Members_Role_Factory`.
 *
 * @since  1.0.0
 * @access public
 * @param  string
 * @return bool
 */
function members_role_factory() {
	return Members_Role_Factory::get_instance();
}

/* ====== Multiple Role Functions ====== */

/**
 * Returns a count of all the available roles for the site.
 *
 * @since  1.0.0
 * @access public
 * @return int
 */
function members_get_role_count() {
	return count( $GLOBALS['wp_roles']->role_names );
}

/**
 * Returns an array of the role names of roles that have users.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_active_role_names() {
	$has_users = array();

	foreach ( members_role_factory()->has_users as $role )
		$has_users[ $role->role ] = $role->name;

	return $has_users;
}

/**
 * Returns an array of the role names of roles that do not have users.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_inactive_role_names() {
	$no_users = array();

	foreach ( members_role_factory()->no_users as $role )
		$no_users[ $role->role ] = $role->name;

	return $no_users;
}

/**
 * Returns an array of `Members_Role` objects.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_roles() {
	return members_role_factory()->roles;
}

/**
 * Returns an array of role names.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_role_names() {
	$roles = array();

	foreach ( members_role_factory()->roles as $role )
		$roles[ $role->role ] = $role->name;

	return $roles;
}

/**
 * Returns an array of editable role names.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_editable_role_names() {
	$editable = array();

	foreach ( members_role_factory()->editable as $role )
		$editable[ $role->role ] = $role->name;

	return $editable;
}

/**
 * Returns an array of uneditable role names.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_uneditable_role_names() {
	$uneditable = array();

	foreach ( members_role_factory()->uneditable as $role )
		$uneditable[ $role->role ] = $role->name;

	return $uneditable;
}

/**
 * Returns an array of core WordPress role names.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_wordpress_role_names() {
	$names = array();

	foreach ( members_role_factory()->wordpress as $role )
		$names[ $role->role ] = $role->name;

	return $names;
}

/* ====== Single Role Functions ====== */

/**
 * Conditional tag to check if a role exists.
 *
 * @since  1.0.0
 * @access public
 * @param  string
 * @return bool
 */
function members_role_exists( $role ) {
	return $GLOBALS['wp_roles']->is_role( $role );
}

/**
 * Gets a Members role object.
 *
 * @see    Members_Role
 * @since  1.0.0
 * @access public
 * @param  string
 * @return object
 */
function members_get_role( $role ) {
	return members_role_factory()->get_role( $role );
}

/**
 * Sanitizes a role name.  This is a wrapper for the `sanitize_key()` WordPress function.  Only
 * alphanumeric characters and underscores are allowed.  Hyphens are also replaced with underscores.
 *
 * @since  1.0.0
 * @access public
 * @return int
 */
function members_sanitize_role( $role ) {
	$_role = strtolower( $role );
	$_role = preg_replace( '/[^a-z0-9_\-\s]/', '', $_role );
	return apply_filters( 'members_sanitize_role', str_replace( array( '-', ' ' ), '_', $_role ), $role );
}

/**
 * WordPress provides no method of translating custom roles other than filtering the
 * `translate_with_gettext_context` hook, which is very inefficient and is not the proper
 * method of translating.  This is a method that allows plugin authors to hook in and add
 * their own translations.
 *
 * Note the core WP `translate_user_role()` function only translates core user roles.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $role
 * @return string
 */
function members_translate_role( $role ) {
	global $wp_roles;

	return apply_filters( 'members_translate_role', translate_user_role( $wp_roles->role_names[ $role ] ), $role );
}

/**
 * Conditional tag to check if a role has any users.
 *
 * @since  1.0.0
 * @access public
 * @return bool
 */
function members_role_has_users( $role ) {
	return members_role_factory()->get_role( $role )->has_users;
}

/**
 * Conditional tag to check if a role has any capabilities.
 *
 * @since  1.0.0
 * @access public
 * @return bool
 */
function members_role_has_caps( $role ) {
	return members_role_factory()->get_role( $role )->has_caps;
}

/**
 * Counts the number of users for all roles on the site and returns this as an array.  If
 * the `$role` parameter is given, the return value will be the count just for that particular role.
 *
 * @since  0.2.0
 * @access public
 * @param  string     $role
 * @return int|array
 */
function members_get_role_user_count( $role = '' ) {

	// If the count is not already set for all roles, let's get it.
	if ( empty( members_plugin()->role_user_count ) ) {

		// Count users.
		$user_count = count_users();

		// Loop through the user count by role to get a count of the users with each role.
		foreach ( $user_count['avail_roles'] as $_role => $count )
			members_plugin()->role_user_count[ $_role ] = $count;
	}

	// Return the role count.
	if ( $role )
		return isset( members_plugin()->role_user_count[ $role ] ) ? members_plugin()->role_user_count[ $role ] : 0;

	// If the `$role` parameter wasn't passed into this function, return the array of user counts.
	return members_plugin()->role_user_count;
}

/**
 * Returns the number of granted capabilities that a role has.
 *
 * @since  1.0.0
 * @access public
 * @param  string
 * @return int
 */
function members_get_role_granted_cap_count( $role ) {
	return members_role_factory()->get_role( $role )->granted_cap_count;
}

/**
 * Returns the number of denied capabilities that a role has.
 *
 * @since  1.0.0
 * @access public
 * @param  string
 * @return int
 */
function members_get_role_denied_cap_count( $role ) {
	return members_role_factory()->get_role( $role )->denied_cap_count;
}

/**
 * Returns the human-readable role name.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $role
 * @return string
 */
function members_get_role_name( $role ) {
	return members_role_factory()->get_role( $role )->name;
}

/**
 * Conditional tag to check whether a role can be edited.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $role
 * @return bool
 */
function members_is_role_editable( $role ) {
	return members_role_factory()->get_role( $role )->is_editable;
}

/**
 * Conditional tag to check whether a role is a core WordPress URL.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $role
 * @return bool
 */
function members_is_wordpress_role( $role ) {
	return in_array( $role, array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' ) );
}

/* ====== URLs ====== */

/**
 * Returns the URL for the add-new role admin screen.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function members_get_new_role_url() {
	return esc_url( add_query_arg( 'page', 'role-new', admin_url( 'users.php' ) ) );
}

/**
 * Returns the URL for the clone role admin screen.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $role
 * @return string
 */
function members_get_clone_role_url( $role ) {
	return esc_url( add_query_arg( 'clone', $role, members_get_new_role_url() ) );
}

/**
 * Returns the URL for the edit roles admin screen.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function members_get_edit_roles_url( $raw = false ) {
	$url = add_query_arg( 'page', 'roles', admin_url( 'users.php' ) );

	return $raw ? $url : esc_url( $url );
}

/**
 * Returns the URL for the edit "mine" roles admin screen.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function members_get_role_view_url( $view, $raw = false ) {
	$url = add_query_arg( 'role_view', $view, members_get_edit_roles_url( $raw ) );

	return $raw ? $url : esc_url( $url );
}

/**
 * Returns the URL for the edit role admin screen.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $role
 * @param  bool    $raw
 * @return string
 */
function members_get_edit_role_url( $role, $raw = false ) {

	$url = add_query_arg( array( 'action' => 'edit', 'role' => $role ), members_get_edit_roles_url( $raw ) );

	return $raw ? $url : esc_url( $url );
}

/**
 * Returns the URL to permanently delete a role (edit roles screen).
 *
 * @since  1.0.0
 * @access public
 * @param  string  $role
 * @return string
 */
function members_get_delete_role_url( $role ) {
	$url = add_query_arg( array( 'action' => 'delete', 'role' => $role ), members_get_edit_roles_url() );

	return esc_url( wp_nonce_url( $url, 'delete_role', 'members_delete_role_nonce' ) );
}

/**
 * Returns the URL for the users admin screen specific to a role.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $role
 * @return string
 */
function members_get_role_users_url( $role ) {
	return esc_url( admin_url( add_query_arg( 'role', $role, 'users.php' ) ) );
}

/* ====== User-specific role functions ====== */

/**
 * Conditional tag to check whether a user has a specific role.
 *
 * @since  1.0.0
 * @access public
 * @param  int     $user_id
 * @param  string  $role
 * @return bool
 */
function members_user_has_role( $user_id, $role ) {

	$user = new WP_User( $user_id );

	return in_array( $role, (array) $user->roles );
}

/**
 * Conditional tag to check whether the currently logged-in user has a specific role.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $role
 * @return bool
 */
function members_current_user_has_role( $role ) {

	return is_user_logged_in() ? members_user_has_role( get_current_user_id(), $role ) : false;
}

/**
 * Returns an array of the role names a user has.
 *
 * @since  1.0.0
 * @access public
 * @param  int    $user_id
 * @return array
 */
function members_get_user_role_names( $user_id ) {

	$user = new WP_User( $user_id );

	$names = array();

	foreach ( $user->roles as $role )
		$names[ $role ] = members_get_role_name( $role );

	return $names;
}
