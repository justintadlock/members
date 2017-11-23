<?php
/**
 * Class for handling a role group object.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Members;

/**
 * Role group object class.
 *
 * @since  2.0.0
 * @access public
 */
final class Role_Group {

	/**
	 * Name/ID for the group.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $name = '';

	/**
	 * Internationalized text label for the group.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $label = '';

	/**
	 * Internationalized text label for the group + the count in the form of
	 * `_n_noop( 'Singular Name %s', 'Plural Name %s', $textdomain )`
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $label_count = '';

	/**
	 * Array of roles that belong to the group.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    array
	 */
	public $roles = array();

	/**
	 * Whether to create a view for the group on the Manage Roles screen.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    bool
	 */
	public $show_in_view_list = true;

	/**
	 * Magic method to use in case someone tries to output the object as a string.
	 * We'll just return the name.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return string
	 */
	public function __toString() {
		return $this->name;
	}

	/**
	 * Register a new object.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  string  $name
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( $name, $args = array() ) {

		foreach ( array_keys( get_object_vars( $this ) ) as $key ) {

			if ( isset( $args[ $key ] ) )
				$this->$key = $args[ $key ];
		}

		$this->name = sanitize_key( $name );

		$registered_roles = array_keys( wp_list_filter( members_get_roles(), array( 'group' => $this->name ) ) );

		$this->roles = array_unique( array_merge( $this->roles, $registered_roles ) );
	}
}
