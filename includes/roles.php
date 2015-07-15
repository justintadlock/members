<?php

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
