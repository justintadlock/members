<?php
/**
 * Functions for handling add-on plugin registration and integration for the Add-Ons
 * view on the settings screen.
 *
 * @package    Members
 * @subpackage Includes
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2018, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

# Register addons.
add_action( 'members_register_addons', 'members_register_default_addons', 5 );

/**
 * Registers any addons stored globally with WordPress.
 *
 * @since  2.0.0
 * @access public
 * @param  object  $wp_addons
 * @return void
 */
function members_register_default_addons() {

	// Get the transient where the Members addons are stored on-site.
	$data = get_transient( 'members_addons' );

	if ( ! $data || ! is_array( $data ) ) {

		// `localhost` is the sandbox URL.
		// $url = 'http://localhost/api/th/v1/plugins?addons=members';
		$url = 'https://themehybrid.com/api/th/v1/plugins?addons=members';

		// Get data from the remote URL.
		$response = wp_remote_get( $url );

		// Bail if we get no response.
		if ( is_wp_error( $response ) )
			return;

		// Decode the data that we got.
		$data = json_decode( wp_remote_retrieve_body( $response ) );
	}

	// If we have an array of data, let's roll.
	if ( ! empty( $data ) && is_array( $data ) ) {

		// Set the transient with the new data.
		set_transient( 'members_addons', $data, 7 * DAY_IN_SECONDS );

		foreach ( $data as $addon ) {

			$args = array(
				'title'         => $addon->title,
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
			);

			members_register_addon( $addon->slug, $args );
		}
	}
}

/**
 * Returns the instance of the addon registry.
 *
 * @since  2.0.0
 * @access public
 * @return object
 */
function members_addon_registry() {

	return \Members\Registry::get_instance( 'addon' );
}

/**
 * Returns all registered addons.
 *
 * @since  2.0.0
 * @access public
 * @return array
 */
function members_get_addons() {

	return members_addon_registry()->get_collection();
}

/**
 * Registers a addon.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $name
 * @param  array   $args
 * @return void
 */
function members_register_addon( $name, $args = array() ) {

	members_addon_registry()->register( $name, new \Members\Addon( $name, $args ) );
}

/**
 * Unregisters a addon.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $name
 * @return void
 */
function members_unregister_addon( $name ) {

	members_addon_registry()->unregister( $name );
}

/**
 * Returns a addon object.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $name
 * @return object
 */
function members_get_addon( $name ) {

	return members_addon_registry()->get( $name );
}

/**
 * Checks if a addon object exists.
 *
 * @since  2.0.0
 * @access public
 * @param  string  $name
 * @return bool
 */
function members_addon_exists( $name ) {

	return members_addon_registry()->exists( $name );
}
