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

namespace Members;

/**
 * Add-on object class.
 *
 * @since  2.0.0
 * @access public
 */
final class Addon {

	/**
	 * Name/ID for the addon.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var    string
	 */
	public $name = '';

	/**
	 * Title of the add-on.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var    string
	 */
	public $title = '';

	/**
	 * Short description of the add-on.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var    string
	 */
	public $excerpt = '';

	/**
	 * URL where the add-on can be found.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var    string
	 */
	public $url = 'https://themehybrid.com/plugins/members';

	/**
	 * Add-on ZIP file URL.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var    string
	 */
	public $download_url = '';

	/**
	 * Alternate purchase URL.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var    string
	 */
	public $purchase_url = '';

	/**
	 * URL for a 128x128 (size used by WordPress.org) icon image.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var    string
	 */
	public $icon_url = '';

	/**
	 * Add-on plugin's author URL.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var    string
	 */
	public $author_url = '';

	/**
	 * Add-on plugin's author display name.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var    string
	 */
	public $author_name = '';

	/**
	 * Rating for the add-on.  This is the total rating based on a 5-star rating system.
	 * It will be divided by the rating count, so both must be supplied.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var    int
	 */
	public $rating = '';

	/**
	 * Number of ratings.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var    int
	 */
	public $rating_count = 0;

	/**
	 * Number of active installs.  Note that this will be displayed with a `+` at
	 * the end, such as `100,000+`.  Exact counts are necessary.  Just a round number.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @var    string
	 */
	public $install_count = 0;

	/**
	 * Magic method to use in case someone tries to output the object as a string.
	 * We'll just return the name.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return string
	 */
	public function __toString() {
		return $this->name;
	}

	/**
	 * Register a new object.
	 *
	 * @since  2.0.0
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
	public function __construct( $name, $args = array() ) {

		foreach ( array_keys( get_object_vars( $this ) ) as $key ) {

			if ( isset( $args[ $key ] ) )
				$this->$key = $args[ $key ];
		}

		$this->name = sanitize_key( $name );

		if ( ! $this->icon_url )
			$this->icon_url = members_plugin()->uri . 'img/icon-addon.png';
	}
}
