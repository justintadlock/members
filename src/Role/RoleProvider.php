<?php

namespace Members\Role;

use Members\Tools\ServiceProvider;

class RoleProvider extends ServiceProvider {

	public function register() {

		$this->app->singleton( RoleManager::class  );
		$this->app->singleton( GroupManager::class );

		$this->app->alias( RoleManager::class,  'role/roles'  );
		$this->app->alias( GroupManager::class, 'role/groups' );
	}

	public function boot() {

		$this->app->resolve( 'role/roles'  )->boot();
		$this->app->resolve( 'role/groups' )->boot();
	}
}
