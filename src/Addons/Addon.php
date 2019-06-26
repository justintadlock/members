<?php
/**
 * Class for handling an add-on object.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2018, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Members\Addons;

use Members\Proxies\App;

/**
 * Add-on object class.
 *
 * @since  2.0.0
 * @access public
 */
class Addon {

	protected $options = [];

	/**
	 * Magic method to use in case someone tries to output the object as a
	 * string. We'll just return the name.
	 *
	 * @since  3.0.0
	 * @access public
	 * @return string
	 */
	public function __toString() {
		return $this->name();
	}

	/**
	 * Register a new object.
	 *
	 * @since  3.0.0
	 * @access public
	 * @param  string  $name
	 * @param  array   $args  {
	 *     @type string  $label        Internationalized text label.
	 *     @type string  $icon         Dashicon icon in the form of `dashicons-icon-name`.
	 *     @type array   $caps         Array of capabilities in the addon.
	 *     @type bool    $merge_added  Whether to merge this caps into the added caps array.
	 *     @type bool    $diff_added   Whether to remove previously-added caps from this addon.
	 * }
	 * @return void
	 */
	public function __construct( $name, array $args = [] ) {

		$this->options = array_merge( [
			'label'         => '',
			'excerpt'       => '',
			'url'           => '',
			'download_url'  => '',
			'purchase_url'  => '',
			'icon_url'      => '',
			'author_url'    => '',
			'author_name'   => '',
			'rating'        => 0,
			'rating_count'  => 0,
			'install_count' => 0
		], $args );

		$this->name = sanitize_key( $name );

		if ( ! $this->options['icon_url'] ) {
			$this->options['icon_url'] = App::resolve( 'uri' ) . 'img/icon-addon.png';
		}
	}

	public function option( $name ) {

		return isset( $this->options[ $name ] ) ? $this->options[ $name ] : null;
	}

	public function name() {
		return $this->option( 'name' );
	}

	public function label() {
		return $this->option( 'label' );
	}

	public function excerpt() {
		return $this->option( 'excerpt' );
	}

	public function url() {
		return $this->option( 'url' );
	}

	public function downloadUrl() {
		return $this->option( 'download_url' );
	}

	public function purchaseUrl() {
		return $this->option( 'purchase_url' );
	}

	public function iconUrl() {
		return $this->option( 'icon_url' );
	}

	public function authorUrl() {
		return $this->option( 'author_url' );
	}

	public function authorName() {
		return $this->option( 'author_name' );
	}

	public function rating() {
		return $this->option( 'rating' );
	}

	public function ratingCount() {
		return $this->option( 'rating_count' );
	}

	public function installCount() {
		return $this->option( 'install_count' );
	}
}
