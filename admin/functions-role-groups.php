<?php
/**
 * Role groups API. Offers a standardized method for creating role groups.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2017, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Registers default groups.
add_action( 'init', 'members_register_role_groups', 95 );

/**
 * Returns the instance of the role group registry.
 *
 * @since  2.0.0
 * @access public
 * @return object
 */
function members_role_group_registry() {

	return \Members\Registry::get_instance( 'role_group' );
}

/**
 * Function for registering a role group.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $name
 * @param  array   $args
 * @return void
 */
function members_register_role_group( $name, $args = array() ) {

	members_role_group_registry()->register( $name, new \Members\Role_Group( $name, $args ) );
}

/**
 * Unregisters a group.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $name
 * @return void
 */
function members_unregister_role_group( $name ) {

	members_role_group_registry()->unregister( $name );
}

/**
 * Checks if a group exists.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $name
 * @return bool
 */
function members_role_group_exists( $name ) {

	return members_role_group_registry()->exists( $name );
}

/**
 * Returns an array of registered group objects.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_role_groups() {

	return members_role_group_registry()->get_collection();
}

/**
 * Returns a group object if it exists.  Otherwise, `FALSE`.
 *
 * @since  1.0.0
 * @access public
 * @param  string      $name
 * @return object|bool
 */
function members_get_role_group( $name ) {

	return members_role_group_registry()->get( $name );
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
			'roles'       => members_get_editable_roles(),
		)
	);

	// Register the uneditable group.
	members_register_role_group( 'uneditable',
		array(
			'label'       => esc_html__( 'Uneditable', 'members' ),
			'label_count' => _n_noop( 'Uneditable %s', 'Uneditable %s', 'members' ),
			'roles'       => members_get_uneditable_roles(),
		)
	);

	// Register the WordPress group.
	members_register_role_group( 'wordpress',
		array(
			'label'       => esc_html__( 'WordPress', 'members' ),
			'label_count' => _n_noop( 'WordPress %s', 'WordPress %s', 'members' ),
			'roles'       => members_get_wordpress_roles(),
		)
	);

	// Hook for registering role groups. Plugins should always register on this hook.
	do_action( 'members_register_role_groups' );
}
