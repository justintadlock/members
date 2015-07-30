<?php
/**
 * Handles the admin setup and functions for the plugin.
 *
 * @package Members
 * @subpackage Admin
 */


add_action( 'current_screen', 'members_current_screen', 5 );

function members_current_screen( $screen ) {

	if ( 'users_page_capabilities' === $screen->id )
		$screen->add_option( 'per_page', array( 'default' => 30 ) );
}

add_filter( 'manage_users_page_capabilities_columns', 'members_manage_capabilities_columns', 5 );

function members_manage_capabilities_columns( $columns ) {

	$columns = array(
		'cb'     => '<input type="checkbox" />',
		'title'  => esc_html__( 'Capability',    'members' ),
		'roles'  => esc_html__( 'Roles',         'members' ),
	);

	return apply_filters( 'members_manage_capabilities_columns', $columns );
}

/* Set up the administration functionality. */
add_action( 'admin_menu', 'members_admin_setup' );

/**
 * Sets up any functionality needed in the admin.
 *
 * @since 0.2.0
 */
function members_admin_setup() {
	global $members;

	/* Add contextual help to the "Help" tab for the plugin's pages in the admin. */
	add_filter( 'contextual_help', 'members_admin_contextual_help', 10, 2 );

	/* If the role manager feature is active, add its admin pages. */
	if ( members_get_setting( 'role_manager' ) ) {

		/* Create the New Role page. */
		$members->new_roles_page = add_submenu_page( 'users.php', esc_attr__( 'Add New Role', 'members' ), esc_attr__( 'Add New Role', 'members' ), 'create_roles', 'role-new', 'members_new_role_page' );
	}

	//if ( current_user_can( 'delete_capabilities' ) )
	//	$members->edit_capabilities_page = add_submenu_page( 'users.php', esc_attr__( 'Capabilities', 'members' ), esc_attr__( 'Capabilities', 'members' ), 'edit_roles', 'capabilities', 'members_edit_capabilities_page' );

		$members->new_capabilities_page = add_submenu_page( 'users.php', esc_attr__( 'Add New Capability', 'members' ), esc_attr__( 'Add New Capability', 'members' ), 'edit_roles', 'capability-new', 'members_new_capability_page' );


	/* Load post meta boxes on the post editing screen. */
	add_action( 'load-post.php', 'members_admin_load_post_meta_boxes' );
	add_action( 'load-post-new.php', 'members_admin_load_post_meta_boxes' );

	/* Load stylesheets and scripts for our custom admin pages. */
	add_action( 'admin_enqueue_scripts', 'members_admin_enqueue_style' );
	add_action( 'admin_enqueue_scripts', 'members_admin_enqueue_scripts' );
}

/**
 * Loads the admin stylesheet for the required pages based off the $hook_suffix parameter.
 *
 * @since 0.2.0
 * @param string $hook_suffix The hook for the current page in the admin.
 */
function members_admin_enqueue_style( $hook_suffix ) {

	$pages = array(
		'users_page_roles',
		'users_page_role-new',
		'users_page_capabilities'
	);

	if ( in_array( $hook_suffix, $pages ) )
		wp_enqueue_style( 'members-admin', trailingslashit( MEMBERS_URI ) . 'css/admin.css', false, '20110525', 'screen' );
}

/**
 * Loads the admin JavaScript for the required pages based off the $hook_suffix parameter.
 *
 * @since 0.2.0
 * @param string $hook_suffix The hook for the current page in the admin.
 */
function members_admin_enqueue_scripts( $hook_suffix ) {

	$pages = array(
		'users_page_roles',
		'users_page_role-new'
	);

	if ( in_array( $hook_suffix, $pages ) )
		wp_enqueue_script( 'members-admin', trailingslashit( MEMBERS_URI ) . 'js/admin.js', array( 'jquery' ), '20110525', true );
}

/**
 * Loads meta boxes for the post editing screen.
 *
 * @since 0.2.0
 */
function members_admin_load_post_meta_boxes() {

	/* If the content permissions component is active, load its post meta box. */
	if ( members_get_setting( 'content_permissions' ) )
		require_once( MEMBERS_ADMIN . 'meta-box-post-content-permissions.php' );
}

/**
 * Loads the role manager main page (Roles).
 *
 * @since 0.1.0
 */
function members_edit_roles_page() {
	require_once( MEMBERS_ADMIN . 'roles.php' );
}

function members_edit_capabilities_page() {
	require_once( members_plugin()->admin_dir . 'page-capabilities.php' );
}

/**
 * Loads the New Role page.
 *
 * @since 0.1.0
 */
function members_new_role_page() {
	require_once( MEMBERS_ADMIN . 'role-new.php' );
}


/**
 * Loads the New Role page.
 *
 * @since 1.0.0
 */
function members_new_capability_page() {
	require_once( members_plugin()->admin_dir . 'page-capability-new.php' );
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
		$text .= '<li><a href="' . MEMBERS_URI . 'docs/readme.html">' . __( 'Documentation', 'members' ) . '</a></li>';
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
		$text .= '<li><a href="' . MEMBERS_URI . 'docs/readme.html">' . __( 'Documentation', 'members' ) . '</a></li>';
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
		$text .= '<li><a href="' . MEMBERS_URI . 'docs/readme.html">' . __( 'Documentation', 'members' ) . '</a></li>';
		$text .= '<li><a href="http://themehybrid.com/support">' . __( 'Support Forums', 'members' ) . '</a></li>';
		$text .= '</ul>';
	}

	/* Return the contextual help text. */
	return $text;
}

/**
 * Message to show when a single role has been deleted.
 *
 * @since 0.1.0
 */
function members_message_role_deleted() {
	members_admin_message( '', __( 'Role deleted.', 'members' ) );
}

/**
 * Message to show when multiple roles have been deleted (bulk delete).
 *
 * @since 0.1.0
 */
function members_message_roles_deleted() {
	members_admin_message( '', __( 'Selected roles deleted.', 'members' ) );
}

/**
 * A function for displaying messages in the admin.  It will wrap the message in the appropriate <div> with the
 * custom class entered.  The updated class will be added if no $class is given.
 *
 * @since 0.1.0
 * @param $class string Class the <div> should have.
 * @param $message string The text that should be displayed.
 */
function members_admin_message( $class = 'updated', $message = '' ) {

	echo '<div class="' . ( !empty( $class ) ? esc_attr( $class ) : 'updated' ) . '"><p><strong>' . $message . '</strong></p></div>';
}

/**
 * Members plugin nonce function.  This is to help with securely making sure forms have been processed
 * from the correct place.
 *
 * @since 0.1.0
 * @param $action string Additional action to add to the nonce.
 */
function members_get_nonce( $action = '' ) {
	if ( $action )
		return "members-component-action_{$action}";
	else
		return "members-plugin";
}

/**
 * Function for safely deleting a role and transferring the deleted role's users to the default role.  Note that
 * this function can be extremely intensive.  Whenever a role is deleted, it's best for the site admin to assign
 * the user's of the role to a different role beforehand.
 *
 * @since 0.2.0
 * @param string $role The name of the role to delete.
 */
function members_delete_role( $role ) {

	/* Get the default role. */
	$default_role = get_option( 'default_role' );

	/* Don't delete the default role. Site admins should change the default before attempting to delete the role. */
	if ( $role == $default_role )
		return;

	/* Get all users with the role to be deleted. */
	$users = get_users( array( 'role' => $role ) );

	/* Check if there are any users with the role we're deleting. */
	if ( is_array( $users ) ) {

		/* If users are found, loop through them. */
		foreach ( $users as $user ) {

			/* Create a new user object. */
			$new_user = new WP_User( $user->ID );

			/* If the user has the role, remove it and set the default. Do we need this check? */
			if ( $new_user->has_cap( $role ) ) {
				$new_user->remove_role( $role );
				$new_user->set_role( $default_role );
			}
		}
	}

	/* Remove the role. */
	remove_role( $role );
}

/**
 * Returns an array of all the user meta keys in the $wpdb->usermeta table.
 *
 * @since 0.2.0
 * @return array $keys The user meta keys.
 */
function members_get_user_meta_keys() {
	global $wpdb;

	$keys = $wpdb->get_col( "SELECT meta_key FROM $wpdb->usermeta GROUP BY meta_key ORDER BY meta_key" );

	return $keys;
}
