<?php

/**
 * Class that displays the roles admin screen and handles requests for that page.
 *
 * @since  1.0.0
 * @access public
 */
final class Members_Admin_Roles {

	/**
	 * Sets up some necessary actions/filters.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		add_action( 'current_screen', array( $this, 'current_screen' ) );

		add_filter( 'manage_users_page_roles_columns', array( $this, 'manage_roles_columns' ), 5 );
	}

	/**
	 * Modifies the current screen object.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function current_screen( $screen ) {

		if ( 'users_page_roles' === $screen->id )
			$screen->add_option( 'per_page', array( 'default' => 20 ) );
	}

	/**
	 * Sets up the roles column headers.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  array  $columns
	 * @return array
	 */
	public function manage_roles_columns( $columns ) {

		$columns = array(
			'cb'     => '<input type="checkbox" />',
			'title'  => esc_html__( 'Role Name',    'members' ),
			'role'   => esc_html__( 'Role',         'members' ),
			'users'  => esc_html__( 'Users',        'members' ),
			'granted_caps'   => esc_html__( 'Granted Caps', 'members' ),
			'denied_caps'   => esc_html__( 'Denied Caps', 'members' )
		);

		return apply_filters( 'members_manage_roles_columns', $columns );
	}

	/**
	 * Runs on the `load-{$page}` hook.  This is the handler for form submissions and requests.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function load() {

		// Get the current action if sent as request.
		$action = isset( $_REQUEST['action'] ) ? sanitize_key( $_REQUEST['action'] ) : false;

		// Get the current action if posted.
		if ( ( isset( $_POST['action'] ) && 'delete' == $_POST['action'] ) || ( isset( $_POST['action2'] ) && 'delete' == $_POST['action2'] ) )
			$action = 'bulk-delete';

		// Bulk delete role handler.
		if ( 'bulk-delete' === $action ) {

			// If roles were selected, let's delete some roles.
			if ( current_user_can( 'delete_roles' ) && isset( $_POST['roles'] ) && is_array( $_POST['roles'] ) ) {

				// Verify the nonce. Nonce created via `WP_List_Table::display_tablenav()`.
				check_admin_referer( 'bulk-roles' );

				// Send through roles deleted message.
				add_action( 'members_pre_edit_roles_form', 'members_message_roles_deleted' );

				// Loop through each of the selected roles.
				foreach ( $_POST['roles'] as $role ) {

					$role = members_sanitize_role( $role );

					if ( members_role_exists( $role ) )
						members_delete_role( $role );
				}

				// Add roles deleted message.
				add_settings_error( 'members_roles', 'roles_deleted', esc_html__( 'Selected roles deleted.', 'members' ), 'updated' );
			}

		// Delete single role handler.
		} else if ( 'delete' === $action ) {

			// Make sure the current user can delete roles.
			if ( current_user_can( 'delete_roles' ) ) {

				// Verify the referer.
				check_admin_referer( 'delete_role', 'members_delete_role_nonce' );

				// Get the role we want to delete.
				$role = members_sanitize_role( $_GET['role'] );

				// Check that we have a role before attempting to delete it.
				if ( members_role_exists( $role ) ) {

					// Add role deleted message.
					add_settings_error( 'members_roles', 'role_deleted', sprintf( esc_html__( '%s role deleted.', 'members' ), members_get_role_name( $role ) ), 'updated' );

					// Delete the role.
					members_delete_role( $role );
				}
			}
		}
	}

	/**
	 * Displays the page content.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function page() {

		require_once( members_plugin()->admin_dir . 'class-role-list-table.php' ); ?>

		<div class="wrap">

			<h2>
				<?php esc_html_e( 'Roles', 'members' ); ?>

				<?php if ( current_user_can( 'create_roles' ) ) : ?>
					<a href="<?php echo members_get_new_role_url(); ?>" class="page-title-action"><?php esc_html_e( 'Add New', 'members' ); ?></a>
				<?php endif; ?>
			</h2>

			<?php settings_errors( 'members_roles' ); ?>

			<div id="poststuff">

				<form id="roles" action="<?php echo members_get_edit_roles_url(); ?>" method="post">

					<?php $table = new Members_Role_List_Table(); ?>
					<?php $table->prepare_items(); ?>
					<?php $table->display(); ?>

				</form><!-- #roles -->

				<script type="text/javascript">
					jQuery( '.members-delete-role-link' ).click( function() {
						return window.confirm( '<?php esc_html_e( 'Are you sure you want to delete this role? This is a permanent action and cannot be undone.', 'members' ); ?>' );
					} );
				</script>

			</div><!-- #poststuff -->

		</div><!-- .wrap -->
	<?php }
}
