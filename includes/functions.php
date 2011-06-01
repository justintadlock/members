<?php
/**
 * General functions file for the plugin.
 *
 * @package Members
 * @subpackage Functions
 */

/**
 * Gets a setting from from the plugin settings in the database.
 *
 * @since 0.2.0
 */
function members_get_setting( $option = '' ) {
	global $members;

	if ( !$option )
		return false;

	if ( !isset( $members->settings ) )
		$members->settings = get_option( 'members_settings' );

	if ( !is_array( $members->settings ) || empty( $members->settings[$option] ) )
		return false;

	return $members->settings[$option];
}

/**
 * Counts the number of roles the site has.
 *
 * @since 0.2.0
 */
function members_count_roles() {
	global $wp_roles;

	if ( !empty( $wp_roles->role_names ) )
		return count( $wp_roles->role_names );

	return false;
}

/**
 * Gets all the roles that have users for the site.
 *
 * @since 0.2.0
 */
function members_get_active_roles() {
	global $wp_roles, $members;

	if ( !isset( $members->active_roles ) ) {

		$active = array();

		foreach ( $wp_roles->role_names as $role => $name ) {

			$count = members_get_role_user_count( $role );

			if ( !empty( $count ) )
				$active[$role] = $name;
		}

		$members->active_roles = $active;
	}

	return $members->active_roles;
}

/**
 * Gets all the roles that do not have users for the site.
 *
 * @since 0.2.0
 */
function members_get_inactive_roles() {
	global $wp_roles, $members;

	if ( !isset( $members->inactive_roles ) ) {

		$inactive = array();

		foreach ( $wp_roles->role_names as $role => $name ) {

			$count = members_get_role_user_count( $role );

			if ( empty( $count ) )
				$inactive[$role] = $name;
		}

		$members->inactive_roles = $inactive;
	}

	return $members->inactive_roles;
}

/**
 * Counts the number of users for all roles on the site and returns this as an array.  If the $user_role is input, 
 * the return value will be the count just for that particular role.
 *
 * @todo Use WP's cache API to cache this data.
 *
 * @since 0.2.0
 * @param string $user_role The role to get the user count for.
 */
function members_get_role_user_count( $user_role = '' ) {
	global $members;

	/* If the count is not already set for all roles, let's get it. */
	if ( !isset( $members->role_user_count ) ) {

		$avail_roles = array();

		/* Count users */
		$user_count = count_users();

		/* Loop through the user count by role to get a count of the users with each role. */
		foreach ( $user_count['avail_roles'] as $role => $count )
			$avail_roles[$role] = $count;

		$members->role_user_count = $avail_roles;
	}

	/* If the $user_role parameter wasn't passed into this function, return the array of user counts. */
	if ( empty( $user_role ) )
		return $members->role_user_count;

	/* If the role has no users, we need to set it to '0'. */
	if ( !isset( $members->role_user_count[$user_role] ) )
		$members->role_user_count[$user_role] = 0;

	/* Return the user count for the given role. */
	return $members->role_user_count[$user_role];
}

/**
 * Function for listing users like the WordPress function currently uses for authors.
 *
 * Eventually, I hope to remove this function in favor of wp_list_users():
 * @link http://core.trac.wordpress.org/ticket/15145
 *
 * @since 0.1.0
 * @uses get_users()
 */
function members_list_users( $args = array() ) {

	$output = '';
	$users = get_users( $args );

	if ( !empty( $users ) ) {

		$output .= '<ul class="xoxo members-list-users">';

		foreach ( $users as $user ) {

			$url = get_author_posts_url( $author->ID, $author->user_nicename );

			$class = esc_attr( "user-{$user->ID}" );
			if ( is_author( $user->ID ) )
				$class .= ' current-user';

			$output .= "<li class='{$class}'><a href='{$url}' title='" . esc_attr( $user->display_name ) . "'>{$user->display_name}</a></li>\n";
		}

		$output .= '</ul>';
	}

	$output = apply_filters( 'members_list_users', $output );

	if ( empty( $args['echo'] ) )
		return $output;

	echo $output;
}

?>