<?php

namespace Members\Shortcodes;

use Members\Contracts\Shortcodes\Shortcode;

class LoggedIn implements Shortcode {

	public function tag() {

		return 'members_logged_in';
	}

	public function callback( array $attr = [], $content = null ) {

		return is_feed() || ! is_user_logged_in() || is_null( $content )
		       ? ''
		       : do_shortcode( $content );
	}
}
