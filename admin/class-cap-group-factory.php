<?php
/**
 * Singleton factory class for storying capability group objects.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2016, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Capability group factory class.
 *
 * @since  1.0.0
 * @access public
 */
final class Members_Cap_Group_Factory {

	/**
	 * Array of group objects.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $groups = array();

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Register a new group object
	 *
	 * @see    Members_Cap_Group::__construct()
	 * @since  1.0.0
	 * @access public
	 * @param  string  $name
	 * @param  array   $args
	 * @return void
	 */
	public function register_group( $name, $args = array() ) {

		if ( ! $this->group_exists( $name ) ) {

			$group = new Members_Cap_Group( $name, $args );

			$this->groups[ $group->name ] = $group;
		}
	}

	/**
	 * Unregisters a group object.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $name
	 * @return void
	 */
	public function unregister_group( $name ) {

		if ( $this->group_exists( $name ) )
			unset( $this->groups[ $name ] );
	}

	/**
	 * Checks if a group exists.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $name
	 * @return bool
	 */
	public function group_exists( $name ) {

		return isset( $this->groups[ $name ] );
	}

	/**
	 * Gets a group object.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $name
	 * @return object|bool
	 */
	public function get_group( $name ) {

		return $this->group_exists( $name ) ? $this->groups[ $name ] : false;
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) )
			$instance = new Members_Cap_Group_Factory;

		return $instance;
	}
}
