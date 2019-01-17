<?php

namespace Members\Role\Admin;

class View {

	protected $name = '';

	protected $label_count = '';

	protected $roles = [];

	public function __construct( $name, array $args = [] ) {

	       foreach ( array_keys( get_object_vars( $this ) ) as $key ) {

		       if ( isset( $args[ $key ] ) ) {
			       $this->$key = $args[ $key ];
		       }
	       }

	       $this->name = sanitize_key( $name );
       }

	public function label() {

		return $this->label_count;
	}

	public function roles() {

		return $this->roles;
	}
}
