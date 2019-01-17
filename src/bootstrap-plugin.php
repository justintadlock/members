<?php

define( 'MEMBERS_DIR', __DIR__ );

$members = new \Members\Core\Application();


array_map( function( $provider ) use ( $members ) {

	$members->provider( $provider );
}, [
	\Members\Addons\AddonProvider::class,
	\Members\Cap\CapProvider::class,
	\Members\Role\RoleProvider::class,
//	\Members\Settings\SettingsProvider::class,
	\Members\Shortcodes\ShortcodesProvider::class
] );


do_action( 'members/bootstrap', $members );

$members->boot();
