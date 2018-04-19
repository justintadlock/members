<?php
/**
 * Registry class for storing collections of data.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2018, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Members;

/**
 * Base registry class.
 *
 * @since  2.0.0
 * @access public
 */
class Registry {

	/**
	 * Registry instances.
	 *
	 * @since  2.0.0
	 * @access private
	 * @var    array
	 */
	private static $instances = array();

	/**
	 * Array of items in the collection.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var    array
	 */
	protected $collection = array();

	/**
	 * Constructor method.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @return void
	 */
	protected function __construct() {}

	/**
	 * Lock down `__clone()`.
	 *
	 * @since  2.0.0
	 * @access private
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Lock down `__wakeup()`.
	 *
	 * @since  2.0.0
	 * @access private
	 * @return void
	 */
	private function __wakeup() {}

	/**
	 * Register an item.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  string  $name
	 * @param  mixed   $value
	 * @return void
	 */
	public function register( $name, $value ) {

		if ( ! $this->exists( $name ) )
			$this->collection[ $name ] = $value;
	}

	/**
	 * Unregisters an item.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  string  $name
	 * @return void
	 */
	public function unregister( $name ) {

		if ( $this->exists( $name ) )
			unset( $this->collection[ $name ] );
	}

	/**
	 * Checks if an item exists.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  string  $name
	 * @return bool
	 */
	public function exists( $name ) {

		return isset( $this->collection[ $name ] );
	}

	/**
	 * Returns an item.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  string  $name
	 * @return mixed
	 */
	public function get( $name ) {

		return $this->exists( $name ) ? $this->collection[ $name ] : false;
	}

	/**
	 * Returns the entire collection.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return array
	 */
	public function get_collection() {

		return $this->collection;
	}

	/**
	 * Returns the instance.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return object
	 */
	final public static function get_instance( $name = '' ) {

		if ( ! isset( self::$instances[ $name ] ) )
			self::$instances[ $name ] = new static();

		return self::$instances[ $name ];
	}
}
