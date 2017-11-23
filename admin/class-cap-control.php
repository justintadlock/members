<?php
/**
 * Capability control class for use in the edit capabilities tabs.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Members\Admin;

/**
 * Cap control class.
 *
 * @since  2.0.0
 * @access public
 */
final class Cap_Control {

	/**
	 * Stores the cap tabs object.
	 *
	 * @see    Members_Cap_Tabs
	 * @since  2.0.0
	 * @access public
	 * @var    object
	 */
	public $manager;

	/**
	 * Name of the capability the control is for.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $cap = '';

	/**
	 * ID of the section the control is for.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $section = '';

	/**
	 * Array of data to pass as a json object to the Underscore template.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    array
	 */
	public $json = array();

	/**
	 * Creates a new control object.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  object  $manager
	 * @param  string  $cap
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( $manager, $cap, $args = array() ) {

		foreach ( array_keys( get_object_vars( $this ) ) as $key ) {

			if ( isset( $args[ $key ] ) )
				$this->$key = $args[ $key ];
		}

		$this->manager = $manager;
		$this->cap     = $cap;
	}

	/**
	 * Returns the json array.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return array
	 */
	public function json() {
		$this->to_json();
		return $this->json;
	}

	/**
	 * Adds custom data to the json array. This data is passed to the Underscore template.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function to_json() {

		// Is the role editable?
		$is_editable = $this->manager->role ? members_is_role_editable( $this->manager->role->name ) : true;

		// Get the current capability.
		$this->json['cap'] = $this->cap;

		// Add the section ID.
		$this->json['section'] = $this->section;

		// If the cap is not editable, the inputs should be read-only.
		$this->json['readonly'] = $is_editable ? '' : ' disabled="disabled" readonly="readonly"';

		// Set up the input labels.
		$this->json['label'] = array(
			'cap'   => members_show_human_caps() && members_cap_exists( $this->cap ) ? members_get_cap( $this->cap )->label : $this->cap,
			'grant' => sprintf( esc_html__( 'Grant %s capability', 'members' ), "<code>{$this->cap}</code>" ),
			'deny'  => sprintf( esc_html__( 'Deny %s capability',  'members' ), "<code>{$this->cap}</code>" )
		);

		// Set up the input `name` attributes.
		$this->json['name'] = array(
			'grant' => 'grant-caps[]',
			'deny'  => 'deny-caps[]'
		);

		// Is this a granted or denied cap?
		$this->json['is_granted_cap'] = isset( $this->manager->has_caps[ $this->cap ] ) && $this->manager->has_caps[ $this->cap ];
		$this->json['is_denied_cap']  = isset( $this->manager->has_caps[ $this->cap ] ) && false === $this->manager->has_caps[ $this->cap ];
	}
}
