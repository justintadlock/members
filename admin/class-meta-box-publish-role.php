<?php
/**
 * Publish/Update role meta box.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2016, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Class to handle the role meta box edit/new role screen.
 *
 * @since  1.0.0
 * @access public
 */
final class Members_Meta_Box_Publish_Role {

	/**
	 * Holds the instances of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance;

	/**
	 * Adds our methods to the proper hooks.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	protected function __construct() {

		add_action( 'members_load_role_edit', array( $this, 'load' ) );
		add_action( 'members_load_role_new',  array( $this, 'load' ) );
	}

	/**
	 * Runs on the page load hook to hook in the meta boxes.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function load() {

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
	}

	/**
	 * Adds the meta box.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $screen_id
	 * @return void
	 */
	public function add_meta_boxes( $screen_id ) {

		add_meta_box( 'submitdiv', esc_html__( 'Role', 'members' ), array( $this, 'meta_box' ), $screen_id, 'side', 'high' );
	}

	/**
	 * Outputs the meta box HTML.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $role
	 * @return void
	 */
	public function meta_box( $role ) {

		// Set up some defaults for new roles.
		$is_editable = true;
		$user_count  = 0;
		$grant_count = 0;
		$deny_count  = 0;

		// If we're editing a role, overwrite the defaults.
		if ( $role ) {
			$is_editable = members_is_role_editable( $role->name );
			$user_count  = members_get_role_user_count( $role->name );
			$grant_count = members_get_role_granted_cap_count( $role->name );
			$deny_count  = members_get_role_denied_cap_count( $role->name );
		} ?>

		<div class="submitbox" id="submitpost">

			<div id="misc-publishing-actions">

				<div class="misc-pub-section misc-pub-section-users">
					<i class="dashicons dashicons-admin-users"></i>
					<?php esc_html_e( 'Users:', 'members' ); ?>
					<strong class="user-count"><?php echo number_format_i18n( $user_count ); ?></strong>
				</div>

				<div class="misc-pub-section misc-pub-section-granted">
					<i class="dashicons dashicons-yes"></i>
					<?php esc_html_e( 'Granted:', 'members' ); ?>
					<strong class="granted-count"><?php echo number_format_i18n( $grant_count ); ?></strong>
				</div>

				<div class="misc-pub-section misc-pub-section-denied">
					<i class="dashicons dashicons-no"></i>
					<?php esc_html_e( 'Denied:', 'members' ); ?>
					<strong class="denied-count"><?php echo number_format_i18n( $deny_count ); ?></strong>
				</div>

			</div><!-- #misc-publishing-actions -->

			<div id="major-publishing-actions">

				<div id="delete-action">

					<?php if ( $is_editable && $role ) : ?>
						<a class="submitdelete deletion members-delete-role-link" href="<?php echo esc_url( members_get_delete_role_url( $role->name ) ); ?>"><?php echo esc_html_x( 'Delete', 'delete role', 'members' ); ?></a>
					<?php endif; ?>
				</div>

				<div id="publishing-action">

					<?php if ( $is_editable ) : ?>
						<?php submit_button( $role ? esc_attr__( 'Update', 'members' ) : esc_attr__( 'Add Role', 'members' ), 'primary', 'publish', false, array( 'id' => 'publish' ) ); ?>
					<?php endif; ?>

				</div><!-- #publishing-action -->

				<div class="clear"></div>

			</div><!-- #major-publishing-actions -->

		</div><!-- .submitbox -->
	<?php }

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		if ( ! self::$instance )
			self::$instance = new self;

		return self::$instance;
	}
}

Members_Meta_Box_Publish_Role::get_instance();
