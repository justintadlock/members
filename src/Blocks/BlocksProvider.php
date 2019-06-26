<?php

namespace Members\Blocks;

use Members\Tools\ServiceProvider;

class BlocksProvider extends ServiceProvider {

	public function register() {
		$this->app->singleton( Manager::class );
	}

	public function boot() {
		$this->app->resolve( Manager::class )->boot();
	}
}
