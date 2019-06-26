<?php

namespace Members\Shortcodes\Tags;

class Feed extends Shortcode {

	public function tag() {

		return 'members_feed';
	}

	public function render( $attr = [], $content = null ) {

		return ! is_feed() || is_null( $content ) ? '' : do_shortcode( $content );
	}
}
