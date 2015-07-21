<?php
/**
 * Deprecated functions that are being phased out completely or should be replaced with other functions.
 *
 * @package Members
 * @subpackage Functions
 */

/**
 * @since 0.2.0
 * @deprecated 1.0.0
 */
function members_get_active_roles() {
	_deprecated_function( __FUNCTION__, '1.0.0', 'members_get_active_role_names' );
	return members_get_active_role_names();
}

/**
 * @since 0.2.0
 * @deprecated 1.0.0
 */
function members_get_inactive_roles() {
	_deprecated_function( __FUNCTION__, '1.0.0', 'members_get_inactive_role_names' );
	return members_get_inactive_role_names();
}

/**
 * @since 0.2.0
 * @deprecated 1.0.0
 */
function members_count_roles() {
	_deprecated_function( __FUNCTION__, '1.0.0', 'members_get_role_count' );
	return members_get_role_count();
}

/* ====== Functions removed in the 1.0 branch. ====== */

if ( !function_exists( 'has_role' ) ) { function has_role() {} }
if ( !function_exists( 'current_user_has_role' ) ) { function current_user_has_role() {} }

function members_author_profile() {}
function members_login_form() {}
function members_get_login_form() {}
function members_get_avatar_shortcode() {}
