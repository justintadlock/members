<?php

namespace Members\Role;

use Members\Tools\Collection;

class Roles extends Collection {

	public function add( $name, $value ) {

		parent::add( $name, new Role( $name, $value ) );
	}

	public function group( $group ) {

		$roles = [];

		foreach ( $this->all() as $role ) {

			if ( $role->group()->name() === $group ) {

				$roles[ $role->name() ] = $roles[ $role ];
			}
		}

		return $roles;
	}
}
