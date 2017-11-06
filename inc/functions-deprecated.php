<?php
/**
 * Deprecated functions that are being phased out completely or should be replaced with other functions.
 *
 * @package    Members
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Returns an array of role names.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_role_names() {
	_deprecated_function( __FUNCTION__, '2.0.0', '' );

	$roles = array();

	foreach ( members_get_roles() as $role )
		$roles[ $role->name ] = $role->name;

	return $roles;
}

/**
 * Returns an array of the role names of roles that have users.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_active_role_names() {
	_deprecated_function( __FUNCTION__, '2.0.0', '' );

	$has_users = array();

	foreach ( members_get_active_roles() as $role )
		$has_users[ $role ] = members_get_role( $role )->get( 'label' );

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
	_deprecated_function( __FUNCTION__, '2.0.0', '' );

	return array_diff( members_get_role_names(), members_get_active_role_names() );
}

/**
 * Returns an array of editable role names.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_editable_role_names() {
	_deprecated_function( __FUNCTION__, '2.0.0', '' );

	$editable = array();

	foreach ( members_role_registry()->editable as $role )
		$editable[ $role->slug ] = $role->name;

	return $editable;
}

/**
 * Returns an array of editable roles.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_editable_role_slugs() {
	_deprecated_function( __FUNCTION__, '2.0.0', '' );

	return array_keys( members_role_registry()->editable );
}

/**
 * Returns an array of uneditable role names.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_uneditable_role_names() {
	_deprecated_function( __FUNCTION__, '2.0.0', '' );

	$uneditable = array();

	foreach ( members_role_registry()->uneditable as $role )
		$uneditable[ $role->slug ] = $role->name;

	return $uneditable;
}

/**
 * Returns an array of uneditable roles.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_uneditable_role_slugs() {
	_deprecated_function( __FUNCTION__, '2.0.0', '' );

	return array_keys( members_role_registry()->uneditable );
}

/**
 * Returns an array of core WordPress role names.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_wordpress_role_names() {
	_deprecated_function( __FUNCTION__, '2.0.0', '' );

	$names = array();

	foreach ( members_role_registry()->wordpress as $role )
		$names[ $role->slug ] = $role->name;

	return $names;
}

/**
 * Returns an array of core WP roles.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_wordpress_role_slugs() {
	_deprecated_function( __FUNCTION__, '2.0.0', '' );

	return array_keys( members_role_registry()->wordpress );
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
	_deprecated_function( __FUNCTION__, '2.0.0', '' );

	return members_role_registry()->get( $role )->name;
}

/**
 * Returns an array of roles.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_role_slugs() {
	_deprecated_function( __FUNCTION__, '2.0.0', '' );

	return array_keys( members_get_roles() );
}

/**
 * Returns an array of the roles that have users.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_active_role_slugs() {
	_deprecated_function( __FUNCTION__, '2.0.0', '' );

	$has_users = array();

	foreach ( members_get_role_user_count() as $role => $count ) {

		if ( 0 < $count )
			$has_users[] = $role;
	}

	return $has_users;
}

/**
 * Returns an array of the roles that have no users.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_inactive_role_slugs() {
	_deprecated_function( __FUNCTION__, '2.0.0', '' );

	return array_diff( array_keys( members_get_roles() ), members_get_active_roles() );
}

/**
 * Returns the caps for the all capability group.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_all_group_caps() {
	_deprecated_function( __FUNCTION__, '2.0.0', '' );

	return members_get_capabilities();
}

/**
 * Returns the caps for the general capability group.
 *
 * @since      1.0.0
 * @deprecated 2.0.0
 * @access     public
 * @return     array
 */
function members_get_general_group_caps() {

	return array_keys( wp_list_filter( members_get_caps(), array( 'group' => 'general' ) ) );
}

/**
 * Returns the caps for the theme capability group.
 *
 * @since      1.0.0
 * @deprecated 2.0.0
 * @access     public
 * @return     array
 */
function members_get_theme_group_caps() {

	return array_keys( wp_list_filter( members_get_caps(), array( 'group' => 'theme' ) ) );
}

/**
 * Returns the caps for the plugin capability group.
 *
 * @since      1.0.0
 * @deprecated 2.0.0
 * @access     public
 * @return     array
 */
function members_get_plugin_group_caps() {

	return array_keys( wp_list_filter( members_get_caps(), array( 'group' => 'plugin' ) ) );
}

/**
 * Returns the caps for the user capability group.
 *
 * @since      1.0.0
 * @deprecated 2.0.0
 * @access     public
 * @return     array
 */
function members_get_user_group_caps() {

	return array_keys( wp_list_filter( members_get_caps(), array( 'group' => 'user' ) ) );
}

/**
 * Additional capabilities provided by the Members plugin that gives users permissions to handle
 * certain features of the plugin.
 *
 * @since      1.0.0
 * @deprecated 2.0.0
 * @access     public
 * @return     array
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
 * @link       http://codex.wordpress.org/Roles_and_Capabilities#Capabilities
 * @since      1.0.0
 * @deprecated 2.0.0
 * @access     public
 * @return     array
 */
function members_get_wp_capabilities() {

	return array(
		'activate_plugins',
		'add_users',
		'assign_categories',
		'assign_post_tags',
		'create_users',
		'delete_categories',
		'delete_others_pages',
		'delete_others_posts',
		'delete_pages',
		'delete_plugins',
		'delete_posts',
		'delete_post_tags',
		'delete_private_pages',
		'delete_private_posts',
		'delete_published_pages',
		'delete_published_posts',
		'delete_themes',
		'delete_users',
		'edit_categories',
		'edit_dashboard',
		'edit_files',
		'edit_others_pages',
		'edit_others_posts',
		'edit_pages',
		'edit_plugins',
		'edit_posts',
		'edit_post_tags',
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
		'manage_post_tags',
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

/**
 * @since      0.2.0
 * @deprecated 1.0.0
 */
//function members_get_active_roles() {
//	_deprecated_function( __FUNCTION__, '1.0.0', 'members_get_active_role_names' );
//	return members_get_active_role_names();
//}

/**
 * @since      0.2.0
 * @deprecated 1.0.0
 */
//function members_get_inactive_roles() {
//	_deprecated_function( __FUNCTION__, '1.0.0', 'members_get_inactive_role_names' );
//	return members_get_inactive_role_names();
//}

/**
 * @since      0.2.0
 * @deprecated 1.0.0
 */
function members_count_roles() {
	_deprecated_function( __FUNCTION__, '1.0.0', 'members_get_role_count' );
	return members_get_role_count();
}

/**
 * @since      0.1.0
 * @deprecated 1.0.0
 */
function members_get_default_capabilities() {
	_deprecated_function( __FUNCTION__, '1.0.0', 'members_get_wp_capabilities' );
	return members_get_wp_capabilities();
}

/**
 * @since      0.1.0
 * @deprecated 1.0.0
 */
function members_get_additional_capabilities() {
	_deprecated_function( __FUNCTION__, '1.0.0', 'members_get_plugin_capabilities' );
	return members_get_plugin_capabilities();
}

/* ====== Functions removed in the 2.0 branch. ====== */

function members_role_factory() {}
function members_role_group_factory() {}
function members_cap_group_factory() {}
function members_manage_users_columns() {}
function members_manage_users_custom_column() {}

/* ====== Functions removed in the 1.0 branch. ====== */

if ( ! function_exists( 'has_role' ) ) { function has_role() {} }
if ( ! function_exists( 'current_user_has_role' ) ) { function current_user_has_role() {} }

function members_author_profile() {}
function members_login_form() {}
function members_get_login_form() {}
function members_get_avatar_shortcode() {}
function members_version_check() {}
function members_install() {}
function members_update() {}
function members_edit_roles_page() {}
function members_edit_capabilities_page() {}
function members_new_role_page() {}
function members_new_capability_page() {}
function members_message_role_deleted() {}
function members_message_roles_deleted() {}
function members_admin_message() {}
function members_admin_enqueue_scripts() {}
function members_admin_enqueue_style() {}
function members_get_nonce() {}
function members_admin_load_post_meta_boxes() {}
function members_content_permissions_create_meta_box() {}
function members_content_permissions_meta_box() {}
function members_content_permissions_save_meta() {}
function members_admin_setup() {}
function members_admin_contextual_help() {}
