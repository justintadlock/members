<?php
/**
 * Class for handling a role group object.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2018, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Members\Role;

/**
 * Role group object class.
 *
 * @since  2.0.0
 * @access public
 */
class Group {

	protected $name = '';
	protected $label = '';

	/**
	 * Internationalized text label for the group + the count in the form of
	 * `_n_noop( 'Singular Name %s', 'Plural Name %s', $textdomain )`
	 *
	 * @since  3.0.0
	 * @access protected
	 * @var    string
	 */
	protected $label_count = '';

	/**
	 * Array of roles that belong to the group.
	 *
	 * @since  3.0.0
	 * @access protected
	 * @var    array
	 */
	protected $roles = [];

	/**
	 * Whether to create a view for the group on the Manage Roles screen.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var    bool
	 */
	protected $show_in_view_list = true;

	/**
	 * Magic method to use in case someone tries to output the object as a
	 * string. We'll just return the name.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return string
	 */
	public function __toString() {
		return $this->name();
	}

	/**
	 * Register a new object.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  string  $name
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( $name, array $args = [] ) {

		foreach ( array_keys( get_object_vars( $this ) ) as $key ) {

			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}

		$this->name = sanitize_key( $name );

		if ( isset( $args['label'] ) ) {
			$this->labels['label'] = $args['label'];
		}

		if ( isset( $args['label_count'] ) ) {
			$this->labels['count'] = $args['label_count'];
		}

	}

	public function name() {

		return $this->name;
	}

	public function option( $name = '' ) {

		return isset( $this->args[ $name ] ) ? $this->args[ $name ] : '';
	}

	public function label( $name = '' ) {

		return isset( $this->labels[ $name ] ) ? $this->labels[ $name ] : '';
	}

	public function roles() {

		if ( ! $this->roles ) {

			$this->roles = array_unique( array_merge(
				$this->roles,
				array_keys( App::resolve( 'roles' )->group( $this->name ) )
			) );
		}

		return $this->roles;
	}
}
