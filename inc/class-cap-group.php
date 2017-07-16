<?php
/**
 * Class for handling a capability group object.
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
 * Capability group object class.
 *
 * @since  2.0.0
 * @access public
 */
final class Cap_Group {

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
	 * Icon for the group.  This can be a dashicons class or a custom class.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $icon = 'dashicons-admin-generic';

	/**
	 * Capabilities for the group.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    array
	 */
	public $caps = array();

	/**
	 * Sort order priority.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    int
	 */
	public $priority = 10;

	/**
	 * Whether to remove previously-added caps from this group's caps.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    bool
	 */
	public $diff_added = false;

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
	 * @param  array   $args  {
	 *     @type string  $label        Internationalized text label.
	 *     @type string  $icon         Dashicon icon in the form of `dashicons-icon-name`.
	 *     @type array   $caps         Array of capabilities in the group.
	 *     @type bool    $merge_added  Whether to merge this caps into the added caps array.
	 *     @type bool    $diff_added   Whether to remove previously-added caps from this group.
	 * }
	 * @return void
	 */
	public function __construct( $name, $args = array() ) {

		foreach ( array_keys( get_object_vars( $this ) ) as $key ) {

			if ( isset( $args[ $key ] ) )
				$this->$key = $args[ $key ];
		}

		$this->name = sanitize_key( $name );

		$registered_caps = array_keys( wp_list_filter( members_get_caps(), array( 'group' => $this->name ) ) );

		$this->caps = array_unique( array_merge( $this->caps, $registered_caps ) );

		$this->caps = members_remove_hidden_caps( $this->caps );
	}
}
