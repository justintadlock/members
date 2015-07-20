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

class Members_Role {

	public $role = '';
	public $name = '';

	public $is_editable = false;

	public $user_count = 0;
	public $cap_count = 0;
	public $has_users = false;
	public $has_caps = false;

	public $caps = array();
	public $granted_caps = array();
	public $denied_caps = array();

	public function __construct( $role ) {
		global $wp_roles;

		$_role = get_role( $role );

		$this->role = $_role->name;

		if ( isset( $wp_roles->role_names[ $role ] ) )
			$this->name = $wp_roles->role_names[ $role ];

		$this->is_editable = array_key_exists( $role, apply_filters( 'editable_roles', $wp_roles->role_names ) );

		foreach ( $_role->capabilities as $cap => $grant ) {

			if ( true === $grant )
				$this->granted_caps[] = $cap;

			elseif ( false === $grant )
				$this->denied_caps[] = $cap;
		}

		$this->granted_caps = members_remove_old_levels( $this->granted_caps );
		$this->denied_caps  = members_remove_old_levels( $this->denied_caps );

		$this->cap_count = count( $this->granted_caps );

		$this->has_caps = 0 < $this->cap_count;

		$this->user_count = members_get_role_user_count( $role );

		$this->has_users = 0 < $this->user_count;
	}
}
