<?php

namespace Members\Shortcodes\Tags;

class LoggedIn extends Shortcode {

	public function tag() {

		return 'members_logged_in';
	}

	public function render( $attr = [], $content = null ) {

		return is_feed() || ! is_user_logged_in() || is_null( $content )
		       ? ''
		       : do_shortcode( $content );
	}
}
