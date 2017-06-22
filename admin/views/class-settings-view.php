<?php
/**
 * Base class for creating custom settings views.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2016, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Settings view base class.
 *
 * @since  1.2.0
 * @access public
 */
class Members_Settings_View {

	/**
	 * Name/ID for the group.
	 *
	 * @since  1.2.0
	 * @access protected
	 * @var    string
	 */
	public $name = '';

	/**
	 * Internationalized text label for the group.
	 *
	 * @since  1.2.0
	 * @access protected
	 * @var    string
	 */
	public $label = '';

	/**
	 * Priority (order) the control should be output.
	 *
	 * @since  1.2.0
	 * @access public
	 * @var    int
	 */
	public $priority = 10;

	/**
	 * A user role capability required to show the control.
	 *
	 * @since  1.2.0
	 * @access public
	 * @var    string|array
	 */
	public $capability = 'manage_options';

	/**
	 * Magic method to use in case someone tries to output the object as a string.
	 * We'll just return the name.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return string
	 */
	public function __toString() {
		return $this->name;
	}

	/**
	 * Register a new object.
	 *
	 * @since  1.2.0
	 * @access public
	 * @param  string  $name
	 * @param  array   $args  {
	 *     @type string  $label        Internationalized text label.
	 *     @type string  $icon         Dashicon icon in the form of `dashicons-icon-name`.
	 *     @type string  $callback     Callback function for outputting the content for the view.
	 * }
	 * @return void
	 */
	public function __construct( $name, $args = array() ) {

		foreach ( array_keys( get_object_vars( $this ) ) as $key ) {

			if ( isset( $args[ $key ] ) )
				$this->$key = $args[ $key ];
		}

		$this->name = sanitize_key( $name );
	}

	/**
	 * Enqueue scripts/styles for the control.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {}

	/**
	 * Register settings for the view.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return void
	 */
	public function register_settings() {}

	/**
	 * Add help tabs for the view.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return void
	 */
	public function add_help_tabs() {}

	/**
	 * Output the content for the view.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return void
	 */
	public function template() {}

	/**
	 * Checks if the control should be allowed at all.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return bool
	 */
	public function check_capabilities() {

		if ( $this->capability && ! call_user_func_array( 'current_user_can', (array) $this->capability ) )
			return false;

		return true;
	}
}
