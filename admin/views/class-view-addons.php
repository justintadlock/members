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

namespace Members\Admin;

/**
 * Sets up and handles the add-ons settings view.
 *
 * @since  2.0.0
 * @access public
 */
class View_Addons extends View {

	/**
	 * Renders the settings page.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function template() {

		require_once( members_plugin()->dir . 'admin/class-addon.php'      );
		require_once( members_plugin()->dir . 'admin/functions-addons.php' );

		do_action( 'members_register_addons' );

		$addons = members_get_addons(); ?>

		<div class="widefat">

			<?php if ( $addons ) : ?>

				<?php foreach ( $addons as $addon ) : ?>

					<?php $this->addon_card( $addon ); ?>

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
	public function addon_card( $addon ) { ?>

		<div class="plugin-card plugin-card-<?php echo esc_attr( $addon->name ); ?>">

			<div class="plugin-card-top">

				<div class="name column-name">
					<h3>
						<a href="<?php echo esc_url( $addon->url ); ?>">
							<?php echo esc_html( $addon->title ); ?>

							<?php if ( file_exists( members_plugin()->dir . "img/icon-{$addon->name}.png" ) ) : ?>

								<img class="plugin-icon" src="<?php echo esc_url( members_plugin()->uri . "img/icon-{$addon->name}.png" ); ?>" alt="" />

							<?php elseif ( $addon->icon_url ) : ?>

								<img class="plugin-icon" src="<?php echo esc_url( $addon->icon_url ); ?>" alt="" />

							<?php endif; ?>
						</a>
					</h3>
				</div>

				<div class="action-links">

					<ul class="plugin-action-buttons">
						<li>
							<?php if ( $addon->purchase_url ) : ?>

								<a class="install-now button" href="<?php echo esc_url( $addon->purchase_url ); ?>"><?php esc_html_e( 'Purchase', 'members' ); ?></a>

							<?php elseif ( $addon->download_url ) : ?>

								<a class="install-now button" href="<?php echo esc_url( $addon->download_url ); ?>"><?php esc_html_e( 'Download', 'members' ); ?></a>

							<?php else : ?>

								<a class="install-now button" href="<?php echo esc_url( $addon->url ); ?>"><?php esc_html_e( 'Download', 'members' ); ?></a>

							<?php endif; ?>
						</li>
					</ul>
				</div>

				<div class="desc column-description">

					<?php echo wpautop( wp_strip_all_tags( $addon->excerpt ) ); ?>

					<p class="authors">
						<?php $author = sprintf( '<a href="%s">%s</a>', esc_url( $addon->author_url ), esc_html( $addon->author_name ) ); ?>

						<cite><?php printf( esc_html__( 'By %s', 'members' ), $author ); ?></cite>
					</p>

				</div>

			</div><!-- .plugin-card-top -->

			<?php if ( ( $addon->rating && $addon->rating_count ) || $addon->install_count ) : ?>

				<div class="plugin-card-bottom">

					<?php if ( $addon->rating && $addon->rating_count ) : ?>

						<div class="vers column-rating">
							<?php wp_star_rating( array( 'type' => 'rating', 'rating' => floatval( $addon->rating ), 'number' => absint( $addon->rating_count ) ) ); ?>
							<span class="num-ratings" aria-hidden="true">(<?php echo absint( $addon->rating_count ); ?>)</span>
						</div>

					<?php endif; ?>

					<?php if ( $addon->install_count ) : ?>

						<div class="column-downloaded">
							<?php printf(
								esc_html__( '%s+ Active Installs', 'members' ),
								number_format_i18n( absint( $addon->install_count ) )
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
