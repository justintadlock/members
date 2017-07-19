<?php
/**
 * Role groups API. Offers a standardized method for creating role groups.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Registers default groups.
add_action( 'init',                         'members_register_role_groups',         95 );
add_action( 'members_register_role_groups', 'members_register_default_role_groups',  5 );

/**
 * Fires the role group registration action hook.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function members_register_role_groups() {

	do_action( 'members_register_role_groups' );
}


/**
 * Registers the default role groups.
 *
 * @since  2.0.0
 * @access public
 * @return void
 */
function members_register_default_role_groups() {

	// Register the WordPress group.
	members_register_role_group( 'wordpress',
		array(
			'label'       => esc_html__( 'WordPress', 'members' ),
			'label_count' => _n_noop( 'WordPress %s', 'WordPress %s', 'members' ),
			'roles'       => members_get_wordpress_roles(),
		)
	);
}

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
