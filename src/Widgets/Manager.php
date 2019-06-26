<?php

namespace Members\Widgets;

use Members\Contracts\Bootable;

class Manager implements Bootable {

	public function boot() {

		add_action( 'widgets_init', [ $this, 'registerWidgets' ] );
	}

	public function registerWidgets() {

		array_map( function( $widget ) {
			register_widget( $widget );
		}, [
			Login::class,
			Users::class
		] );
	}
}
