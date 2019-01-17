<?php

namespace Members\Cap;

use Members\Tools\ServiceProvider;

class CapProvider extends ServiceProvider {

	public function register() {

		$this->app->singleton( CapManager::class   );
		$this->app->singleton( GroupManager::class );

		$this->app->alias( CapManager::class,   'cap/caps'   );
		$this->app->alias( GroupManager::class, 'cap/groups' );
	}

	public function boot() {

		$this->app->resolve( 'cap/caps'   )->boot();
		$this->app->resolve( 'cap/groups' )->boot();
	}
}
