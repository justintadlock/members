<?php
/**
 * Handles the add-ons settings view.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2017, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Sets up and handles the add-ons settings view.
 *
 * @since  1.2.0
 * @access public
 */
class Members_Settings_View_Add_Ons extends Members_Settings_View {

	/**
	 * Renders the settings page.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return void
	 */
	public function template() { ?>

		<div class="widefat">

		<?php foreach ( $this->get_add_ons() as $addon ) : ?>

			<div class="plugin-card plugin-card-<?php echo esc_attr( $addon->slug ); ?>">

				<div class="plugin-card-top">

					<div class="name column-name">
						<h3>
							<a href="<?php echo esc_url( $addon->url ); ?>">
								<?php echo esc_html( $addon->title ); ?>
								<img class="plugin-icon" src="<?php echo esc_url( $addon->icon_url ); ?>" alt="" />
							</a>
						</h3>
					</div>

					<div class="action-links">
						<ul class="plugin-action-buttons">
							<li>

							<?php if ( $addon->purchase_url ) : ?>
								<a class="install-now button" href="<?php echo esc_url( $addon->purchase_url ); ?>"><?php esc_html_e( 'Purchase', 'members' ); ?></a>
							<?php else : ?>
								<a class="install-now button" href="<?php echo esc_url( $addon->url ); ?>"><?php esc_html_e( 'Download', 'members' ); ?></a>
							<?php endif; ?>

							</li>
						</ul>
					</div>

					<div class="desc column-description">
						<?php echo wpautop( wp_strip_all_tags( $addon->excerpt ) ); ?>

						<p class="authors">
							<?php $author = sprintf( '<a href="%s">%s</a>', esc_url( $addon->author_url ), esc_html( $addon->author ) ); ?>

							<cite><?php printf( esc_html__( 'By %s', 'members' ), $author ); ?></cite>
						</p>
					</div>
				</div>
			</div>

		<?php endforeach; ?>

		</div>

	<?php }

	/**
	 * Adds help tabs.
	 *
	 * @since  1.2.0
	 * @access public
	 * @return void
	 */
	public function add_help_tabs() {

		// Get the current screen.
		$screen = get_current_screen();

		// Roles/Caps help tab.
	/*	$screen->add_help_tab(
			array(
				'id'       => 'roles-caps',
				'title'    => esc_html__( 'Role and Capabilities', 'members' ),
				'callback' => array( $this, 'help_tab_roles_caps' )
			)
		);
	*/
		// Set the help sidebar.
		$screen->set_help_sidebar( members_get_help_sidebar_text() );
	}

	/**
	 * Returns a list of add-ons for the Members plugin.  This function is temporary until
	 * we can get a proper API set up on Theme Hybrid to allow this data to be sent over.
	 *
	 * @since  1.2.0
	 * @access private
	 * @return array
	 */
	private function get_add_ons() {

		$addons = array();

		$defaults = array(
			'title'          => '',
			'excerpt'        => '',
			'url'            => '',
			'author'         => '',
			'author_url'     => '',
			'purchase_url'   => '',
			'wporg_slug'     => '',
			'download_count' => '',
			'install_count'  => '',
			'icon_url'       => '',
			'slug'           => ''
		);

		$_addons = array(

			'members-role-hierarchy' =>  array(
				'title'       => 'Members - Role Hierarchy',
				'url'         => 'https://themehybrid.com/plugins/members-role-hierarchy',
				'excerpt'     => 'Add-on plugin for Members for hierarchical user roles.',
				'icon_url'    => members_plugin()->dir_uri . 'img/add-ons/icon-members-role-hierarchy.png',
				'author'      => 'Justin Tadlock',
				'author_url'  => 'https://themehybrid.com'
			),

			'members-role-levels' => array(
				'title'        => 'Members - Role Levels',
				'url'          => 'https://themehybrid.com/plugins/members-role-levels',
				'purchase_url' => 'https://themehybrid.com/plugins/members-role-levels',
				'excerpt'      => "Exposes the old user levels system, which fixes the WordPress author drop-down bug.",
				'icon_url'     => members_plugin()->dir_uri . 'img/add-ons/icon-members-role-levels.png',
				'author'       => 'Justin Tadlock',
				'author_url'   => 'https://themehybrid.com'
			)
		);

		foreach ( $_addons as $slug => $args ) {

			$addons[ $slug ] = (object) wp_parse_args( $args, $defaults );

			$addons[ $slug ]->slug = $slug;
		}

		return $addons;
	}
}
