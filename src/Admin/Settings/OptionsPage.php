<?php

namespace Members\Admin\Settings;

use Members\Contracts\Bootable;

class OptionsPage implements Bootable {

	protected $name = 'members-settings';

	protected $page = '';

	protected $label = '';
	protected $capability = 'manage_options';

	public $views = [];

	public function __construct( $name, array $args = [] ) {

		foreach ( array_keys( get_object_vars( $this ) ) as $key ) {

			if ( isset( $args[ $key ] ) ) {
				$this->$key = $args[ $key ];
			}
		}

		$this->name = $name;
	}

	public function boot() {

		add_action( 'admin_menu', [ $this, 'adminMenu' ] );
	}

	public function name() {

		return $this->name;
	}

	public function adminMenu() {

		$this->page = add_options_page(
			esc_html( $this->label ),
			esc_html( $this->label ),
			$this->capability,
			$this->name,
			[ $this, 'template' ]
		);

		if ( $this->page ) {

			add_action( 'admin_init', [ $this, 'init' ] );

			add_action( "load-{$this->page}", [ $this, 'load' ] );

			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
		}
	}

	public function init() {

		$this->addView( Views\General::class );
		$this->addView( Views\Addons::class  );

		$this->registerViews();
	}

	public function load() {

		// Print custom styles.
		add_action( 'admin_head', array( $this, 'print_styles' ) );

		// Add help tabs for the current view.
	//	$view = $this->get_view( members_get_current_settings_view() );

		$view = $this->currentView();

		if ( $view ) {
			$this->bootView( $view );
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

		if ( $this->page !== $hook_suffix )
			return;

	//	$view = $this->get_view( members_get_current_settings_view() );

	//	if ( $view )
	//		$view->enqueue();
	}

	function register_settings() {

		foreach ( $this->views as $view )
			$view->register_settings();
	}

	public function template() { ?>

		<div class="wrap">
			<h1><?= esc_html( $this->label ) ?></h1>

			<div class="wp-filter">
				<?php $this->filterLinks() ?>
			</div>

			<?php $this->currentView()->template() ?>
		</div><!-- wrap -->
	<?php }

	protected function filterLinks() { ?>

		<ul class="filter-links">

			<?php foreach ( $this->views as $view ) :

				// Determine current class.
				$class = $view->name() === $this->currentView()->name() ? 'class="current"' : '';

				// Get the URL.
				//$url = members_get_settings_view_url( $view->name );
				$url = admin_url( 'options-general.php' );

				$url = add_query_arg( [
					'page' => $this->name,
					'view' => $view->name()
				], $url );

				if ( 'general' === $view->name() )
					$url = remove_query_arg( 'view', $url ); ?>

				<li class="<?php echo sanitize_html_class( $view->name() ); ?>">
					<a href="<?php echo esc_url( $url ); ?>" <?php echo $class; ?>><?php echo esc_html( $view->label() ); ?></a>
				</li>

			<?php endforeach; ?>

		</ul>
	<?php }

	public function addView( $view ) {

		if ( is_string( $view ) ) {
			$view = $this->resolveView( $view );
		}

		$this->views[ $view->name() ] = $view;
	}

	protected function resolveView( $view ) {

		return new $view( $this );
	}

	protected function registerView( $view ) {

		if ( method_exists( $view, 'register' ) ) {
			$view->register();
		}
	}

	protected function bootView( $view ) {

		if ( method_exists( $view, 'boot' ) ) {
			$view->boot();
		}
	}

	protected function getViews() {

		return $this->views;
	}

	protected function registerViews() {

		foreach ( $this->getViews() as $view ) {
			$this->registerView( $view );
		}
	}

	protected function bootViews() {

		foreach ( $this->getViews() as $view ) {
			$this->bootView( $view );
		}
	}

	public function currentView() {

	//	if ( ! members_is_settings_page() )
	//			return '';

		$current = isset( $_GET['view'] ) ? sanitize_key( $_GET['view'] ) : 'general';

		if ( isset( $this->views[ $current ] ) ) {
			return $this->views[ $current ];
		}

		return null;
	}
}
