<?php

namespace Members\Shortcodes;

use Members\Contracts\Bootable;

class Manager implements Bootable {

	public function boot() {

		add_action( 'init', [ $this, 'register' ] );
	}

	public function register() {

		$shortcodes = [
	//		Avatar::class,
	//		Access::class,
	//		Feed::class,
			LoggedIn::class,
	//		LoginForm::class,
	//		NotLoggedIn::class
		];

		array_walk( $shortcodes, function( &$class ) {

			$shortcode = new $class();

			add_shortcode( $shortcode->tag(), $shortcode->callback() );

			// Back-compatible shortcode tag aliases.
			if ( method_exists( $shortcode, 'aliases' ) && $shortcode->aliases() ) {

				foreach ( $shortcode->aliases() as $alias ) {

					add_shortcode( $alias, $shortcode->callback() );
				}
			}
		} );
	}
}
