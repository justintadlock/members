<?php

namespace Members\Role;

use Members\Tools\ServiceProvider;

class RoleProvider extends ServiceProvider {

	public function register() {

		$this->app->singleton( Roles::class  );
		$this->app->singleton( Groups::class );

		$this->app->singleton( RoleManager::class, function( $app ) {
			return new RoleManager( $app->resolve( Roles::class ) );
		} );

		$this->app->singleton( GroupManager::class, function( $app ) {
			return new GroupManager( $app->resolve( Groups::class ) );
		} );
	}

	public function boot() {

		$this->app->resolve( RoleManager::class  );
		$this->app->resolve( GroupManager::class );
	}
}
