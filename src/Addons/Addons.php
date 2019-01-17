<?php

namespace Members\Addons;

use Members\Tools\Collection;

class Addons extends Collection {

	public function add( $name, $value ) {

		parent::add( $name, new Addon( $name, $value ) );
	}
}
