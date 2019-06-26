<?php

namespace Members\Shortcodes\Tags;

class LoginForm extends Shortcode {

	public function tag() {

		return 'members_login_form';
	}

	public function render( array $attr = [], $content = null ) {

		return wp_login_form( [ 'echo' => false ] );
	}
}
