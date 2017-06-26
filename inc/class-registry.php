<?php
/**
 * Registry class for storing collections of data.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2016, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Members;

/**
 * Base registry class.
 *
 * @since  1.2.0
 * @access public
 */
class Registry {

	/**
	 * Registry instances.
	 *
	 * @since  1.2.0
	 * @access private
	 * @var    array
	 */
	private static $instances = array();

	/**
	 * Array of objects in the collection.
	 *
	 * @since  1.2.0
	 * @access protected
	 * @var    array
	 */
	protected $collection = array();

	/**
	 * Constructor method.
	 *
	 * @since  1.2.0
	 * @access protected
	 * @return void
	 */
	protected function __construct() {}

	/**
	 * Lock down `__clone()`.
	 *
	 * @since  1.2.0
	 * @access private
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Lock down `__wakeup()`.
	 *
	 * @since  1.2.0
	 * @access private
	 * @return void
	 */
	private function __wakeup() {}

	/**
	 * Register a new cap object
	 *
	 * @since  1.2.0
	 * @access public
	 * @param  string  $name
	 * @param  object  $value
	 * @return void
	 */
	public function register( string $name, $value ) {

		if ( ! $this->exists( $name ) )
			$this->collection[ $name ] = $value;
	}

	/**
	 * Unregisters a cap object.
	 *
	 * @since  1.2.0
	 * @access public
	 * @param  string  $name
	 * @return void
	 */
	public function unregister( string $name ) {

		if ( $this->exists( $name ) )
			unset( $this->collection[ $name ] );
	}

	/**
	 * Checks if a cap exists.
	 *
	 * @since  1.2.0
	 * @access public
	 * @param  string  $name
	 * @return bool
	 */
	public function exists( string $name ) {

		return isset( $this->collection[ $name ] );
	}

	/**
	 * Gets a cap object.
	 *
	 * @since  1.2.0
	 * @access public
	 * @param  string  $name
	 * @return object|bool
	 */
	public function get( string $name ) {

		return $this->exists( $name ) ? $this->collection[ $name ] : false;
	}

	/**
	 * Returns the entire collection.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return array
	 */
	public function get_collection() {

		return $this->collection;
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return object
	 */
	final public static function get_instance( string $name = '' ) {

		if ( ! isset( self::$instances[ $name ] ) )
			self::$instances[ $name ] = new static();

		return self::$instances[ $name ];
	}
}
