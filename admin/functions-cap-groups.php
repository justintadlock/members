<?php

# Registers default groups.
add_action( 'members_pre_edit_caps_manager_register', 'members_register_cap_groups', 0 );

/**
 * Returns the instance of the `Members_Cap_Group_Factory` object. Use this function to access the object.
 *
 * @see    Members_Cap_Group_Factory
 * @since  1.0.0
 * @access public
 * @return object
 */
function members_cap_groups() {
	return Members_Cap_Group_Factory::get_instance();
}

/**
 * Registers the default cap groups.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function members_register_cap_groups() {

	members_register_cap_group( 'all', array( 'label' =>  esc_html__( 'All', 'members' ), 'caps' => members_get_capabilities(), 'icon' => 'dashicons-plus', 'count_added' => false ) );

	members_register_cap_group( 'general', array( 'label' => esc_html__( 'General', 'members' ), 'caps' => members_get_wp_general_caps(), 'icon' => 'dashicons-wordpress' ) );

	foreach ( get_post_types( array(), 'objects' ) as $type ) {

		if ( in_array( $type->name, array( 'revision', 'nav_menu_item' ) ) )
			continue;

		$has_caps = members_get_post_type_caps( $type->name );

		if ( empty( $has_caps ) )
			continue;

		$icon = $type->hierarchical ? 'dashicons-admin-page' : 'dashicons-admin-post';

		if ( is_string( $type->menu_icon ) && preg_match( '/dashicons-/i', $type->menu_icon ) )
			$icon = $type->menu_icon;
		else if ( 'attachment' === $type->name )
			$icon = 'dashicons-admin-media';
		else if ( 'download' === $type->name )
			$icon = 'dashicons-download'; // EDD
		else if ( 'product' === $type->name )
			$icon = 'dashicons-cart';

		members_register_cap_group( "type-{$type->name}", array( 'label' => $type->labels->name, 'caps' => $has_caps, 'icon' => $icon ) );
	}

	members_register_cap_group( 'taxonomies', array( 'label' => esc_html__( 'Taxonomies', 'members' ), 'caps' => members_get_tax_caps(), 'icon' => 'dashicons-tag', 'diff_added' => true ) );

	members_register_cap_group( 'themes', array( 'label' => esc_html__( 'Themes', 'members' ), 'caps' => members_get_wp_theme_caps(), 'icon' => 'dashicons-admin-appearance' ) );

	members_register_cap_group( 'plugins', array( 'label' => esc_html__( 'Plugins', 'members' ), 'caps' => members_get_wp_plugin_caps(), 'icon' => 'dashicons-admin-plugins' ) );

	members_register_cap_group( 'users', array( 'label' => esc_html__( 'Users', 'members' ), 'caps' => members_get_wp_user_caps(), 'icon' => 'dashicons-admin-users' ) );

	members_register_cap_group( 'custom', array( 'label' => esc_html__( 'Custom', 'members' ), 'caps' => members_get_capabilities(), 'icon' => 'dashicons-admin-generic', 'diff_added' => true ) );

	// Hook for registering cap groups. Plugins should always register on this hook.
	do_action( 'members_register_cap_groups' );
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
	members_cap_groups()->register_group( $name, $args );
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
	members_cap_groups()->unregister_group( $name );
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
	return members_cap_groups()->group_exists( $name );
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
	return members_cap_groups()->groups;
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
	return members_cap_groups()->get_group( $name );
}
