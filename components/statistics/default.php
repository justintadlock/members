<?php
/**
 * Stats package for the plugin.  This component displays stats for individual roles. On the main stats
 * page, each role is listed.  Each role then has its own stats page, which lists users by month.  Each
 * month should list all of the users that signed up for that particular month for with that role.
 *
 * To view the stats page, a user must have a role with the 'view_stats' capability.
 *
 * @package Members
 * @subpackage Components
 */

/* Add the stats page to the admin. */
add_action( 'admin_menu', 'members_component_add_stats_page' );

/* Additional capabilities required by the component. */
add_filter( 'members_get_capabilities', 'members_component_stats_capabilities' );

/**
 * Adds the stats page to the admin menu.
 *
 * @since 0.2
 */
function members_component_add_stats_page() {
	global $members;

	$members->stats_page = add_submenu_page( 'users.php', __( 'Members Statistics', 'members' ), __( 'Members Stats', 'members' ), 'view_stats', 'stats', 'members_component_stats_page' );
}

/**
 * Loads the stats page.
 *
 * @since 0.2
 */
function members_component_stats_page() {

	$stats = get_option( 'members_statistics' );

	if ( empty( $stats ) )
		members_component_stats_create_initial_stats();

	require_once( MEMBERS_COMPONENTS . '/statistics/statistics.php' );
}

/**
 * Adds additional capabilities required by the stats component.
 *
 * @since 0.2
 */
function members_component_stats_capabilities( $capabilities ) {

	$capabilities['view_stats'] = 'view_stats';

	return $capabilities;
}

add_action( 'user_register', 'update_stats_package' );

function update_stats_package( $user_id ) {

	$stats = get_option( 'members_statistics' );
	$new_user = new WP_User( $user_id );

	if ( is_array( $new_user->roles ) )
		$role = $new_user->roles[0];

	$stats[$role][$new_user->ID] = array(
		'id' => $new_user->ID,
		'role' => $role,
		'date' => $new_user->user_registered,
		'year' => mysql2date( 'Y', $new_user->user_registered ),
		'month' => mysql2date( 'm', $new_user->user_registered ),
		'day' => mysql2date( 'd', $new_user->user_registered ),
	);

	update_option( 'members_statistics', $stats );
}


/**
 * If the stats package was previously unused, this means that prior users stats were
 * not tracked.  So, we're going to create some default stats based on the user registration
 * date and user role.
 *
 * @since 0.2
 */
function members_component_stats_create_initial_stats() {
	global $wp_roles;

	$stats = array();

	foreach ( $wp_roles->role_objects as $key => $role ) {

		$sta = array();

		$search = new WP_User_Search( '', '', $role->name );

		$users = $search->get_results();

		if ( isset( $users ) && is_array( $users ) ) {

			foreach ( $users as $user ) {
				$new_user = new WP_User( $user );

				$sta[$new_user->ID] = array(
					'id' => $new_user->ID,
					'role' => $role->name,
					'date' => $new_user->user_registered,
					'year' => mysql2date( 'Y', $new_user->user_registered ),
					'month' => mysql2date( 'm', $new_user->user_registered ),
					'day' => mysql2date( 'd', $new_user->user_registered ),
				);

			}
		}

		$stats[$role->name] = $sta;
	}

	add_option( 'members_statistics', $stats );

}














?>