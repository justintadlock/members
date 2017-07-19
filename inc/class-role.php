<?php
/**
 * Creates a new role object.  This is an extension of the core `get_role()` functionality.  It's
 * just been beefed up a bit to provide more useful info for our plugin.
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
 * Role class.
 *
 * @since  2.0.0
 * @access public
 */
class Role {

	/**
	 * The role name.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $name = '';

	/**
	 * The role label.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $label = '';

	/**
	 * The group the role belongs to.
	 *
	 * @see    Members\Role_Group
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $group = '';

	/**
	 * Whether the role has caps (granted).
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    bool
	 */
	public $has_caps = false;

	/**
	 * Capability count for the role.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    int
	 */
	public $granted_cap_count = 0;

	/**
	 * Capability count for the role.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    int
	 */
	public $denied_cap_count = 0;

	/**
	 * Array of capabilities that the role has in the form of `array( $cap => $bool )`.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    array
	 */
	public $caps = array();

	/**
	 * Array of granted capabilities that the role has.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    array
	 */
	public $granted_caps = array();

	/**
	 * Array of denied capabilities that the role has.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    array
	 */
	public $denied_caps = array();

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
	 * @param  string  $role
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( $name, $args = array() ) {

		foreach ( array_keys( get_object_vars( $this ) ) as $key ) {

			if ( isset( $args[ $key ] ) )
				$this->$key = $args[ $key ];
		}

		$this->name = members_sanitize_role( $name );

		if ( $this->caps ) {

			// Validate cap values as booleans in case they are stored as strings.
			$this->caps = array_map( 'members_validate_boolean', $this->caps );

			// Get granted and denied caps.
			$this->granted_caps = array_keys( $this->caps, true  );
			$this->denied_caps  = array_keys( $this->caps, false );

			// Remove user levels from granted/denied caps.
			$this->granted_caps = members_remove_old_levels( $this->granted_caps );
			$this->denied_caps  = members_remove_old_levels( $this->denied_caps  );

			// Remove hidden caps from granted/denied caps.
			$this->granted_caps = members_remove_hidden_caps( $this->granted_caps );
			$this->denied_caps  = members_remove_hidden_caps( $this->denied_caps  );

			// Set the cap count.
			$this->granted_cap_count = count( $this->granted_caps );
			$this->denied_cap_count  = count( $this->denied_caps  );

			// Check if we have caps.
			$this->has_caps = 0 < $this->granted_cap_count;
		}
	}
}
