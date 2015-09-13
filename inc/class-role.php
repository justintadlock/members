<?php
/**
 * Creates a new role object.  This is an extension of the core `get_role()` functionality.  It's
 * just been beefed up a bit to provide more useful info for our plugin.
 *
 * @package    Members
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Role class.
 *
 * @since  1.0.0
 * @access public
 */
class Members_Role {

	/**
	 * The role/slug.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $slug = '';

	/**
	 * The role name.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $name = '';

	/**
	 * Whether the role can be edited.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    bool
	 */
	public $is_editable = false;

	/**
	 * Whether the role is a core WP role.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    bool
	 */
	public $is_wordpress_role = false;

	/**
	 * Whether the role has caps (granted).
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    bool
	 */
	public $has_caps = false;

	/**
	 * Capability count for the role.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    int
	 */
	public $granted_cap_count = 0;

	/**
	 * Capability count for the role.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    int
	 */
	public $denied_cap_count = 0;

	/**
	 * Array of capabilities that the role has.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $caps = array();

	/**
	 * Array of granted capabilities that the role has.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $granted_caps = array();

	/**
	 * Array of denied capabilities that the role has.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $denied_caps = array();

	/**
	 * Return the role string in attempts to use the object as a string.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function __toString() {
		return $this->slug;
	}

	/**
	 * Creates a new role object.
	 *
	 * @since  1.0.0
	 * @access public
	 * @global object  $wp_roles
	 * @param  string  $role
	 * @return void
	 */
	public function __construct( $role ) {
		global $wp_roles;

		// Get the WP role object.
		$_role = get_role( $role );

		// Set the slug.
		$this->slug = $_role->name;

		// Set the role name.
		if ( isset( $wp_roles->role_names[ $role ] ) )
			$this->name = members_translate_role( $role );

		// Check whether the role is editable.
		$editable_roles    = function_exists( 'get_editable_roles' ) ? get_editable_roles() : apply_filters( 'editable_roles', $wp_roles->roles );
		$this->is_editable = array_key_exists( $role, $editable_roles );

		// Loop through the role's caps.
		foreach ( (array) $_role->capabilities as $cap => $grant ) {

			// Validate any boolean grant/denied in case they are stored as strings.
			$grant = members_validate_boolean( $grant );

			// Add to all caps array.
			$this->caps[ $cap ] = $grant;

			// If a granted cap.
			if ( true === $grant )
				$this->granted_caps[] = $cap;

			// If a denied cap.
			elseif ( false === $grant )
				$this->denied_caps[] = $cap;
		}

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
