<?php
/**
 * Capability section class for use in the edit capabilities tabs.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2018, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Members\Admin;

/**
 * Cap section class.
 *
 * @since  2.0.0
 * @access public
 */
final class Cap_Section {

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
	 * ID of the section.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $section = '';

	/**
	 * Dashicons icon for the section.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $icon = 'dashicons-admin-generic';

	/**
	 * Label for the section.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $label = '';

	/**
	 * Array of data to pass as a json object to the Underscore template.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    array
	 */
	public $json = array();

	/**
	 * Creates a new section object.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  object  $manager
	 * @param  string  $section
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( $manager, $section, $args = array() ) {

		foreach ( array_keys( get_object_vars( $this ) ) as $key ) {

			if ( isset( $args[ $key ] ) )
				$this->$key = $args[ $key ];
		}

		$this->manager = $manager;
		$this->section = $section;
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

		// Set up the ID and class.
		$this->json['id']    = $this->section;
		$this->json['class'] = 'members-tab-content' . ( $is_editable ? ' editable-role' : '' );
	}
}
