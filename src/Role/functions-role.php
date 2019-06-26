<?php

namespace Members\Role;

function sanitize_name( $role ) {

	$_role = strtolower( $role );
	$_role = preg_replace( '/[^a-z0-9_\-\s]/', '', $_role );

	return apply_filters( 'members/role/sanitize/name', str_replace( ' ', '_', $_role ), $role );
}
