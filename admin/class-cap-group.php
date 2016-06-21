<?php
/**
 * Class for handling a capability group object.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2016, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Capability group object class.
 *
 * @since  1.0.0
 * @access public
 */
final class Members_Cap_Group {

	/**
	 * Name/ID for the group.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	public $name = '';

	/**
	 * Internationalized text label for the group.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	public $label = '';

	/**
	 * Icon for the group.  This can be a dashicons class or a custom class.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    string
	 */
	public $icon = 'dashicons-admin-generic';

	/**
	 * Capabilities for the group.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	public $caps = array( 'read' );

	/**
	 * Whether to merge this groups caps with the added caps array.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    bool
	 */
	public $merge_added = true;

	/**
	 * Whether to remove previously-added caps from this group's caps.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    bool
	 */
	public $diff_added = false;

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

		$this->caps = members_remove_hidden_caps( $this->caps );
	}
}
