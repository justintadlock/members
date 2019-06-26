<?php

namespace Members\Admin\Settings;

use Members\Tools\ServiceProvider;

class SettingsProvider extends ServiceProvider {

	public function register() {

		$this->app->singleton( OptionsPage::class, function() {

			return new OptionsPage( 'members-settings', [
				'label'      => __( 'Members Settings' ),
				'capability' => 'manage_options'
			] );
		} );
	}

	public function boot() {

		if ( is_admin() ) {
			$this->app->resolve( OptionsPage::class )->boot();
		}
	}
}
