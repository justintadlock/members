<?php

namespace Members\Blocks;

class LoggedIn {

	public function name() {
		return 'members/logged-in';
	}

	public function args() {

		return [
			'editor_script'   => 'members-editor',
			'render_callback' => [ $this, 'render' ]
		];
	}

	public function render( array $attr = [], $content = '' ) {

		return is_feed() || ! is_user_logged_in() ? '' : do_shortcode( $content );
	}
}
