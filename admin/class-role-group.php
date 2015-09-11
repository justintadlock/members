<?php
/**
 * Class for handling a role group object.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Role group object class.
 *
 * @since  1.0.0
 * @access public
 */
final class Members_Role_Group {

	/**
	 * Stores the properties for the object.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $args = array();

	/**
	 * Magic method for getting object properties.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $property
	 * @return mixed
	 */
	public function __get( $property ) {

		return isset( $this->$property ) ? $this->args[ $property ] : null;
	}

	/**
	 * Magic method for setting object properties.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $property
	 * @param  mixed   $value
	 * @return void
	 */
	public function __set( $property, $value ) {

		if ( isset( $this->$property ) )
			$this->args[ $property ] = $value;
	}

	/**
	 * Magic method for checking if a property is set.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $property
	 * @return bool
	 */
	public function __isset( $property ) {

		return isset( $this->args[ $property ] );
	}

	/**
	 * Don't allow properties to be unset.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $property
	 * @return void
	 */
	public function __unset( $property ) {}

	/**
	 * Magic method to use in case someone tries to output the object as a string.
	 * We'll just return the name.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function __toString() {
		return $this->name;
	}

	/**
	 * Register a new object.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $name
	 * @param  array   $args  {
	 *     @type string  $label        Internationalized text label.
	 *     @type string  $icon         Count label in admin.
	 *     @type array   $roles        Array of roles in the group.
	 * }
	 * @return void
	 */
	public function __construct( $name, $args = array() ) {

		$name = sanitize_key( $name );

		$defaults = array(
			'label'             => '',
			'label_count'       => '',
			'roles'             => array(),
			'show_in_view_list' => true
		);

		$this->args = wp_parse_args( $args, $defaults );

		// Get the roles that exist.
		$existing_roles = array_keys( members_get_role_names() );

		// Remove roles that don't exist.
		if ( $this->args['roles'] )
			$this->args['roles'] = array_intersect( $existing_roles, $this->args['roles'] );

		// Set the name.
		$this->args['name'] = $name;
	}
}
