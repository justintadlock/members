<?php

namespace Members\Shortcodes;

use Members\Contracts\Bootable;
use Members\Shortcodes\Tags\Access;
use Members\Shortcodes\Tags\Feed;
use Members\Shortcodes\Tags\LoggedIn;
use Members\Shortcodes\Tags\LoginForm;
use Members\Shortcodes\Tags\NotLoggedIn;
use Members\Tools\Collection;

class Manager implements Bootable {

	protected $shortcodes;

	public function __construct( Collection $shortcodes ) {

		$this->shortcodes = $shortcodes;

		$this->registerDefaultShortcodes();
	}

	public function boot() {

		add_action( 'init', [ $this, 'register' ] );
	}

	public function register() {

		$this->registerShortcodes();
	}

	public function registerDefaultShortcodes() {

		array_map( function( $shortcode ) {
			$this->shortcode( $shortcode );
		}, [
	//		Avatar::class,
			Access::class,
			Feed::class,
			LoggedIn::class,
			LoginForm::class,
			NotLoggedIn::class
		] );
	}

	/**
	 * Adds a shortcode.
	 *
	 * @since  5.0.0
	 * @access public
	 * @param  string|object  $shortcode
	 * @return void
	 */
	public function shortcode( $shortcode ) {

		if ( is_string( $shortcode ) ) {
			$shortcode = $this->resolveShortcode( $shortcode );
		}

		$this->shortcodes->add( $shortcode->tag(), $shortcode );
	}

	/**
	 * Creates a new instance of a service shortcode class.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @param  string    $shortcode
	 * @return object
	 */
	protected function resolveShortcode( $shortcode ) {

		return new $shortcode( $this );
	}

	/**
	 * Registers a shortcode with WordPress.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @param  object    $shortcode
	 * @return void
	 */
	protected function registerShortcode( $shortcode ) {

		add_shortcode( $shortcode->tag(), [ $shortcode, 'render' ] );

		// Back-compatible shortcode tag aliases.
		if ( method_exists( $shortcode, 'aliases' ) && $shortcode->aliases() ) {

			foreach ( $shortcode->aliases() as $alias ) {

				add_shortcode( $alias, [ $shortcode, 'render' ] );
			}
		}
	}

	/**
	 * Returns an array of service shortcodes.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return array
	 */
	protected function getShortcodes() {

		return $this->shortcodes->all();
	}

	/**
	 * Calls the `register()` method of all the available service shortcodes.
	 *
	 * @since  5.0.0
	 * @access protected
	 * @return void
	 */
	protected function registerShortcodes() {

		foreach ( $this->getShortcodes() as $shortcode ) {
			$this->registerShortcode( $shortcode );
		}
	}
}
