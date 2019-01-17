<?php

namespace Members\Admin\Views;

use Members\Contracts\Bootable;

class Manager implements Bootable {

	protected $views;

	public function __construct( Views $views ) {

		$this->views = $views;
	}

	public function boot() {

		add_action( 'members/admin/views/register', [ $this, 'registerDefaultViews' ], 5 );
	}

	public function register() {

		do_action( 'members/views/register', $this->views );

	//	do_action( 'members_register_settings_views', null );
	}

	public function registerDefaultAddons( $views ) {

		// Get the transient where the Members views are stored on-site.
		$data = get_transient( 'members_views' );

		if ( ! $data || ! is_array( $data ) ) {

			// `localhost` is the sandbox URL.
			// $url = 'http://localhost/api/th/v1/plugins?views=members';
			$url = 'https://themehybrid.com/api/th/v1/plugins?views=members';

			// Get data from the remote URL.
			$response = wp_remote_get( $url );

			// Bail if we get no response.
			if ( is_wp_error( $response ) ) {
				return;
			}

			// Decode the data that we got.
			$data = json_decode( wp_remote_retrieve_body( $response ) );
		}

		// If we have an array of data, let's roll.
		if ( ! empty( $data ) && is_array( $data ) ) {

			// Set the transient with the new data.
			set_transient( 'members_views', $data, 7 * DAY_IN_SECONDS );

			foreach ( $data as $addon ) {

				$views->add( $addon->slug, [
					'label'         => $addon->title,
					'excerpt'       => $addon->excerpt,
					'url'           => $addon->url,
					'purchase_url'  => $addon->meta->purchase_url,
					'download_url'  => $addon->meta->download_url,
					'rating'        => $addon->meta->rating,
					'rating_count'  => $addon->meta->rating_count,
					'install_count' => $addon->meta->install_count,
					'icon_url'      => $addon->media->icon->url,
					'author_url'    => $addon->author->url,
					'author_name'   => $addon->author->name
				] );
			}
		}
	}
}
