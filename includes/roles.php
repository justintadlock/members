<?php

function members_sanitize_role( $role ) {
	return apply_filters( 'members_sanitize_role', str_replace( '-', '_', sanitize_key( $role ) ), $role );
}

function members_edit_roles_url() {
	return esc_url( add_query_arg( 'page', 'roles', admin_url( 'users.php' ) ) );
}

function members_get_user_role_names( $user_id ) {

	$user = new WP_User( $user_id );

	$names = array();

	foreach ( $user->roles as $role )
		$names[ $role ] = members_get_role_name( $role );

	return $names;
}

function members_get_active_role_names() {
	global $wp_roles, $members;

	if ( !isset( $members->active_roles ) ) {

		$active = array();

		foreach ( $wp_roles->role_names as $role => $name ) {

			$count = members_get_role_user_count( $role );

			if ( !empty( $count ) )
				$active[ $role ] = $name;
		}

		$members->active_roles = $active;
	}

	return $members->active_roles;
}

function members_get_inactive_role_names() {
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

function members_role_name( $role ) {
	echo members_get_role_name( $role );
}

function members_get_role_name( $role ) {
	global $wp_roles;

	return isset( $wp_roles->role_names[ $role ] ) ? $wp_roles->role_names[ $role ] : '';
}

function members_get_roles() {
	global $wp_roles;

	return $wp_roles->roles;
}

function members_get_role_names() {
	global $wp_roles;

	return $wp_roles->role_names;
}

function members_get_editable_role_names() {
	return apply_filters( 'editable_roles', members_get_role_names() );
}

function members_get_uneditable_role_names() {
	return array_diff( members_get_role_names(), members_get_editable_role_names() );
}

function members_is_role_editable( $role ) {

	$editable = members_get_editable_role_names();

	return isset( $editable[ $role ] );
}

function members_get_edit_role_url( $role ) {

	return esc_url(
		wp_nonce_url(
			add_query_arg(
				array(
					'page'   => 'roles',
					'action' => 'edit',
					'role'   => $role
				),
				admin_url( 'users.php' )
			),
			members_get_nonce( 'edit-roles' )
		)
	);
}

function members_get_delete_role_url( $role ) {

	return esc_url(
		wp_nonce_url(
			add_query_arg(
				array(
					'page'   => 'roles',
					'action' => 'delete',
					'role'   => $role
				),
				admin_url( 'users.php' )
			),
			members_get_nonce( 'edit-roles' )
		)
	);
}

function members_get_clone_role_url( $role ) {

	return esc_url(
		add_query_arg(
			array(
				'page'  => 'role-new',
				'clone' => $role
			),
			admin_url( 'users.php' )
		)
	);
}

function members_get_role_users_url( $role ) {
	return esc_url( admin_url( add_query_arg( 'role', $role, 'users.php' ) ) );
}






