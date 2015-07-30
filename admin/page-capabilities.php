<?php

final class Members_Admin_Edit_Caps {

	/**
	 * Holds the instances of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance;

	/**
	 * Name of the page we've created.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $settings_page = '';

	/**
	 * Sets up our initial actions.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		if ( members_cap_manager_enabled() )
			add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
	}

	/**
	 * Adds the "Capabilities" page to the admin.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function add_settings_page() {

		$this->settings_page = add_submenu_page( 'users.php', esc_attr__( 'Capabilities', 'members' ), esc_attr__( 'Capabilities', 'members' ), 'edit_roles', 'capabilities', array( $this, 'settings_page' ) );

		if ( $this->settings_page )
			add_action( "load-{$this->settings_page}", array( $this, 'load' ) );
	}

	/**
	 * Checks posted data on load and performs actions if needed.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function load() {

		if ( isset( $_GET['action'] ) ) {

			if ( 'delete' === $_GET['action'] ) {

				check_admin_referer( 'delete_cap', 'members_delete_cap_nonce' );

				if ( isset( $_GET['cap'] ) ) {
					$cap = members_sanitize_cap( $_GET['cap'] );

					add_settings_error( 'members_edit_caps', 'cap_deleted', sprintf( esc_html__( '%s capability deleted. Not really. This is just a message to output until the functionality is working.', 'members' ), '<code>' . $cap . '</code>' ), 'updated' );

				} else {

					add_settings_error( 'members_edit_caps', 'no_cap', esc_html__( 'No capability selected to delete.', 'members' ) );
				}
			}
		}
	}

	/**
	 * Outputs the page.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function settings_page() {

		require_once( members_plugin()->admin_dir . 'class-capability-list-table.php' ); ?>

		<div class="wrap">

			<h2>
				<?php esc_html_e( 'Capabilities', 'members' ); ?>

				<?php if ( current_user_can( 'create_roles' ) ) : ?>
					<a href="<?php echo members_get_new_cap_url(); ?>" class="add-new-h2"><?php esc_html_e( 'Add New', 'members' ); ?></a>
				<?php endif; ?>
			</h2>

			<?php settings_errors( 'members_edit_caps' ); ?>

			<div id="poststuff">

				<form id="roles" action="<?php echo members_get_edit_caps_url(); ?>" method="post">

					<?php wp_nonce_field( 'edit_cap', 'members_edit_caps_nonce' ); ?>

					<?php $table = new Members_Capability_List_Table(); ?>
					<?php $table->prepare_items(); ?>
					<?php $table->display(); ?>

				</form><!-- #roles -->

				<script type="text/javascript">
					jQuery( '.members-delete-cap-link' ).click( function() {
						return window.confirm( '<?php esc_html_e( 'Are you sure you want to delete this capability? This is a permanent action and cannot be undone.', 'members' ); ?>' );
					} );
				</script>

			</div><!-- #poststuff -->

		</div><!-- .wrap -->
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

Members_Admin_Edit_Caps::get_instance();
