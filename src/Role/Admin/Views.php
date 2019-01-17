<?php

namespace Members\Role\Admin;

use Members\Tools\Collection;

class Views implements Collection {

	public function add( $name, array $args = [] ) {

		parent::add( $name, new View( $name, $args ) );
	}
}
