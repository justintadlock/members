<?php

namespace Members\Role;

use Members\Contracts\Bootable;

class GroupManager implements Bootable {

	protected $groups;

	public function __construct( Groups $groups ) {

		$this->groups = $groups;
	}

	public function boot() {

		// Set up registration hooks.
		add_action( 'init', [ $this, 'register' ], 95 );

		// Register default roles and groups.
		add_action( 'members/roles/register', 'registerDefaultGroups',  5 );
	}

	public function register() {

		do_action( 'members/role/register/groups', $this->groups );

		// Back-compat.
		do_action( 'members_register_role_groups' );
	}

	public function registerDefaultGroups( $groups ) {

		$groups->add( 'wordpress', [
			'label'       => esc_html__( 'WordPress', 'members' ),
			'label_count' => _n_noop( 'WordPress %s', 'WordPress %s', 'members' ),
			'roles'       => array_intersect(
				[
					'administrator',
					'editor',
					'author',
					'contributor',
					'subscriber'
				],
				array_keys( App::resolve( 'role/roles' )->all() )
			)
		] );
	}
}
