<?php

namespace Members\Role;

use Members\Contracts\Bootable;

class RoleManager implements Bootable {

	protected $roles;

	protected $wp_roles;

	public function __construct( Roles $roles ) {

		$this->roles = $roles;
	}

	public function boot() {

		// Set up registration hooks.
		add_action( 'wp_roles_init', [ $this, 'register'  ], 95 );

		// Register default roles and groups.
		add_action( 'members/roles/register', 'registerDefaults',  5 );
	}

	public function register( $wp_roles ) {

		$this->wp_roles = $wp_roles;

		do_action( 'members/role/register/roles', $this->roles );

		// Back-compat.
		do_action( 'members_register_roles' );
	}

	public function registerDefaults( $roles ) {

		foreach ( $wp_roles->roles as $name => $object ) {

			$roles->add( $name, [
				'label' => $object['name'],
				'caps'  => $object['capabilities']
			] );
		}

		// Unset any roles that were registered previously but are not
		// currently available.
		foreach ( $roles->all() as $role ) {

			if ( ! isset( $wp_roles->roles[ $role->name() ] ) ) {

				$roles->remove( $role->name() );
			}
		}
	}
}
