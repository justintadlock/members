<?php

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
