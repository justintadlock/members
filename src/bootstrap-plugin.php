<?php

$members = new \Members\Core\Application();


array_map( function( $provider ) use ( $members ) {

	$members->provider( $provider );
}, [
	\Members\Addons\AddonProvider::class,
	\Members\Blocks\BlocksProvider::class,
	\Members\Cap\CapProvider::class,
	\Members\Role\RoleProvider::class,
//	\Members\Shortcodes\ShortcodesProvider::class,

	\Members\Admin\Settings\SettingsProvider::class
] );


do_action( 'members/bootstrap', $members );

$members->boot();
