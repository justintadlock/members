<?php
/**
 * Class for handling a role group object.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2016, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Members;

/**
 * Role group object class.
 *
 * @since  1.2.0
 * @access public
 */
final class Role_Group {

	/**
	 * Name/ID for the group.
	 *
	 * @since  1.2.0
	 * @access protected
	 * @var    string
	 */
	public $name = '';

	/**
	 * Internationalized text label for the group.
	 *
	 * @since  1.2.0
	 * @access protected
	 * @var    string
	 */
	public $label = '';

	/**
	 * Internationalized text label for the group + the count in the form of
	 * `_n_noop( 'Singular Name %s', 'Pluran Name %s', $textdomain )`
	 *
	 * @since  1.2.0
	 * @access protected
	 * @var    string
	 */
	public $label_count = '';

	/**
	 * Array of roles that belong to the group.
	 *
	 * @since  1.2.0
	 * @access protected
	 * @var    array
	 */
	public $roles = array();

	/**
	 * Whether to create a view for the group on the Manage Roles screen.
	 *
	 * @since  1.2.0
	 * @access protected
	 * @var    bool
	 */
	public $show_in_view_list = true;

	/**
	 * Magic method to use in case someone tries to output the object as a string.
	 * We'll just return the name.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return string
	 */
	public function __toString() {
		return $this->name;
	}

	/**
	 * Register a new object.
	 *
	 * @since  1.2.0
	 * @access public
	 * @param  string  $name
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( string $name, array $args = array() ) {

		foreach ( array_keys( get_object_vars( $this ) ) as $key ) {

			if ( isset( $args[ $key ] ) )
				$this->$key = $args[ $key ];
		}

		$this->name = sanitize_key( $name );
	}
}
