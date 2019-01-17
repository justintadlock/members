<?php
/**
 * Role management.  This is the base class for the Roles and Edit Role screens.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2018, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-3.0.html
 */

namespace Members\Admin\Role;

use Members\Contracts\Bootable;

/**
 * Role management class.
 *
 * @since  3.0.0
 * @access public
 */
class Manage implements Bootable {

	/**
	 * Name of the page we've created.
	 *
	 * @since  3.0.0
	 * @access private
	 * @var    string
	 */
	private $page = '';

	/**
	 * The page object to show.
	 *
	 * @since  3.0.0
	 * @access public
	 * @var    object
	 */
	private $page_obj = '';

	/**
	 * Sets up our initial actions.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return void
	 */
	public function boot() {

		// If the role manager is active.
		if ( members_role_manager_enabled() ) {

			add_action( 'admin_menu', [ $this, 'addAdminPage' ] );
		}
	}

	/**
	 * Adds the roles page to the admin.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return void
	 */
	public function addAdminPage() {

		// The "Roles" page should be shown for anyone that has the
		// 'list_roles', 'edit_roles', or 'delete_roles' caps, so we're
		// checking against all three.
		$edit_roles_cap = 'list_roles';

		if ( current_user_can( 'edit_roles' ) ) {
			$edit_roles_cap = 'edit_roles';
		} elseif ( current_user_can( 'delete_roles' ) ) {
			$edit_roles_cap = 'delete_roles';
		}

		// Get the page title.
		$title = esc_html__( 'Roles', 'members' );

		if ( isset( $_GET['action'] ) && 'edit' === $_GET['action'] && isset( $_GET['role'] ) ) {
			$title = esc_html__( 'Edit Role', 'members' );
		}

		// Create the Manage Roles page.
		$this->page = add_users_page(
			$title,
			esc_html__( 'Roles', 'members' ),
			$edit_roles_cap,
			'roles',
			[ $this, 'page' ]
		);

		// Let's roll if we have a page.
		if ( $this->page ) {

			$class = Roles::class;

			// If viewing the edit role page.
			if ( isset( $_REQUEST['action'] ) && 'edit' === $_REQUEST['action'] && current_user_can( 'edit_roles' ) ) {
				$class = Edit::class;
			}

			$this->page_obj = new $class( $this->page );

			$this->page_obj->boot();
		}
	}

	/**
	 * Outputs the page.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return void
	 */
	public function page() {

		if ( method_exists( $this->page_obj, 'page' ) )
			$this->page_obj->page();
	}
}
