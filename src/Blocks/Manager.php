<?php

namespace Members\Blocks;

use Members\Contracts\Bootable;

use function Members\uri;

class Manager implements Bootable {

	public function boot() {

		add_action( 'init', [ $this, 'registerBlocks' ] );

		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue' ] );
	}

	public function registerBlocks() {

		$blocks = [
			LoggedIn::class
		];

		foreach ( $blocks as $block_class ) {

			$block = new $block_class();

			register_block_type( $block->name(), $block->args() );
		}
	}

	public function enqueue() {

		wp_register_script(
			'members-editor',
			uri( 'public/js/editor.js' ),
			[
				'wp-blocks',
				'wp-components',
				'wp-editor',
				'wp-element'
			]
		);
	}
}
