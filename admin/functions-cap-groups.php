<?php
/**
 * Capability groups API. Offers a standardized method for creating capability groups.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Registers default groups.
add_action( 'init', 'members_register_cap_groups', 15 );

/**
 * Returns the instance of the `Members_Cap_Group_Factory` object. Use this function to access the object.
 *
 * @see    Members_Cap_Group_Factory
 * @since  1.0.0
 * @access public
 * @return object
 */
function members_cap_group_factory() {
	return Members_Cap_Group_Factory::get_instance();
}

/**
 * Function for registering a cap group.
 *
 * @see    Members_Cap_Group_Factory::register_group()
 * @since  1.0.0
 * @access public
 * @param  string  $name
 * @param  array   $args
 * @return void
 */
function members_register_cap_group( $name, $args = array() ) {
	members_cap_group_factory()->register_group( $name, $args );
}

/**
 * Unregisters a group.
 *
 * @see    Members_Cap_Group_Factory::unregister_group()
 * @since  1.0.0
 * @access public
 * @param  string  $name
 * @return void
 */
function members_unregister_cap_group( $name ) {
	members_cap_group_factory()->unregister_group( $name );
}

/**
 * Checks if a group exists.
 *
 * @see    Members_Cap_Group_Factory::group_exists()
 * @since  1.0.0
 * @access public
 * @param  string  $name
 * @return bool
 */
function members_cap_group_exists( $name ) {
	return members_cap_group_factory()->group_exists( $name );
}

/**
 * Returns an array of registered group objects.
 *
 * @see    Members_Cap_Group_Factory::group
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_cap_groups() {
	return members_cap_group_factory()->groups;
}

/**
 * Returns a group object if it exists.  Otherwise, `FALSE`.
 *
 * @see    Members_Cap_Group_Factory::get_group()
 * @see    Members_Cap_Group
 * @since  1.0.0
 * @access public
 * @param  string      $name
 * @return object|bool
 */
function members_get_cap_group( $name ) {
	return members_cap_group_factory()->get_group( $name );
}

/**
 * Registers the default cap groups.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function members_register_cap_groups() {

	// Register the all group.
	members_register_cap_group( 'all',
		array(
			'label'       => esc_html__( 'All', 'members' ),
			'caps'        => members_get_all_group_caps(),
			'icon'        => 'dashicons-plus',
			'merge_added' => false
		)
	);

	// Registers the general group.
	members_register_cap_group( 'general',
		array(
			'label' => esc_html__( 'General', 'members' ),
			'caps'  => members_get_general_group_caps(),
			'icon'  => 'dashicons-wordpress'
		)
	);

	// Loop through every custom post type.
	foreach ( get_post_types( array(), 'objects' ) as $type ) {

		// Skip revisions and nave menu items.
		if ( in_array( $type->name, array( 'revision', 'nav_menu_item' ) ) )
			continue;

		// Get the caps for the post type.
		$has_caps = members_get_post_type_group_caps( $type->name );

		// Skip if the post type doesn't have caps.
		if ( empty( $has_caps ) )
			continue;

		// Set the default post type icon.
		$icon = $type->hierarchical ? 'dashicons-admin-page' : 'dashicons-admin-post';

		// Get the post type icon.
		if ( is_string( $type->menu_icon ) && preg_match( '/dashicons-/i', $type->menu_icon ) )
			$icon = $type->menu_icon;

		else if ( 'attachment' === $type->name )
			$icon = 'dashicons-admin-media';

		else if ( 'download' === $type->name )
			$icon = 'dashicons-download'; // EDD

		else if ( 'product' === $type->name )
			$icon = 'dashicons-cart';

		// Register the post type cap group.
		members_register_cap_group( "type-{$type->name}",
			array(
				'label' => $type->labels->name,
				'caps'  => $has_caps,
				'icon'  => $icon
			)
		);
	}

	// Register the taxonomy group.
	members_register_cap_group( 'taxonomy',
		array(
			'label'      => esc_html__( 'Taxonomies', 'members' ),
			'caps'       => members_get_taxonomy_group_caps(),
			'icon'       => 'dashicons-tag',
			'diff_added' => true
		)
	);

	// Register the theme group.
	members_register_cap_group( 'theme',
		array(
			'label' => esc_html__( 'Appearance', 'members' ),
			'caps'  => members_get_theme_group_caps(),
			'icon'  => 'dashicons-admin-appearance'
		)
	);

	// Register the plugin group.
	members_register_cap_group( 'plugin',
		array(
			'label' => esc_html__( 'Plugins', 'members' ),
			'caps'  => members_get_plugin_group_caps(),
			'icon'  => 'dashicons-admin-plugins'
		)
	);

	// Register the user group.
	members_register_cap_group( 'user',
		array(
			'label' => esc_html__( 'Users', 'members' ),
			'caps'  => members_get_user_group_caps(),
			'icon'  => 'dashicons-admin-users'
		)
	);

	// Register the custom group.
	members_register_cap_group( 'custom',
		array(
			'label'      => esc_html__( 'Custom', 'members' ),
			'caps'       => members_get_capabilities(),
			'icon'       => 'dashicons-admin-generic',
			'diff_added' => true
		)
	);

	// Hook for registering cap groups. Plugins should always register on this hook.
	do_action( 'members_register_cap_groups' );
}

/**
 * Returns the caps for the all capability group.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_all_group_caps() {

	return members_get_capabilities();
}

/**
 * Returns the caps for the general capability group.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_general_group_caps() {

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

/**
 * Returns the caps for a specific post type capability group.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_post_type_group_caps( $post_type = 'post' ) {

	// Get the post type caps.
	$caps = (array) get_post_type_object( $post_type )->cap;

	// remove meta caps.
	unset( $caps['edit_post']   );
	unset( $caps['read_post']   );
	unset( $caps['delete_post'] );

	// Get the cap names only.
	$caps = array_values( $caps );

	// If this is not a core post/page post type.
	if ( ! in_array( $post_type, array( 'post', 'page' ) ) ) {

		// Get the post and page caps.
		$post_caps = array_values( (array) get_post_type_object( 'post' )->cap );
		$page_caps = array_values( (array) get_post_type_object( 'page' )->cap );

		// Remove post/page caps from the current post type caps.
		$caps = array_diff( $caps, $post_caps, $page_caps );
	}

	// If attachment post type, add the `unfiltered_upload` cap.
	if ( 'attachment' === $post_type )
		$caps[] = 'unfiltered_upload';

	// Make sure there are no duplicates and return.
	return array_unique( $caps );
}

/**
 * Returns the caps for the taxonomy capability group.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_taxonomy_group_caps() {

	$taxi = get_taxonomies( array(), 'objects' );

	$caps = array();

	foreach ( $taxi as $tax )
		$caps = array_merge( $caps, array_values( (array) $tax->cap ) );

	return array_unique( $caps );
}

/**
 * Returns the caps for the theme capability group.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_theme_group_caps() {

	return array(
		'delete_themes',
		'edit_theme_options',
		'edit_themes',
		'install_themes',
		'switch_themes',
		'update_themes',
	);
}

/**
 * Returns the caps for the plugin capability group.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_plugin_group_caps() {

	return array(
		'activate_plugins',
		'delete_plugins',
		'edit_plugins',
		'install_plugins',
		'update_plugins',
	);
}

/**
 * Returns the caps for the user capability group.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_user_group_caps() {

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

/**
 * Returns the caps for the custom capability group.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_custom_group_caps() {

	return members_get_capabilities();
}
