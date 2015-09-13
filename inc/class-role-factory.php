<?php
/**
 * WordPress' `WP_Roles` and the global `$wp_roles` array don't really cut it.  So, this is a
 * singleton factory class for storing role objects and information that we need.
 *
 * @package    Members
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Role factory class.
 *
 * @since  1.0.0
 * @access public
 */
final class Members_Role_Factory {

	/**
	 * Array of roles added.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $roles = array();

	/**
	 * Array of editable roles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $editable = array();

	/**
	 * Array of uneditable roles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $uneditable = array();

	/**
	 * Array of core WordPress roles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $wordpress = array();

	/**
	 * Private constructor method to prevent a new instance of the object.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Adds a role object.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $role
	 * @return object
	 */
	public function add_role( $role ) {

		// If the role exists with WP but hasn't been added.
		if ( members_role_exists( $role ) ) {

			// Get the role object.
			$this->roles[ $role ] = new Members_Role( $role );

			// Check if role is editable.
			if ( $this->roles[ $role ]->is_editable )
				$this->editable[ $role ] = $this->roles[ $role ];
			else
				$this->uneditable[ $role ] = $this->roles[ $role ];

			// Is WP role?
			if ( members_is_wordpress_role( $role ) )
				$this->wordpress[ $role ] = $this->roles[ $role ];
		}
	}

	/**
	 * Returns a single role object.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $role
	 * @return object
	 */
	public function get_role( $role ) {

		return isset( $this->roles[ $role ] ) ? $this->roles[ $role ] : false;
	}

	/**
	 * Returns an array of role objects.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_roles() {
		return $this->roles;
	}

	/**
	 * Adds all the WP roles as role objects.  Rather than running this elsewhere, we're just
	 * going to call this directly within the class when it is first constructed.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	protected function setup_roles() {

		foreach ( $GLOBALS['wp_roles']->role_names as $role => $name )
			$this->add_role( $role );
	}

	/**
	 * Returns the instance.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new Members_Role_Factory;
			$instance->setup_roles();
		}

		return $instance;
	}
}
