<?php

namespace Members\Addons;

use Members\Contracts\Bootable;

class AddonManager implements Bootable {

	protected $addons;

	public function __construct( Addons $addons ) {

		$this->addons = $addons;
	}

	public function addons() {
		return $this->addons;
	}

	public function boot() {

		add_action( 'members/addons/register', [ $this, 'registerDefaultAddons' ], 5 );
	}

	public function register() {

		do_action( 'members/addons/register', $this->addons );

		do_action( 'members_register_addons' );
	}

	public function registerDefaultAddons( $addons ) {

		// Get the transient where the Members addons are stored on-site.
		$data = get_transient( 'members_addons' );

		if ( ! $data || ! is_array( $data ) ) {

			// `localhost` is the sandbox URL.
			// $url = 'http://localhost/api/th/v1/plugins?addons=members';
			$url = 'https://themehybrid.com/api/th/v1/plugins?addons=members';

			// Get data from the remote URL.
			$response = wp_remote_get( $url );

			// Bail if we get no response.
			if ( is_wp_error( $response ) ) {
				return;
			}

			// Decode the data that we got.
			$data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( ! empty( $data ) && is_array( $data ) ) {
				// Set the transient with the new data.
				set_transient( 'members_addons', $data, 7 * DAY_IN_SECONDS );
			}
		}

		// If we have an array of data, let's roll.
		if ( ! empty( $data ) && is_array( $data ) ) {

			foreach ( $data as $addon ) {

				$addons->add( $addon->slug, [
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
