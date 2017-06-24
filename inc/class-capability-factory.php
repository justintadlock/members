<?php
/**
 * Capability factory.  Stores all registered capabilities.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2016, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Capability factory class.
 *
 * @since  1.2.0
 * @access public
 */
final class Members_Capability_Factory {

	/**
	 * Array of cap objects.
	 *
	 * @since  1.2.0
	 * @access public
	 * @var    array
	 */
	public $caps = array();

	/**
	 * Constructor method.
	 *
	 * @since  1.2.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Register a new cap object
	 *
	 * @since  1.2.0
	 * @access public
	 * @param  string  $name
	 * @param  array   $args
	 * @return void
	 */
	public function register_cap( $name, $args = array() ) {

		if ( ! $this->cap_exists( $name ) ) {

			$cap = new Members_Capability( $name, $args );

			$this->caps[ $cap->name ] = $cap;
		}
	}

	/**
	 * Unregisters a cap object.
	 *
	 * @since  1.2.0
	 * @access public
	 * @param  string  $name
	 * @return void
	 */
	public function unregister_cap( $name ) {

		if ( $this->cap_exists( $name ) )
			unset( $this->caps[ $name ] );
	}

	/**
	 * Checks if a cap exists.
	 *
	 * @since  1.2.0
	 * @access public
	 * @param  string  $name
	 * @return bool
	 */
	public function cap_exists( $name ) {

		return isset( $this->caps[ $name ] );
	}

	/**
	 * Gets a cap object.
	 *
	 * @since  1.2.0
	 * @access public
	 * @param  string  $name
	 * @return object|bool
	 */
	public function get_cap( $name ) {

		return $this->cap_exists( $name ) ? $this->caps[ $name ] : false;
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) )
			$instance = new self;

		return $instance;
	}
}
