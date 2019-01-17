<?php
/**
 * Handles the settings screen.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2018, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Members\Admin;

use Members\Contracts\Bootable;

/**
 * Sets up and handles the plugin settings screen.
 *
 * @since  1.0.0
 * @access public
 */
class Settings implements Bootable {

	/**
	 * Admin page name/ID.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $name = 'members-settings';

	/**
	 * Settings page name.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $settings_page = '';

	/**
	 * Holds an array the settings page views.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    array
	 */
	public $views = array();

	public function boot() {

		add_action( 'admin_menu', [ $this, 'adminMenu' ] );
	}

	/**
	 * Sets up custom admin menus.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function admin_menu() {

		// Create the settings page.
		$this->settings_page = add_options_page(
			esc_html_x( 'Members', 'admin screen', 'members' ),
			esc_html_x( 'Members', 'admin screen', 'members' ),
			apply_filters( 'members_settings_capability', 'manage_options' ),
			$this->name,
			array( $this, 'settings_page' )
		);

		if ( $this->settings_page ) {

			$this->views = new \Members\Admin\Views\Manager();

			do_action( 'members_register_settings_views', $this );

			uasort( $this->views, 'members_priority_sort' );

			// Register setings.
			add_action( 'admin_init', array( $this, 'register_settings' ) );

			// Page load callback.
			add_action( "load-{$this->settings_page}", array( $this, 'load' ) );

			// Enqueue scripts/styles.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		}
	}

	/**
	 * Runs on page load.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function load() {

		// Print custom styles.
		add_action( 'admin_head', array( $this, 'print_styles' ) );

		// Add help tabs for the current view.
		$view = $this->get_view( members_get_current_settings_view() );

		if ( $view ) {
			$view->load();
			$view->add_help_tabs();
		}
	}

	/**
	 * Print styles to the header.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function print_styles() { ?>

		<style type="text/css">
			.settings_page_members-settings .wp-filter { margin-bottom: 15px; }
		</style>
	<?php }

	/**
	 * Enqueue scripts/styles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $hook_suffix
	 * @return void
	 */
	public function enqueue( $hook_suffix ) {

		if ( $this->settings_page !== $hook_suffix )
			return;

		$view = $this->get_view( members_get_current_settings_view() );

		if ( $view )
			$view->enqueue();
	}

	/**
	 * Registers the plugin settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	function register_settings() {

		foreach ( $this->views as $view )
			$view->register_settings();
	}

	/**
	 * Renders the settings page.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function settings_page() { ?>

		<div class="wrap">
			<h1><?php echo esc_html_x( 'Members', 'admin screen', 'members' ); ?></h1>

			<div class="wp-filter">
				<?php $this->filter_links(); ?>
			</div>

			<?php $this->get_view( members_get_current_settings_view() )->template(); ?>

		</div><!-- wrap -->
	<?php }

	/**
	 * Outputs the list of views.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	private function filter_links() { ?>

		<ul class="filter-links">

			<?php foreach ( $this->views as $view ) :

				// Determine current class.
				$class = $view->name === members_get_current_settings_view() ? 'class="current"' : '';

				// Get the URL.
				$url = members_get_settings_view_url( $view->name );

				if ( 'general' === $view->name )
					$url = remove_query_arg( 'view', $url ); ?>

				<li class="<?php echo sanitize_html_class( $view->name ); ?>">
					<a href="<?php echo esc_url( $url ); ?>" <?php echo $class; ?>><?php echo esc_html( $view->label ); ?></a>
				</li>

			<?php endforeach; ?>

		</ul>
	<?php }
}
