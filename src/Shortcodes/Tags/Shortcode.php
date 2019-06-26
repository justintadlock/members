<?php

namespace Members\Shortcodes\Tags;

use Members\Shortcodes\Manager;
use Members\Contracts\Displayable;
use Members\Contracts\Renderable;
use Members\Contracts\Shortcodes\Shortcode as ShortcodeContract;

abstract class Shortcode implements Displayable, Renderable, ShortcodeContract {

	protected $manager;

	public function __construct( Manager $manager ) {

		$this->manager = $manager;
	}

	abstract public function tag();

	protected function options( array $options, array $defaults = [] ) {

		return shortcode_atts( $defaults, $options, $this->tag() );
	}

	public function render( $attr = [], $content = null ) {

		return ! is_null( $content ) ? $content : '';
	}

	public function display( $attr = [], $content = null ) {

		echo $this->render( $attr, $content );
	}
}
