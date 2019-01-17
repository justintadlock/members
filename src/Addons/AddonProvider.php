<?php

namespace Members\Addons;

use Members\Tools\ServiceProvider;

class AddonProvider extends ServiceProvider {

	public function register() {

		$this->app->singleton( AddonManager::class );

		$this->app->alias( AddonManager::class, 'addons' );
	}

	public function boot() {

		$this->app->resolve( 'addons' )->boot();
	}
}
