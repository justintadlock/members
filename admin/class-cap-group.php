<?php

final class Members_Cap_Group {

	protected $args = array();

	/**
	 * Magic method for getting object properties.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $property
	 * @return mixed
	 */
	public function __get( $property ) {

		return isset( $this->$property ) ? $this->args[ $property ] : null;
	}

	/**
	 * Magic method for setting object properties.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $property
	 * @param  mixed   $value
	 * @return void
	 */
	public function __set( $property, $value ) {

		if ( isset( $this->$property ) )
			$this->args[ $property ] = $value;
	}

	/**
	 * Magic method for checking if a property is set.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $property
	 * @return bool
	 */
	public function __isset( $property ) {

		return isset( $this->args[ $property ] );
	}

	/**
	 * Don't allow properties to be unset.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $property
	 * @return void
	 */
	public function __unset( $property ) {}

	/**
	 * Magic method to use in case someone tries to output the object as a string.
	 * We'll just return the name.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function __toString() {
		return $this->name;
	}

	/**
	 * Register a new object.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $name
	 * @param  array   $args  {
	 *     @type string  $label
	 *     @type string  $icon
	 *     @type array   $caps
	 * }
	 * @return void
	 */
	public function __construct( $name, $args = array() ) {

		$name = sanitize_key( $name );

		$defaults = array(
			'label'       => '',
			'icon'        => '',
			'caps'        => array( 'read' ),
			'merge_added' => true,
			'diff_added'  => false,
		);

		$this->args = wp_parse_args( $args, $defaults );

		$this->args['name'] = $name;
	}
}
