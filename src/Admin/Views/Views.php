<?php

namespace Members\Admin\Views;

use Members\Tools\Collection;

class Views extends Collection {

	public function add( $name, array $args = [] ) {

		parent::add( $name, new View( $name, $args ) );
	}
}
