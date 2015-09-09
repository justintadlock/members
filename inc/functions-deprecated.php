<?php
/**
 * Deprecated functions that are being phased out completely or should be replaced with other functions.
 *
 * @package    Members
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * @since      0.2.0
 * @deprecated 1.0.0
 */
function members_get_active_roles() {
	_deprecated_function( __FUNCTION__, '1.0.0', 'members_get_active_role_names' );
	return members_get_active_role_names();
}

/**
 * @since      0.2.0
 * @deprecated 1.0.0
 */
function members_get_inactive_roles() {
	_deprecated_function( __FUNCTION__, '1.0.0', 'members_get_inactive_role_names' );
	return members_get_inactive_role_names();
}

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
