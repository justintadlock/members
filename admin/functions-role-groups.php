<?php
/**
 * Role groups API. Offers a standardized method for creating role groups.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2016, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Registers default groups.
add_action( 'init', 'members_register_role_groups', 15 );

/**
 * Returns the instance of the `Members_Role_Group_Factory` object. Use this function to access the object.
 *
 * @see    Members_Role_Group_Factory
 * @since  1.0.0
 * @access public
 * @return object
 */
function members_role_group_factory() {
	return Members_Role_Group_Factory::get_instance();
}

/**
 * Function for registering a role group.
 *
 * @see    Members_Role_Group_Factory::register_group()
 * @since  1.0.0
 * @access public
 * @param  string  $name
 * @param  array   $args
 * @return void
 */
function members_register_role_group( $name, $args = array() ) {
	members_role_group_factory()->register_group( $name, $args );
}

/**
 * Unregisters a group.
 *
 * @see    Members_Role_Group_Factory::unregister_group()
 * @since  1.0.0
 * @access public
 * @param  string  $name
 * @return void
 */
function members_unregister_role_group( $name ) {
	members_role_group_factory()->unregister_group( $name );
}

/**
 * Checks if a group exists.
 *
 * @see    Members_Role_Group_Factory::group_exists()
 * @since  1.0.0
 * @access public
 * @param  string  $name
 * @return bool
 */
function members_role_group_exists( $name ) {
	return members_role_group_factory()->group_exists( $name );
}

/**
 * Returns an array of registered group objects.
 *
 * @see    Members_Role_Group_Factory::group
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_role_groups() {
	return members_role_group_factory()->groups;
}

/**
 * Returns a group object if it exists.  Otherwise, `FALSE`.
 *
 * @see    Members_Role_Group_Factory::get_group()
 * @see    Members_Role_Group
 * @since  1.0.0
 * @access public
 * @param  string      $name
 * @return object|bool
 */
function members_get_role_group( $name ) {
	return members_role_group_factory()->get_group( $name );
}

/**
 * Registers the default role groups.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function members_register_role_groups() {

	// Get the current user.
	$current_user = wp_get_current_user();

	if ( is_object( $current_user ) ) {

		// Register the mine group.
		members_register_role_group( 'mine',
			array(
				'label'       => esc_html__( 'Mine', 'members' ),
				'label_count' => _n_noop( 'Mine %s', 'Mine %s', 'members' ),
				'roles'       => $current_user->roles,
			)
		);
	}

	// Register the active group.
	members_register_role_group( 'active',
		array(
			'label'       => esc_html__( 'Has Users', 'members' ),
			'label_count' => _n_noop( 'Has Users %s', 'Has Users %s', 'members' ),
			'roles'       => array(), // These will be updated on the fly b/c it requires counting users.
		)
	);

	// Register the inactive group.
	members_register_role_group( 'inactive',
		array(
			'label'       => esc_html__( 'No Users', 'members' ),
			'label_count' => _n_noop( 'No Users %s', 'No Users %s', 'members' ),
			'roles'       => array(), // These will be updated on the fly b/c it requires counting users.
		)
	);

	// Register the editable group.
	members_register_role_group( 'editable',
		array(
			'label'       => esc_html__( 'Editable', 'members' ),
			'label_count' => _n_noop( 'Editable %s', 'Editable %s', 'members' ),
			'roles'       => members_get_editable_role_slugs(),
		)
	);

	// Register the uneditable group.
	members_register_role_group( 'uneditable',
		array(
			'label'       => esc_html__( 'Uneditable', 'members' ),
			'label_count' => _n_noop( 'Uneditable %s', 'Uneditable %s', 'members' ),
			'roles'       => members_get_uneditable_role_slugs(),
		)
	);

	// Register the WordPress group.
	members_register_role_group( 'wordpress',
		array(
			'label'       => esc_html__( 'WordPress', 'members' ),
			'label_count' => _n_noop( 'WordPress %s', 'WordPress %s', 'members' ),
			'roles'       => members_get_wordpress_role_slugs(),
		)
	);

	// Hook for registering role groups. Plugins should always register on this hook.
	do_action( 'members_register_role_groups' );
}
