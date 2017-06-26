<?php
/**
 * Capability groups API. Offers a standardized method for creating capability groups.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2017, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Registers default groups.
add_action( 'init', 'members_register_cap_groups', 95 );

/**
 * Returns the instance of cap group registry.
 *
 * @since  1.2.0
 * @access public
 * @return object
 */
function members_cap_group_registry() {

	return \Members\Registry::get_instance( 'cap_group' );
}

/**
 * Function for registering a cap group.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $name
 * @param  array   $args
 * @return void
 */
function members_register_cap_group( $name, $args = array() ) {

	members_cap_group_registry()->register( $name, new \Members\Cap_Group( $name, $args ) );
}

/**
 * Unregisters a group.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $name
 * @return void
 */
function members_unregister_cap_group( $name ) {

	members_cap_group_registry()->unregister( $name );
}

/**
 * Checks if a group exists.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $name
 * @return bool
 */
function members_cap_group_exists( $name ) {

	return members_cap_group_registry()->exists( $name );
}

/**
 * Returns an array of registered group objects.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_cap_groups() {

	return members_cap_group_registry()->get_collection();
}

/**
 * Returns a group object if it exists.  Otherwise, `FALSE`.
 *
 * @since  1.0.0
 * @access public
 * @param  string      $name
 * @return object|bool
 */
function members_get_cap_group( $name ) {

	return members_cap_group_registry()->get( $name );
}

/**
 * Registers the default cap groups.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function members_register_cap_groups() {

	// Registers the general group.
	members_register_cap_group( 'general',
		array(
			'label'    => esc_html__( 'General', 'members' ),
			'icon'     => 'dashicons-wordpress',
			'priority' => 5
		)
	);

	// Loop through every custom post type.
	foreach ( get_post_types( array(), 'objects' ) as $type ) {

		// Skip revisions and nave menu items.
		if ( in_array( $type->name, array( 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset' ) ) )
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
				'label'    => $type->labels->name,
				'caps'     => $has_caps,
				'icon'     => $icon,
				'priority' => 5
			)
		);
	}

	// Register the taxonomy group.
	members_register_cap_group( 'taxonomy',
		array(
			'label'      => esc_html__( 'Taxonomies', 'members' ),
			'caps'       => members_get_taxonomy_group_caps(),
			'icon'       => 'dashicons-tag',
			'diff_added' => true,
			'priority'   => 10
		)
	);

	// Register the theme group.
	members_register_cap_group( 'theme',
		array(
			'label'    => esc_html__( 'Appearance', 'members' ),
			'icon'     => 'dashicons-admin-appearance',
			'priority' => 15
		)
	);

	// Register the plugin group.
	members_register_cap_group( 'plugin',
		array(
			'label'    => esc_html__( 'Plugins', 'members' ),
			'icon'     => 'dashicons-admin-plugins',
			'priority' => 20
		)
	);

	// Register the user group.
	members_register_cap_group( 'user',
		array(
			'label'    => esc_html__( 'Users', 'members' ),
			'icon'     => 'dashicons-admin-users',
			'priority' => 25
		)
	);

	// Register the custom group.
	members_register_cap_group( 'custom',
		array(
			'label'      => esc_html__( 'Custom', 'members' ),
			'caps'       => members_get_capabilities(),
			'icon'       => 'dashicons-admin-generic',
			'diff_added' => true,
			'priority'   => 90
		)
	);

	// Register the all group.
	members_register_cap_group( 'all',
		array(
			'label'       => esc_html__( 'All', 'members' ),
			'caps'        => members_get_all_group_caps(),
			'icon'        => 'dashicons-plus',
			'merge_added' => false,
			'priority'    => 95
		)
	);

	// Hook for registering cap groups. Plugins should always register on this hook.
	do_action( 'members_register_cap_groups' );

	// Check if the `all` group is registered.
	if ( members_cap_group_exists( 'all' ) ) {

		// Set up an empty caps array and get the `all` group object.
		$caps   = array();
		$_group = members_get_cap_group( 'all' );

		// Get the caps from every registered group.
		foreach ( members_get_cap_groups() as $group )
			$caps = array_merge( $caps, $group->caps );

		// Sort the caps alphabetically.
		asort( $caps );

		// Assign all caps to the `all` group.
		$_group->caps = array_unique( $caps );
	}
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

	$registered_caps = array_keys( wp_list_filter( members_get_caps(), array( 'group' => "type-{$post_type}" ) ) );

	if ( $registered_caps )
		array_merge( $caps, $registered_caps );

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

	$registered_caps = array_keys( wp_list_filter( members_get_caps(), array( 'group' => 'taxonomy' ) ) );

	if ( $registered_caps )
		array_merge( $caps, $registered_caps );

	return array_unique( $caps );
}

/**
 * Returns the caps for the custom capability group.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_custom_group_caps() {

	$caps = members_get_capabilities();

	$registered_caps = array_keys( wp_list_filter( members_get_caps(), array( 'group' => 'custom' ) ) );

	if ( $registered_caps )
		array_merge( $caps, $registered_caps );

	return array_unique( $caps );
}

/**
 * Returns the caps for the general capability group.
 *
 * @since      1.0.0
 * @deprecated 1.2.0
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
 * @deprecated 1.2.0
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
 * @deprecated 1.2.0
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
 * @deprecated 1.2.0
 * @access     public
 * @return     array
 */
function members_get_user_group_caps() {

	return array_keys( wp_list_filter( members_get_caps(), array( 'group' => 'user' ) ) );
}
