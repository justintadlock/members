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
 * @since      0.2.0
 * @deprecated 1.0.0
 */
function members_admin_contextual_help() {}

/**
 * Help sidebar for all of the help tabs.
 *
 * @since  1.0.0
 * @access public
 * @return string
 */
function members_get_help_sidebar_text() {

	// Get docs and help links.
	$docs_link = sprintf( '<li><a href="https://github.com/justintadlock/members/blob/master/readme.md">%s</a></li>', esc_html__( 'Documentation',  'members' ) );
	$help_link = sprintf( '<li><a href="http://themehybrid.com/board/topics">%s</a></li>',                            esc_html__( 'Support Forums', 'members' ) );

	// Return the text.
	return sprintf(
		'<p><strong>%s</strong></p><ul>%s%s</ul>',
		esc_html__( 'For more information:', 'members' ),
		$docs_link,
		$help_link
	);
}

function members_get_edit_role_help_overview_args() {

	return array(
		'id'       => 'overview',
		'title'    => esc_html__( 'Overview', 'members' ),
		'callback' => 'members_edit_role_help_tab_overview'
	);
}

function members_get_edit_role_help_role_name_args() {

	return array(
		'id'       => 'role-name',
		'title'    => esc_html__( 'Role Name', 'members' ),
		'callback' => 'members_edit_role_help_tab_role_name'
	);
}

function members_get_edit_role_help_edit_caps_args() {

	return array(
		'id'       => 'edit-capabilities',
		'title'    => esc_html__( 'Edit Capabilities', 'members' ),
		'callback' => 'members_edit_role_help_tab_capabilities'
	);
}

function members_get_edit_role_help_custom_cap_args() {

	return array(
		'id'       => 'custom-capability',
		'title'    => esc_html__( 'Custom Capability', 'members' ),
		'callback' => 'members_edit_role_help_tab_custom_cap'
	);
}

function members_edit_role_help_tab_overview() { ?>

	<p>
		<?php esc_html_e( 'This screen allows you to edit an individual role and its capabilities.', 'members' ); ?>
	<p>
<?php }

function members_edit_role_help_tab_role_name() { ?>

	<p>
		<?php esc_html_e( 'The role name field allows you to enter a human-readable name for your role.', 'members' ); ?>
	</p>

	<p>
		<?php esc_html_e( 'The machine-readable version of the role appears below the name field, which you can edit. This can only have lowercase letters, numbers, or underscores.', 'members' ); ?>
	</p>
<?php }

function members_edit_role_help_tab_capabilities() { ?>

	<p>
		<?php esc_html_e( 'The capabilities edit box is made up of tabs that separate capabilities into groups. You may take the following actions for each capability:', 'members' ); ?>
	</p>

	<ul>
		<li><?php _e( '<strong>Grant</strong> allows you to grant the role the capability.', 'members' ); ?></li>
		<li><?php _e( '<strong>Deny</strong> allows you to deny the capability for the role. This is typically only useful when building a site with multiple roles per user where you might want to explicitly deny a capability.', 'members' ); ?></li>
		<li><?php esc_html_e( 'You may also opt to neither grant nor deny the role a capability.', 'members' ); ?></li>
	</ul>
<?php }

function members_edit_role_help_tab_custom_cap() { ?>

	<p>
		<?php esc_html_e( 'The custom capability box allows you to create a custom capability for the role. After hitting the Add New button, it will add the capability to the Custom tab in the Edit Capabilities box.', 'members' ); ?>
	</p>
<?php }

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
