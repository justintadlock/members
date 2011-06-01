<?php
/**
 * @package Members
 * @subpackage Includes
 */

/* Disables the old levels from being seen. If you need them, use remove_filter() to add display back. */
add_filter( 'members_get_capabilities', 'members_remove_old_levels' );

/**
 * The function that makes this plugin what it is.  It returns all of our capabilities in a nicely-formatted, 
 * alphabetized array with no duplicate capabilities.  It pulls from three different functions to make sure 
 * we get all of the capabilities that we need for use in the plugin components.
 *
 * @since 0.1.0
 * @uses members_get_default_capabilities() Gets an array of WP's default capabilities.
 * @uses members_get_role_capabilities() Gets an array of all the capabilities currently mapped to a role.
 * @uses members_get_additional_capabilities() Gets an array of capabilities added by the plugin.
 * @return array $capabilities An array containing all of the capabilities.
 */
function members_get_capabilities() {

	/* Capabilities array. */
	$capabilities = array();

	/* Grab the default capabilities (these are set by the plugin so the user doesn't lose them). */
	$default_caps = members_get_default_capabilities();

	/* Get the user capabilities that are already set. */
	$role_caps = members_get_role_capabilities();

	/* Gets capabilities added by the plugin. */
	$plugin_caps = members_get_additional_capabilities();

	/* Merge all the capability arrays (current role caps, plugin caps, and default WP caps) together. */
	$capabilities = array_merge( $default_caps, $role_caps, $plugin_caps );

	/* Apply filters to the array of capabilities. Devs should respect the available capabilities and return an array. */
	$capabilities = apply_filters( 'members_get_capabilities', $capabilities );

	/* Sort the capabilities by name so they're easier to read when shown on the screen. */
	sort( $capabilities );

	/* Return the array of capabilities, making sure we have no duplicates. */
	return array_unique( $capabilities );
}

/**
 * Gets an array of capabilities according to each user role.  Each role will return its caps, which are then 
 * added to the overall $capabilities array.
 *
 * Note that if no role has the capability, it technically no longer exists.  Since this could be a problem with 
 * folks accidentally deleting the default WordPress capabilities, the members_default_capabilities() will 
 * return all the defaults.
 *
 * @since 0.1.0
 * @return array $capabilities All the capabilities of all the user roles.
 * @global array $wp_roles Holds all the roles for the installation.
 */
function members_get_role_capabilities() {
	global $wp_roles;

	/* Set up an empty capabilities array. */
	$capabilities = array();

	/* Loop through each role object because we need to get the caps. */
	foreach ( $wp_roles->role_objects as $key => $role ) {

		/* Roles without capabilities will cause an error, so we need to check if $role->capabilities is an array. */
		if ( is_array( $role->capabilities ) ) {

			/* Loop through the role's capabilities and add them to the $capabilities array. */
			foreach ( $role->capabilities as $cap => $grant )
				$capabilities[$cap] = $cap;
		}
	}

	/* Return the capabilities array, making sure there are no duplicates. */
	return array_unique( $capabilities );
}

/**
 * Additional capabilities provided by the Members plugin that gives users permissions to handle certain features
 * of the plugin.
 *
 * @todo Integrate 'edit_roles' into the settings.  It should be a priority on initial setup.
 * @todo Move each capability within its component. Use the 'members_get_capabilities' filter hook to add them.
 *
 * @since 0.1.0
 * @return array $capabilities
 */
function members_get_additional_capabilities() {

	$capabilities = array(
		'list_roles',	// Ability to view roles list
		'create_roles',	// Ability to create new roles
		'delete_roles',	// Ability to delete roles
		'edit_roles',	// Ability to edit a role's caps
		'restrict_content'	// Ability to restrict content (content permissions component)
	);

	return $capabilities;
}

/**
 * Make sure we keep the default capabilities in case users screw 'em up.  A user could easily remove a 
 * useful WordPress capability from all roles.  When this happens, the capability is no longer stored in any of 
 * the roles, so it basically doesn't exist.  This function will house all of the default WordPress capabilities in 
 * case this scenario comes into play.
 *
 * For those reading this note, yes, I did "accidentally" remove all capabilities from my administrator account 
 * when developing this plugin.  And yes, that was fun putting back together. ;)
 *
 * The Codex has a list of all the defaults:
 * @link http://codex.wordpress.org/Roles_and_Capabilities#Capabilities
 *
 * @since 0.1
 * @return array $defaults All the default WordPress capabilities.
 */
function members_get_default_capabilities() {

	/* Create an array of all the default WordPress capabilities so the user doesn't accidentally get rid of them. */
	$defaults = array(
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

	/* Return the array of default capabilities. */
	return $defaults;
}

/**
 * Checks if a specific capability has been given to at least one role. If it has, return true. Else, return false.
 *
 * @since 0.1.0
 * @uses members_get_role_capabilities() Checks for capability in array of role caps.
 * @param string $cap Name of the capability to check for.
 * @return true|false bool Whether the capability has been given to a role.
 */
function members_check_for_cap( $cap = '' ) {

	/* Without a capability, we have nothing to check for.  Just return false. */
	if ( !$cap )
		return false;

	/* Gets capabilities that are currently mapped to a role. */
	$caps = members_get_role_capabilities();

	/* If the capability has been given to at least one role, return true. */
	if ( in_array( $cap, $caps ) )
		return true;

	/* If no role has been given the capability, return false. */
	return false;
}

/**
 * Old WordPress levels system.  This is mostly useful for filtering out the levels when shown in admin 
 * screen.  Plugins shouldn't rely on these levels to create permissions for users.  They should move to the 
 * newer system of checking for a specific capability instead.
 *
 * @since 0.1.0
 * @return array Old user levels.
 */
function members_get_old_levels() {
	return array( 'level_0', 'level_1', 'level_2', 'level_3', 'level_4', 'level_5', 'level_6', 'level_7', 'level_8', 'level_9', 'level_10' );
}

/**
 * Get rid of levels since these are mostly useless in newer versions of WordPress.
 *
 * To remove this filter:
 * remove_filter( 'members_get_capabilities', 'members_remove_old_levels' );
 *
 * @since 0.1.0
 * @param $capabilities array All of the combined capabilities.
 * @return $capabilities array Capabilities with old user levels removed.
 */
function members_remove_old_levels( $capabilities ) {
	return array_diff( $capabilities, members_get_old_levels() );
}

/**
 * Returns an array of capabilities that should be set on the New Role admin screen.  By default, the only 
 * capability checked is 'read' because it's needed for users of the role to view their profile in the admin.
 *
 * @since 0.1.0
 * @return $capabilities array Default capabilities for new roles.
 */
function members_new_role_default_capabilities() {

	$capabilities = array( 'read' );

	/* Filters should return an array. */
	return apply_filters( 'members_new_role_default_capabilities', $capabilities );
}

?>