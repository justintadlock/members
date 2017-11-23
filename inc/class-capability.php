<?php
/**
 * Creates a new capability object.
 *
 * @package    Members
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Members;

/**
 * Capability class.
 *
 * @since  2.0.0
 * @access public
 */
class Capability {

	/**
	 * The capability name.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $name = '';

	/**
	 * The capability label.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $label = '';

	/**
	 * The group the capability belongs to.
	 *
	 * @see    Members_Cap_Group
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $group = '';

	/**
	 * Return the role string in attempts to use the object as a string.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return string
	 */
	public function __toString() {
		return $this->name;
	}

	/**
	 * Creates a new role object.
	 *
	 * @since  2.0.0
	 * @access public
	 * @global object  $wp_roles
	 * @param  string  $role
	 * @return void
	 */
	public function __construct( $name, $args = array() ) {

		foreach ( array_keys( get_object_vars( $this ) ) as $key ) {

			if ( isset( $args[ $key ] ) )
				$this->$key = $args[ $key ];
		}

		$this->name = sanitize_key( $name );
	}
}
