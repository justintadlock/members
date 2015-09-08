<?php
/**
 * Add new/custom capability meta box.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Class to handle the new cap meta box on the edit/new role screen.
 *
 * @since  1.0.0
 * @access public
 */
final class Members_Meta_Box_Custom_Cap {

	/**
	 * Holds the instances of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance;

	/**
	 * Adds our methods to the proper hooks.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	protected function __construct() {

		add_action( 'members_load_role_edit', array( $this, 'load' ) );
		add_action( 'members_load_role_new',  array( $this, 'load' ) );
	}

	/**
	 * Runs on the page load hook to hook in the meta boxes.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function load() {

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
	}

	/**
	 * Adds the meta box.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $screen_id
	 * @param  string  $role
	 * @return void
	 */
	public function add_meta_boxes( $screen_id, $role = '' ) {

		// If role isn't editable, bail.
		if ( $role && ! members_is_role_editable( $role ) )
			return;

		// Add the meta box.
		add_meta_box( 'newcapdiv', esc_html__( 'Custom Capability', 'members' ), array( $this, 'meta_box' ), $screen_id, 'side', 'core' );
	}

	/**
	 * Outputs the meta box HTML.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function meta_box() { ?>

		<p>
			<input type="text" id="members-new-cap-field" class="widefat" />
		</p>

		<p>
			<button type="button" class="button-secondary" id="members-add-new-cap"><?php esc_html_e( 'Add New', 'members' ); ?></button>
		</p>
	<?php }

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		if ( ! self::$instance )
			self::$instance = new self;

		return self::$instance;
	}
}

Members_Meta_Box_Custom_Cap::get_instance();
