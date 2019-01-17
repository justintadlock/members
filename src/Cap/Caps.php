<?php

namespace Members\Cap;

use Members\Tools\Collection;

class Caps extends Collection {

	public function add( $name, $value ) {

		parent::add( $name, new Cap( $name, $value ) );
	}
}
