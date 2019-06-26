<?php

namespace Members\Shortcodes\Tags;

class NotLoggedIn extends Shortcode {

	public function tag() {

		return 'members_not_logged_in';
	}

	public function render( array $attr = [], $content = null ) {

		return is_user_logged_in() || is_null( $content ) ? '' : do_shortcode( $content );
	}
}
