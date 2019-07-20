<?php
/**
 * Handles the donate settings view.
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
 * Sets up and handles the donate settings view.
 *
 * @since  2.2.0
 * @access public
 */
class View_Donate extends View {

	/**
	 * Enqueues scripts/styles.
	 *
	 * @since  2.2.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_style( 'members-admin' );
	}

	/**
	 * Renders the settings page.
	 *
	 * @since  2.2.0
	 * @access public
	 * @return void
	 */
	public function template() { ?>

		<div class="widefat">

			<div class="welcome-panel">

				<div class="welcome-panel-content">

					<h2>
						<?php esc_html_e( 'Donate Toward Future Development', 'members' ); ?>
					</h2>

					<p class="about-description">
						<?php esc_html_e( 'The Members plugin needs funding to cover development costs toward version 3.0.', 'members' ); ?>
					</p>

					<p class="members-short-p">
						<?php esc_html_e( "Members itself will always remain free as long as I'm able to work on it. However, it is easily my largest and most complex plugin. A major update takes 100s of hours of development. If every user would donate just $1, it would fund fulltime development of this plugin for at least 3 years. Of course, it's not a reality that everyone is able donate. Pitching in any amount will help.", 'members' ); ?>
					</p>

					<p>
						<a target="_blank" class="button button-primary button-hero" href="<?php echo esc_url( 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=E9D2YGZFM8QT4&source=url' ); ?>"><?php esc_html_e( 'Donate Via PayPal', 'members' ); ?></a>
					</p>
					<p>
						<a target="_blank" href="https://themehybrid.com/plugins/members#donate"><?php esc_html_e( 'Learn More', 'members' ); ?></a>
					</p>

				</div><!-- .plugin-card-top -->

			</div><!-- .plugin-card -->

		</div>

	<?php }
}
