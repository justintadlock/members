<?php

namespace Members\Private;

use Members\Tools\ServiceProvider;

class PrivateProvider extends ServiceProvider {

	public function register() {

		$this->app->singleton( 'private/site', function( $app ) {

			$is_private = $app->resolve( 'settings/options' )->get( 'private_site' );

			return $is_private ? new PrivateSite() : null;
		} );

	//	$this->app->singleton( PrivateSite::class );

		$this->app->alias( PrivateSite::class, 'private/site' );
	}

	public function boot() {

		$private = $this->app->resolve( 'private/site' );

		if ( $private instanceof PrivateSite ) {
			$private->boot();
		}
	}
}
