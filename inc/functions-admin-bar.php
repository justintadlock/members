<?php
/**
 * Functions for modifying the WordPress admin bar.
 *
 * @package    Members
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Hook the members admin bar to 'wp_before_admin_bar_render'.
add_action( 'wp_before_admin_bar_render', 'members_admin_bar' );

/**
 * Adds new menu items to the WordPress admin bar.
 *
 * @since  0.2.0
 * @access public
 * @global object  $wp_admin_bar
 * @return void
 */
function members_admin_bar() {
	global $wp_admin_bar;

	// Check if the current user can 'create_roles'.
	if ( current_user_can( 'create_roles' ) ) {

		// Add a 'Role' menu item as a sub-menu item of the new content menu.
		$wp_admin_bar->add_menu(
			array(
				'id'     => 'members-new-role',
				'parent' => 'new-content',
				'title'  => esc_attr__( 'Role', 'members' ),
				'href'   => esc_url( members_get_new_role_url() )
			)
		);
	}
}
