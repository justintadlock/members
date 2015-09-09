<?php
/**
 * General admin functionality.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Add contextual help to the "Help" tab for the plugin's pages in the admin.
add_filter( 'contextual_help', 'members_admin_contextual_help', 10, 2 );

# Register scripts/styles.
add_action( 'admin_enqueue_scripts', 'members_admin_register_scripts', 0 );
add_action( 'admin_enqueue_scripts', 'members_admin_register_styles',  0 );

# Custom manage users columns.
add_filter( 'manage_users_columns',       'members_manage_users_columns'              );
add_filter( 'manage_users_custom_column', 'members_manage_users_custom_column', 10, 3 );

/**
 * Get an Underscore JS template.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $name
 * @return bool
 */
function members_get_underscore_template( $name ) {
	require_once( members_plugin()->admin_dir . "tmpl/{$name}.php" );
}

/**
 * Registers custom plugin scripts.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function members_admin_register_scripts() {

	wp_register_script( 'members-settings',  members_plugin()->js_uri . 'settings.js',  array( 'jquery'  ), '', true );
	wp_register_script( 'members-edit-role', members_plugin()->js_uri . 'edit-role.js', array( 'postbox', 'wp-util' ), '', true );

	// Localize our script with some text we want to pass in.
	$i18n = array(
		'button_role_edit' => esc_html__( 'Edit',                'members' ),
		'button_role_ok'   => esc_html__( 'OK',                  'members' ),
		'label_grant_cap'  => esc_html__( 'Grant %s capability', 'members' ),
		'label_deny_cap'   => esc_html__( 'Deny %s capability',  'members' ),
		'ays_delete_role'  => esc_html__( 'Are you sure you want to delete this role? This is a permanent action and cannot be undone.', 'members' )
	);

	wp_localize_script( 'members-edit-role', 'members_i18n', $i18n );
}

/**
 * Registers custom plugin scripts.
 *
 * @since  1.0.0
 * @access public
 * @return void
 */
function members_admin_register_styles() {
	wp_register_style( 'members-admin', members_plugin()->css_uri . 'admin.css' );
}

/**
 * Adds custom contextual help on the plugin's admin screens.  This is the text shown under the "Help" tab.
 *
 * @since 0.2.0
 */
function members_admin_contextual_help( $text, $screen ) {

	/* Text shown on the "Members Settings" screen in the admin. */
	if ( 'settings_page_members-settings' == $screen ) {
		$text = '';

		$text .= '<p>' . __( '<strong>Role Manager:</strong> This feature allows you to manage roles on your site by giving you the ability to create, edit, and delete any role. Note that changes to roles do not change settings for the Members plugin. You are literally changing data in your WordPress database. This plugin feature merely provides an interface for you to make these changes.', 'members' ) . '</p>';
		$text .= '<p>' . __( "<strong>Content Permissions:</strong> This feature adds a meta box to the post edit screen that allows you to grant permissions for who can read the post content based on the user's role. Only users of roles with the <code>restrict_content</code> capability will be able to use this component.", 'members' ) . '</p>';
		$text .= '<p>' . __( "<strong>Sidebar Widgets:</strong> This feature creates additional widgets for use in your theme's sidebars. You can access them by clicking Widgets in the menu.", 'members' ) . '</p>';
		$text .= '<p>' . __( '<strong>Private Site:</strong> This feature allows you to redirect all users who are not logged into the site to the login page, creating an entirely private site. You may also replace your feed content with a custom error message.', 'members' ) . '</p>';

		$text .= '<p><strong>' . __( 'For more information:', 'members' ) . '</strong></p>';

		$text .= '<ul>';
		$text .= '<li><a href="' . members_plugin()->dir_uri . 'docs/readme.html">' . __( 'Documentation', 'members' ) . '</a></li>';
		$text .= '<li><a href="http://themehybrid.com/support">' . __( 'Support Forums', 'members' ) . '</a></li>';
		$text .= '</ul>';
	}

	/* Text shown on the "Roles" screens in the admin. */
	elseif ( 'users_page_roles' == $screen ) {
		$text = '';

		/* Text for the "Edit Role" screen. */
		if ( isset( $_GET['action'] ) && 'edit' == $_GET['action'] ) {

			$text .= '<p>' . __( 'This screen allows you to edit the capabilities given to the role. You can tick the checkbox next to a capability to add the capability to the role. You can untick the checkbox next to a capability to remove a capability from the role. You can also add as many custom capabilities as you need in the Custom Capabilities section.', 'members' ) . '</p>';
			$text .= '<p>' . __( 'Capabilities are both powerful and dangerous tools. You should not add or remove a capability to a role unless you understand what permission you are granting or removing.', 'members' ) . '</p>';
		}

		/* Text shown on the main "Roles" screen. */
		else {
			$text .= '<p>' . __( 'This screen lists all the user roles available on this site. Roles are given to users as a way to "group" them. Roles are made up of capabilities (permissions), which decide what functions users of each role can perform on the site. From this screen, you can manage these roles and their capabilities.', 'members' ) . '</p>';
			$text .= '<p>' . __( 'To add a role to a user, click Users in the menu. To create a new role, click the Add New button at the top of the screen or Add New Role under the Users menu.', 'members' ) . '</p>';
		}

		/* Text shown for both the "Roles" and "Edit Role" screen. */
		$text .= '<p><strong>' . __( 'For more information:', 'members' ) . '</strong></p>';

		$text .= '<ul>';
		$text .= '<li><a href="http://justintadlock.com/archives/2009/08/30/users-roles-and-capabilities-in-wordpress">' . __( 'Users, Roles, and Capabilities', 'members' ) . '</a></li>';
		$text .= '<li><a href="' . members_plugin()->dir_uri . 'docs/readme.html">' . __( 'Documentation', 'members' ) . '</a></li>';
		$text .= '<li><a href="http://themehybrid.com/support">' . __( 'Support Forums', 'members' ) . '</a></li>';
		$text .= '</ul>';
	}

	/* Text to show on the "Add New Role" screen in the admin. */
	elseif ( 'users_page_role-new' == $screen || 'users_page_role' == $screen ) {
		$text = '';

		$text .= '<p>' . __( 'This screen allows you to create a new user role for your site. You must input a unique role name and role label. You can also grant capabilities (permissions) to the new role. Capabilities are both powerful and dangerous tools. You should not add a capability to a role unless you understand what permission you are granting.', 'members' ) . '</p>';
		$text .= '<p>' . __( 'To add a role to a user, click Users in the menu. To edit roles, click Roles under the Users menu.', 'members' ) . '</p>';

		$text .= '<p><strong>' . __( 'For more information:', 'members' ) . '</strong></p>';

		$text .= '<ul>';
		$text .= '<li><a href="http://justintadlock.com/archives/2009/08/30/users-roles-and-capabilities-in-wordpress">' . __( 'Users, Roles, and Capabilities', 'members' ) . '</a></li>';
		$text .= '<li><a href="' . members_plugin()->dir_uri . 'docs/readme.html">' . __( 'Documentation', 'members' ) . '</a></li>';
		$text .= '<li><a href="http://themehybrid.com/support">' . __( 'Support Forums', 'members' ) . '</a></li>';
		$text .= '</ul>';
	}

	/* Return the contextual help text. */
	return $text;
}

/**
 * Function for safely deleting a role and transferring the deleted role's users to the default
 * role.  Note that this function can be extremely intensive.  Whenever a role is deleted, it's
 * best for the site admin to assign the user's of the role to a different role beforehand.
 *
 * @since  0.2.0
 * @access public
 * @param  string  $role
 * @return void
 */
function members_delete_role( $role ) {

	// Get the default role.
	$default_role = get_option( 'default_role' );

	// Don't delete the default role. Site admins should change the default before attempting to delete the role.
	if ( $role == $default_role )
		return;

	// Get all users with the role to be deleted.
	$users = get_users( array( 'role' => $role ) );

	// Check if there are any users with the role we're deleting.
	if ( is_array( $users ) ) {

		// If users are found, loop through them.
		foreach ( $users as $user ) {

			// If the user has the role and no other roles, set their role to the default.
			if ( $user->has_cap( $role ) && 1 >= count( $user->roles ) )
				$user->set_role( $default_role );

			// Else, remove the role.
			else if ( $user->has_cap( $role ) )
				$user->remove_role( $role );
		}
	}

	// Remove the role.
	remove_role( $role );
}

/**
 * Returns an array of all the user meta keys in the $wpdb->usermeta table.
 *
 * @since  0.2.0
 * @access public
 * @global object  $wpdb
 * @return array
 */
function members_get_user_meta_keys() {
	global $wpdb;

	return $wpdb->get_col( "SELECT meta_key FROM $wpdb->usermeta GROUP BY meta_key ORDER BY meta_key" );
}

/**
 * Adds custom columns to the `users.php` screen.
 *
 * @since  1.0.0
 * @access public
 * @param  array  $columns
 * @return array
 */
function members_manage_users_columns( $columns ) {

	// If multiple roles per user is not enabled, bail.
	if ( ! members_multiple_user_roles_enabled() )
		return $columns;

	// Unset the core WP `role` column.
	if ( isset( $columns['role'] ) )
		unset( $columns['role'] );

	// Add our new roles column.
	$columns['roles'] = esc_html__( 'Roles', 'members' );

	// Move the core WP `posts` column to the end.
	if ( isset( $columns['posts'] ) ) {
		$p = $columns['posts'];
		unset( $columns['posts'] );
		$columns['posts'] = $p;
	}

	return $columns;
}

/**
 * Handles the output of the roles column on the `users.php` screen.
 *
 * @since  1.0.0
 * @access public
 * @param  string  $output
 * @param  string  $column
 * @param  int     $user_id
 * @global object  $wp_roles
 * @return string
 */
function members_manage_users_custom_column( $output, $column, $user_id ) {
	global $wp_roles;

	if ( 'roles' === $column && members_multiple_user_roles_enabled() ) {

		$user = new WP_User( $user_id );

		$user_roles = array();
		$output = esc_html__( 'None', 'members' );

		if ( is_array( $user->roles ) ) {

			foreach ( $user->roles as $role ) {

				if ( isset( $wp_roles->role_names[ $role ] ) )
					$user_roles[] = translate_user_role( $wp_roles->role_names[ $role ] );
			}

			$output = join( ', ', $user_roles );
		}
	}

	return $output;
}
