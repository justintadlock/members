<?php

final class Members_Admin_Manage_Roles {

	/**
	 * Holds the instances of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance;

	/**
	 * Name of the page we've created.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $page = '';

	public $page_obj = '';

	public $is_edit_page = false;

	/**
	 * Sets up our initial actions.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		// If the role manager is active.
		if ( members_get_setting( 'role_manager' ) )
			add_action( 'admin_menu', array( $this, 'add_admin_page' ) );
	}

	/**
	 * Adds the roles page to the admin.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function add_admin_page() {

		// The "Roles" page should be shown for anyone that has the 'list_roles', 'edit_roles', or
		// 'delete_roles' caps, so we're checking against all three.
		$edit_roles_cap = 'list_roles';

		// If the current user can 'edit_roles'.
		if ( current_user_can( 'edit_roles' ) )
			$edit_roles_cap = 'edit_roles';

		// If the current user can 'delete_roles'.
		elseif ( current_user_can( 'delete_roles' ) )
			$edit_roles_cap = 'delete_roles';

		// Create the Manage Roles page.
		$this->page = add_submenu_page( 'users.php', esc_html__( 'Roles', 'members' ), esc_html__( 'Roles', 'members' ), $edit_roles_cap, 'roles', array( $this, 'page' ) );

		if ( $this->page ) {

			if ( isset( $_REQUEST['action'] ) && 'edit' === $_REQUEST['action'] && current_user_can( 'edit_roles' ) ) {
				$this->page_obj = new Members_Admin_Role_Edit();

				$this->is_edit_page = true;
			} else {
				$this->page_obj = new Members_Admin_Roles();
			}

			//if ( ! $this->is_edit_page )
			//	add_action( 'current_screen', array( $this, 'current_screen' ) );

			add_action( "load-{$this->page}", array( $this, 'load' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		}
	}

	/**
	 * Checks posted data on load and performs actions if needed.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function load() {
		$this->page_obj->load();
	}

	public function enqueue( $hook ) {

		if ( $this->page !== $hook )
			return;

		wp_enqueue_script( 'members-admin', trailingslashit( MEMBERS_URI ) . 'js/admin.js', array( 'jquery' ), '20110525', true );

		wp_enqueue_style( 'members-admin', trailingslashit( MEMBERS_URI ) . 'css/admin.css', false, '20110525', 'screen' );
	}


	/**
	 * Outputs the page.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function page() {
		$this->page_obj->page();
	}

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

Members_Admin_Manage_Roles::get_instance();
