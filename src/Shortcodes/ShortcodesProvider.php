<?php

namespace Members\Shortcodes;

use Members\Tools\ServiceProvider;

class ShortcodesProvider extends ServiceProvider {

	public function register() {

		$this->app->singleton( Manager::class   );

		$this->app->alias( Manager::class, 'shortcodes'   );
	}

	public function boot() {

		$this->app->resolve( 'shortcodes' )->boot();
	}
}
