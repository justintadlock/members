<?php
/**
 * Creates a new role object.  This is an extension of the core `get_role()` functionality.  It's
 * just been beefed up a bit to provide more useful info for our plugin.
 *
 * @package    Members
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2018, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Members\Role;

use Members\Proxies\App;

class Role {

	protected $name = '';
	protected $label = '';

	protected $group = '';

	protected $caps = [];

	protected $granted_caps = [];

	protected $denied_caps = [];

	/**
	 * Return the role string in attempts to use the object as a string.
	 *
	 * Important! Need to keep this for back-compat when passing to some
	 * filters that expect a string name.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return string
	 */
	public function __toString() {
		return $this->name();
	}

	/**
	 * Creates a new role object.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $name
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( $name, $args = array() ) {

		foreach ( array_keys( get_object_vars( $this ) ) as $key ) {

			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}

		$this->name = sanitize_name( $name );

		if ( $this->caps ) {

			// Validate cap values as booleans in case they are stored as strings.
			$this->caps = array_map( 'members_validate_boolean', $this->caps );

			// Get granted and denied caps.
			$this->granted_caps = array_keys( $this->caps, true  );
			$this->denied_caps  = array_keys( $this->caps, false );

			// Remove user levels from granted/denied caps.
			$this->granted_caps = \Members\Cap\remove_levels( $this->granted_caps );
			$this->denied_caps  = \Members\Cap\remove_levels( $this->denied_caps  );

			// Remove hidden caps from granted/denied caps.
			$this->granted_caps = \Members\Cap\remove_hidden_caps( $this->granted_caps );
			$this->denied_caps  = \Members\Cap\remove_hidden_caps( $this->denied_caps  );
		}
	}

	public function name() {
		return $this->name;
	}

	public function label() {
		return $this->label ?: $this->name();
	}

	public function group() {
		return App::resolve( Groups::class )->get( $this->group );
	}

	public function hasCaps() {
		return 0 < $this->grantedCapCount();
	}

	public function caps() {
		return $this->caps;
	}

	public function grantedCaps() {
		return $this->granted_caps;
	}

	public function deniedCaps() {
		return $this->denied_caps;
	}

	public function grantedCapCount() {
		return count( $this->grantedCaps() );
	}

	public function deniedCapCount() {
		return count( $this->deniedCaps() );
	}
}
