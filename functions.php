<?php
/**
 * Functions that may be used across a variety of components, so they should always
 * be loaded for use on the front end of the site.
 *
 * @todo Decide whether Template Tags is a useful enough component to stand alone.
 * The Widgets and Shortcodes components both require the use of these functions here,
 * which should be template tags.
 *
 * @package Members
 */

/**
 * Displays the login form.
 *
 * @since 0.1.0
 * @deprecated 0.2.0 Use wp_login_form() instead.
 */
function members_login_form() {
	wp_login_form( array( 'echo' => true ) );
}

/**
 * Creates the login form.
 * @todo Make each section customizable.
 * @todo Clean up.
 *
 * @since 0.1.0
 * @deprecated 0.2.0 Use wp_login_form() instead.
 */
function members_get_login_form( $args = array() ) {
	wp_login_form( array( echo => 'false' ) );
}

/**
 * Function for listing users like the WordPress function currently use for authors.
 * This function is based off wp_dropdown_users() and wp_list_authors(). It is my
 * hope that a wp_list_users() function eventually exists and this is no longer relevant.
 *
 * @todo Allow the input of a role to limit the list.
 *
 * @since 0.1
 * @param $order string ASC or DESC order.
 * @param $orderby string display_name, id, user_login
 * @param $include string IDs of users to include.
 * @param $exclude string IDs of users to exclude.
 * @param $limit int Number of users to list.
 * @param $show_fullname bool Whether to show users' full name (defaults to display name).
 * @param $echo bool Whether to print the list or return for use in a function.
 */
function members_list_users( $args = array() ) {
	global $wpdb;

	$defaults = array(
		'order' => 'ASC',
		'orderby' => 'display_name',
		'include' => '',
		'exclude' => '',
		//'role' => '',
		'limit' => '',
		//'optioncount' => false,
		'show_fullname' => true,
		//'exclude_empty' => false,
		//'exclude_admin' => true,
		'echo' => true,
	);

	$r = wp_parse_args( $args, $defaults );

	$r = apply_filters( 'members_list_users_args', $r );

	extract( $r, EXTR_SKIP );

	$query = "SELECT * FROM $wpdb->users";

	$query_where = array();

	if ( is_array( $include ) )
		$include = join( ',', $include );

	$include = preg_replace( '/[^0-9,]/', '', $include ); // (int)

	if ( $include )
		$query_where[] = "ID IN ($include)";

	if ( is_array($exclude) )
		$exclude = join( ',', $exclude );

	$exclude = preg_replace( '/[^0-9,]/', '', $exclude ); // (int)

	if ( $exclude )
		$query_where[] = "ID NOT IN ($exclude)";

	if ( $query_where )
		$query .= " WHERE " . join( ' AND', $query_where );

	$query .= " ORDER BY $orderby $order";

	if ( '' != $limit ) {
		$limit = absint( $limit );
		$query .= ' LIMIT ' . $limit;
	}

	$users = $wpdb->get_results( $query );

	$output = '';

	if ( !empty( $users ) ) {

		foreach ( (array) $users as $user ) {

			$user->ID = (int) $user->ID;

			$author = get_userdata( $user->ID );

			$name = $author->display_name;

			if ( $show_fullname && ($author->first_name != '' && $author->last_name != '') )
				$name = "$author->first_name $author->last_name";

			$class = "user-{$user->ID}";

			if ( is_author( $user->ID ) )
				$class .= ' current-user';

			if ( $hide_empty )
				$output .= "<li class='$class'>$name</li>\n";
			else
				$output .= "<li class='$class'><a href='" . get_author_posts_url( $author->ID, $author->user_nicename ) . "' title='" . sprintf(__("Posts by %s"), esc_attr( $author->display_name ) ) . "'>$name</a></li>\n";
		}
	}

	$output = apply_filters( 'members_list_users', $output );

	if ( !$echo )
		return $output;

	echo $output;
}

?>