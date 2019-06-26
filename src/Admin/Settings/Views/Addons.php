<?php
/**
 * Handles the add-ons settings view.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2018, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Members\Admin\Settings\Views;

use Members\Proxies\App;
use Members\Addons\AddonManager;
use Members\Addons\Addons as AddonCollection;

class Addons extends View {

	protected $addons;

	public function name() {
		return 'addons';
	}

	public function label() {
		return __( 'Add-ons', 'members' );
	}

	public function boot() {
		$addons = App::resolve( AddonManager::class );

		$addons->boot();
		$addons->register();

		$this->addons = $addons->addons();
	}

	public function template() { ?>

		<div class="widefat">

			<?php if ( $this->addons->all() ) : ?>

				<?php foreach ( $this->addons->all() as $addon ) : ?>

					<?php $this->card( $addon ); ?>

				<?php endforeach; ?>

			<?php else : ?>

				<div class="error notice">
					<p>
						<strong><?php esc_html_e( 'There are currently no add-ons to show. Please try again later.', 'members' ); ?></strong>
					</p>
				</div>

			<?php endif; ?>

		</div><!-- .widefat -->
	<?php }

	/**
	 * Renders an individual add-on plugin card.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function card( $addon ) { ?>

		<div class="plugin-card plugin-card-<?php echo esc_attr( $addon->name() ); ?>">

			<div class="plugin-card-top">

				<div class="name column-name">
					<h3>
						<a href="<?php echo esc_url( $addon->url() ); ?>">
							<?php echo esc_html( $addon->label() ); ?>

							<?php if ( file_exists( App::resolve( 'path' ) . 'img/icon-' . $addon->name() . '.png' ) ) : ?>

								<img class="plugin-icon" src="<?php echo esc_url( App::resolve( 'uri' ) . 'img/icon-' . $addon->name() . '.png' ); ?>" alt="" />

							<?php elseif ( $addon->iconUrl() ) : ?>

								<img class="plugin-icon" src="<?php echo esc_url( $addon->iconUrl() ); ?>" alt="" />

							<?php endif; ?>
						</a>
					</h3>
				</div>

				<div class="action-links">

					<ul class="plugin-action-buttons">
						<li>
							<?php if ( $addon->purchaseUrl() ) : ?>

								<a class="install-now button" href="<?php echo esc_url( $addon->purchaseUrl() ); ?>"><?php esc_html_e( 'Purchase', 'members' ); ?></a>

							<?php elseif ( $addon->downloadUrl() ) : ?>

								<a class="install-now button" href="<?php echo esc_url( $addon->downloadUrl() ); ?>"><?php esc_html_e( 'Download', 'members' ); ?></a>

							<?php else : ?>

								<a class="install-now button" href="<?php echo esc_url( $addon->url() ); ?>"><?php esc_html_e( 'Download', 'members' ); ?></a>

							<?php endif; ?>
						</li>
					</ul>
				</div>

				<div class="desc column-description">

					<?php echo wpautop( wp_strip_all_tags( $addon->excerpt() ) ); ?>

					<p class="authors">
						<?php $author = sprintf( '<a href="%s">%s</a>', esc_url( $addon->authorUrl() ), esc_html( $addon->authorName() ) ); ?>

						<cite><?php printf( esc_html__( 'By %s', 'members' ), $author ); ?></cite>
					</p>

				</div>

			</div><!-- .plugin-card-top -->

			<?php if ( ( $addon->rating() && $addon->ratingCount() ) || $addon->installCount() ) : ?>

				<div class="plugin-card-bottom">

					<?php if ( $addon->rating() && $addon->ratingCount() ) : ?>

						<div class="vers column-rating">
							<?php wp_star_rating( [
								'type'   => 'rating',
								'rating' => floatval( $addon->rating() ),
								'number' => absint( $addon->ratingCount() )
							] ); ?>
							<span class="num-ratings" aria-hidden="true">(<?php echo absint( $addon->ratingCount() ); ?>)</span>
						</div>

					<?php endif; ?>

					<?php if ( $addon->installCount() ) : ?>

						<div class="column-downloaded">
							<?php printf(
								esc_html__( '%s+ Active Installs', 'members' ),
								number_format_i18n( absint( $addon->installCount() ) )
							); ?>
						</div>

					<?php endif; ?>

				</div><!-- .plugin-card-bottom -->

			<?php endif; ?>

		</div><!-- .plugin-card -->

	<?php }

	/**
	 * Adds help tabs.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function add_help_tabs() {

		// Get the current screen.
		$screen = get_current_screen();

		// Roles/Caps help tab.
		$screen->add_help_tab(
			array(
				'id'       => 'overview',
				'title'    => esc_html__( 'Overview', 'members' ),
				'callback' => array( $this, 'help_tab_overview' )
			)
		);

		// Roles/Caps help tab.
		$screen->add_help_tab(
			array(
				'id'       => 'download',
				'title'    => esc_html__( 'Download', 'members' ),
				'callback' => array( $this, 'help_tab_download' )
			)
		);

		// Roles/Caps help tab.
		$screen->add_help_tab(
			array(
				'id'       => 'purchase',
				'title'    => esc_html__( 'Purchase', 'members' ),
				'callback' => array( $this, 'help_tab_purchase' )
			)
		);

		// Set the help sidebar.
		$screen->set_help_sidebar( members_get_help_sidebar_text() );
	}

	/**
	 * Displays the overview help tab.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function help_tab_overview() { ?>

		<p>
			<?php esc_html_e( 'The Add-Ons screen allows you to view available add-ons for the Members plugin. You can download some plugins directly. Others may be available to purchase.', 'members' ); ?>
		</p>
	<?php }

	/**
	 * Displays the download help tab.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function help_tab_download() { ?>

		<p>
			<?php esc_html_e( 'Some plugins may be available for direct download. In such cases, you can click the download button to get a ZIP file of the plugin.', 'members' ); ?>
		</p>
	<?php }

	/**
	 * Displays the purchase help tab.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function help_tab_purchase() { ?>

		<p>
			<?php esc_html_e( 'Some add-ons may require purchase before downloading them. Clicking the purchase button will take you off-site to view the add-on in more detail.', 'members' ); ?>
		</p>
	<?php }
}
