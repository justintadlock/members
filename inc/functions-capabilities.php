<?php
/**
 * Functions related to capabilities.
 *
 * @package    Members
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Disables the old user levels from capabilities array.
add_filter( 'members_get_capabilities', 'members_remove_old_levels' );

/**
 * Function for sanitizing a capability.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $cap
 * @return string
 */
function members_sanitize_cap( $cap ) {
	return apply_filters( 'members_sanitize_cap', sanitize_key( $cap ) );
}

/**
 * Conditional tag for checking whether a capability exists.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $cap
 * @return bool
 */
function members_cap_exists( $cap ) {
	return in_array( $cap, members_get_capabilities() );
}

/**
 * Checks if a capability is editable.  A capability is editable if it's not one of the core WP
 * capabilities and doesn't belong to an uneditable role.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $cap
 * @return bool
 */
function members_is_cap_editable( $cap ) {

	$uneditable = array_keys( members_get_uneditable_role_names() );

	return ! in_array( $cap, members_get_wp_capabilities() ) && ! array_intersect( $uneditable, members_get_cap_roles( $cap ) );
}

/**
 * Returns an array of roles that have a capability.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $cap
 * @return array
 */
function members_get_cap_roles( $cap ) {
	global $wp_roles;

	$_roles = array();

	foreach ( $wp_roles->role_objects as $role ) {

		if ( $role->has_cap( $cap ) )
			$_roles[] = $role->name;
	}

	return $_roles;
}

/**
 * Returns the URL for the add-new capability admin screen.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function members_get_new_cap_url() {
	return esc_url( add_query_arg( 'page', 'cap-new', admin_url( 'users.php' ) ) );
}

/**
 * Returns the URL for the edit caps admin screen.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function members_get_edit_caps_url() {
	return esc_url( add_query_arg( 'page', 'capabilities', admin_url( 'users.php' ) ) );
}

/**
 * Returns the URL for deleting a capability.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $cap
 * @return string
 */
function members_get_delete_cap_url( $cap ) {
	$url = add_query_arg( array( 'action' => 'delete', 'cap' => $cap ), members_get_edit_caps_url() );

	return esc_url( wp_nonce_url( $url, 'delete_cap', 'members_delete_cap_nonce' ) );
}

/**
 * The function that makes this plugin what it is.  It returns all of our capabilities in a
 * nicely-formatted, alphabetized array with no duplicate capabilities.  It pulls from three
 * different functions to make sure we get all of the capabilities that we need for use in the
 * plugin components.
 *
 * @since  0.1.0
 * @access public
 * @return array
 */
function members_get_capabilities() {

	// Merge the default WP, role, and plugin caps together.
	$capabilities = array_merge(
		members_get_wp_capabilities(),
		members_get_role_capabilities(),
		members_get_plugin_capabilities()
	);

	// Apply filters to the array of capabilities.
	$capabilities = apply_filters( 'members_get_capabilities', $capabilities );

	// Sort the capabilities alphabetically.
	sort( $capabilities );

	// Discard duplicates and return.
	return array_unique( $capabilities );
}

/**
 * Gets an array of capabilities according to each user role.  Each role will return its caps,
 * which are then added to the overall `$capabilities` array.
 *
 * Note that if no role has the capability, it technically no longer exists.  Since this could be
 * a problem with folks accidentally deleting the default WordPress capabilities, the
 * `members_get_plugin_capabilities()` will return all the defaults.
 *
 * @since  0.1.0
 * @global object  $wp_roles
 * @return array
 */
function members_get_role_capabilities() {
	global $wp_roles;

	// Set up an empty capabilities array.
	$capabilities = array();

	// Loop through each role object because we need to get the caps.
	foreach ( $wp_roles->role_objects as $key => $role ) {

		// Make sure that the role has caps.
		if ( is_array( $role->capabilities ) ) {

			// Add each of the role's caps (both granted and denied) to the array.
			foreach ( $role->capabilities as $cap => $grant )
				$capabilities[ $cap ] = $cap;
		}
	}

	// Return the capabilities array, making sure there are no duplicates.
	return array_unique( $capabilities );
}

/**
 * Additional capabilities provided by the Members plugin that gives users permissions to handle
 * certain features of the plugin.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_plugin_capabilities() {

	return array(
		'list_roles',	   // View roles list.
		'create_roles',	   // Create new roles.
		'delete_roles',	   // Delete roles.
		'edit_roles',	   // Edit a role's caps.
		'restrict_content' // Restrict content (content permissions component).
	);
}

/**
 * Make sure we keep the default capabilities in case users screw 'em up.  A user could easily
 * remove a useful WordPress capability from all roles.  When this happens, the capability is no
 * longer stored in any of the roles, so it basically doesn't exist.  This function will house
 * all of the default WordPress capabilities in case this scenario comes into play.
 *
 * For those reading this note, yes, I did "accidentally" remove all capabilities from my
 * administrator account when developing this plugin.  And yes, that was fun putting back
 * together. ;)
 *
 * @link   http://codex.wordpress.org/Roles_and_Capabilities#Capabilities
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_wp_capabilities() {

	return array(
		'activate_plugins',
		'add_users',
		'create_users',
		'delete_others_pages',
		'delete_others_posts',
		'delete_pages',
		'delete_plugins',
		'delete_posts',
		'delete_private_pages',
		'delete_private_posts',
		'delete_published_pages',
		'delete_published_posts',
		'delete_themes',
		'delete_users',
		'edit_dashboard',
		'edit_files',
		'edit_others_pages',
		'edit_others_posts',
		'edit_pages',
		'edit_plugins',
		'edit_posts',
		'edit_private_pages',
		'edit_private_posts',
		'edit_published_pages',
		'edit_published_posts',
		'edit_theme_options',
		'edit_themes',
		'edit_users',
		'export',
		'import',
		'install_plugins',
		'install_themes',
		'list_users',
		'manage_categories',
		'manage_links',
		'manage_options',
		'moderate_comments',
		'promote_users',
		'publish_pages',
		'publish_posts',
		'read',
		'read_private_pages',
		'read_private_posts',
		'remove_users',
		'switch_themes',
		'unfiltered_html',
		'unfiltered_upload',
		'update_core',
		'update_plugins',
		'update_themes',
		'upload_files'
	);
}

function members_get_wp_dashboard_caps() {

	return array(
		'edit_dashboard',
		'read',
	);
}

function members_get_wp_theme_caps() {

	return array(
		'delete_themes',
		'edit_theme_options',
		'edit_themes',
		'install_themes',
		'switch_themes',
		'update_themes',
	);
}

function members_get_wp_plugin_caps() {

	return array(
		'activate_plugins',
		'delete_plugins',
		'edit_plugins',
		'install_plugins',
		'update_plugins',
	);
}

function members_get_wp_user_caps() {

	return array(
		'add_users',
		'create_roles',
		'create_users',
		'delete_roles',
		'delete_users',
		'edit_roles',
		'edit_users',
		'list_roles',
		'list_users',
		'promote_users',
		'remove_users',
	);
}

function members_get_wp_links_caps() {

	return array(
		'manage_links'
	);
}

function members_get_wp_tools_caps() {

	return array(
		'export',
		'import'
	);
}

function members_get_wp_comments_caps() {

	return array(
		'moderate_comments'
	);
}

function members_get_wp_general_caps() {

	return array(
		'edit_dashboard',
		'edit_files',
		'export',
		'import',
		'manage_links',
		'manage_options',
		'moderate_comments',
		'read',
		'unfiltered_html',
		'update_core',
	);
}

function members_get_post_type_caps( $post_type ) {

	$obj = get_post_type_object( $post_type );

	$caps = (array)$obj->cap;

	// remove meta caps.
	unset( $caps['edit_post'] );
	unset( $caps['read_post'] );
	unset( $caps['delete_post'] );

	$caps = array_values( $caps );

	if ( 'post' !== $post_type && ! $obj->hierarchical ) {

		$_post_obj = get_post_type_object( 'post' );
		$_p_caps = array_values( (array)$_post_obj->cap );

		$caps = array_diff( $caps, $_p_caps );

	} elseif ( 'page' !== $post_type && $obj->hierarchical ) {

		$_post_obj = get_post_type_object( 'page' );
		$_p_caps = array_values( (array)$_post_obj->cap );

		$caps = array_diff( $caps, $_p_caps );
	}

	if ( 'attachment' === $post_type )
		$caps[] = 'unfiltered_upload';

	return $caps;
}

function members_get_tax_caps() {

	$taxi = get_taxonomies( array(), 'objects' );

	$caps = array();

	foreach ( $taxi as $tax ) {

		$caps = array_merge( $caps, array_values( (array)$tax->cap ) );
	}

	return array_unique( $caps );
}

/**
 * Checks if a specific capability has been given to at least one role. If it has, return true.
 * Else, return false.
 *
 * @since  0.1.0
 * @access public
 * @param  string  $cap
 * @return bool
 */
function members_check_for_cap( $cap = '' ) {

	// Without a capability, we have nothing to check for.  Just return false.
	if ( ! $cap )
		return false;

	// Check if the cap is assigned to any role.
	return in_array( $cap, members_get_role_capabilities() );
}

/**
 * Old WordPress levels system.  This is mostly useful for filtering out the levels when shown
 * in admin screen.  Plugins shouldn't rely on these levels to create permissions for users.
 * They should move to the newer system of checking for a specific capability instead.
 *
 * @since  0.1.0
 * @access public
 * @return array
 */
function members_get_old_levels() {

	return array(
		'level_0',
		'level_1',
		'level_2',
		'level_3',
		'level_4',
		'level_5',
		'level_6',
		'level_7',
		'level_8',
		'level_9',
		'level_10'
	);
}

/**
 * Get rid of levels since these are mostly useless in newer versions of WordPress.  Devs should
 * add the `__return_false` filter to the `members_remove_old_levels` hook to utilize user levels.
 *
 * @since  0.1.0
 * @access public
 * @param  array  $caps
 * @return array
 */
function members_remove_old_levels( $caps ) {
	return apply_filters( 'members_remove_old_levels', true ) ? array_diff( $caps, members_get_old_levels() ) : $caps;
}

/**
 * Returns an array of capabilities that should be set on the New Role admin screen.  By default,
 * the only capability checked is 'read' because it's needed for users of the role to view their
 * profile in the admin.
 *
 * @since  0.1.0
 * @access public
 * @return array
 */
function members_new_role_default_capabilities() {

	return apply_filters( 'members_new_role_default_capabilities', array( 'read' ) );
}
