<?php
/**
 * Handles custom functionality on the manage users screen.
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
 * Manager users screen class.
 *
 * @since  2.0.0
 * @access public
 */
final class Manage_Users {

	/**
	 * Holds the instances of this class.
	 *
	 * @since  2.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	/**
	 * Custom admin notices.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    array
	 */
	public $notices = array();

	/**
	 * Constructore method.
	 *
	 * @since  2.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Sets up needed actions/filters.
	 *
	 * @since  2.0.0
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		// If multiple roles per user is not enabled, bail.
		if ( ! members_multiple_user_roles_enabled() )
			return;

		// Add our primary actions to the load hook.
		add_action( 'load-users.php', array( $this, 'load'             ) );
		add_action( 'load-users.php', array( $this, 'role_bulk_add'    ) );
		add_action( 'load-users.php', array( $this, 'role_bulk_remove' ) );
	}

	/**
	 * Adds actions/filters on load.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function load() {

		// Add custom bulk fields.
		add_action( 'restrict_manage_users', array( $this, 'bulk_fields' ), 5 );

		// Custom manage users columns.
		add_filter( 'manage_users_columns', array( $this, 'manage_users_columns' ) );

		// Handle scripts and styles.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'admin_footer',          array( $this, 'print_scripts' ), 25 );
		add_action( 'admin_head',            array( $this, 'print_styles' ) );

		// If there was an update, add notices if they're from our plugin.
		if ( isset( $_GET['update'] ) ) {

			$action = sanitize_key( $_GET['update'] );

			// If a role was added.
			if ( 'members-role-added' === $action ) {

				$this->notices['role_added'] = array( 'message' => esc_html__( 'Role added to selected users.', 'members' ), 'type' => 'success' );

			// If a role was removed.
			} elseif ( 'members-role-removed' === $action ) {

				$this->notices['role_removed'] = array( 'message' => esc_html__( 'Role removed from selected users.', 'members' ), 'type' => 'success' );

			} elseif ( 'members-error-remove-admin' === $action ) {

				$this->notices['error_remove_admin'] = array( 'message' => esc_html__( 'The current user&#8217;s role must have user editing capabilities.', 'members' ), 'type' => 'error' );
				$this->notices['role_removed'] = array( 'message' => esc_html__( 'Role removed from other selected users.', 'members' ), 'type' => 'success' );
			}

			// If we have notices, hook them in.
			if ( $this->notices )
				add_action( 'admin_notices', array( $this, 'notices' ) );
		}
	}

	/**
	 * Adds a single role to users in bulk.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function role_bulk_add() {

		// Bail if we ain't got users.
		if ( empty( $_REQUEST['users'] ) )
			return;

		// Figure out if we have a role selected.
		if ( ! empty( $_REQUEST['members-add-role-top'] ) && ! empty( $_REQUEST['members-add-role-submit-top'] ) )
			$role = members_sanitize_role( $_REQUEST['members-add-role-top'] );

		elseif ( ! empty( $_REQUEST['members-add-role-bottom'] ) && ! empty( $_REQUEST['members-add-role-submit-bottom'] ) )
			$role = members_sanitize_role( $_REQUEST['members-add-role-bottom'] );

		// Get only editable roles.
		$editable_roles = members_get_editable_roles();

		// If we don't have a role or the role is not editable, bail.
		if ( empty( $role ) || ! in_array( $role, $editable_roles ) )
			return;

		// Validate our nonce.
		check_admin_referer( 'members-bulk-users', 'members-bulk-users-nonce' );

		// If the current user cannot promote users, bail.
		if ( ! current_user_can( 'promote_users' ) )
			return;

		// Loop through the users and add the role if possible.
		foreach ( (array) $_REQUEST['users'] as $user_id ) {

			$user_id = absint( $user_id );

			// If the user doesn't already belong to the blog, bail.
			if ( is_multisite() && ! is_user_member_of_blog( $user_id ) ) {

				wp_die(
					sprintf(
						'<h1>%s</h1> <p>%s</p>',
						esc_html__( 'Whoah, partner!', 'members' ),
						esc_html__( 'One of the selected users is not a member of this site.', 'members' )
					),
					403
				);
			}

			// Check that the current user can promote this specific user.
			if ( ! current_user_can( 'promote_user', $user_id ) )
				continue;

			// Get the user object.
			$user = new \WP_User( $user_id );

			// If the user doesn't have the role, add it.
			if ( ! in_array( $role, $user->roles ) )
				$user->add_role( $role );
		}

		// Redirect to the users screen.
		wp_redirect( add_query_arg( 'update', 'members-role-added', 'users.php' ) );
	}

	/**
	 * Removes a single role from users in bulk.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function role_bulk_remove() {

		// Bail if we ain't got users.
		if ( empty( $_REQUEST['users'] ) )
			return;

		// Figure out if we have a role selected.
		if ( ! empty( $_REQUEST['members-remove-role-top'] ) && ! empty( $_REQUEST['members-remove-role-submit-top'] ) )
			$role = members_sanitize_role( $_REQUEST['members-remove-role-top'] );

		elseif ( ! empty( $_REQUEST['members-remove-role-bottom'] ) && ! empty( $_REQUEST['members-remove-role-submit-bottom'] ) )
			$role = members_sanitize_role( $_REQUEST['members-remove-role-bottom'] );

		// Get only editable roles.
		$editable_roles = members_get_editable_roles();

		// If we don't have a role or the role is not editable, bail.
		if ( empty( $role ) || ! in_array( $role, $editable_roles ) )
			return;

		// Validate our nonce.
		check_admin_referer( 'members-bulk-users', 'members-bulk-users-nonce' );

		// If the current user cannot promote users, bail.
		if ( ! current_user_can( 'promote_users' ) )
			return;

		// Get the current user.
		$current_user = wp_get_current_user();

		$m_role = members_get_role( $role );

		$update = 'members-role-removed';

		// Loop through the users and remove the role if possible.
		foreach ( (array) $_REQUEST['users'] as $user_id ) {

			$user_id = absint( $user_id );

			// If the user doesn't already belong to the blog, bail.
			if ( is_multisite() && ! is_user_member_of_blog( $user_id ) ) {

				wp_die(
					sprintf(
						'<h1>%s</h1> <p>%s</p>',
						esc_html__( 'Whoah, partner!', 'members' ),
						esc_html__( 'One of the selected users is not a member of this site.', 'members' )
					),
					403
				);
			}

			// Check that the current user can promote this specific user.
			if ( ! current_user_can( 'promote_user', $user_id ) )
				continue;

			$is_current_user    = $user_id == $current_user->ID;
			$role_can_promote   = in_array( 'promote_users', $m_role->granted_caps );
			$can_manage_network = is_multisite() && current_user_can( 'manage_network_users' );

			// If the removed role has the `promote_users` cap and user is removing it from themselves.
			if ( $is_current_user && $role_can_promote && ! $can_manage_network ) {

				$can_remove = false;

				// Loop through the current user's roles.
				foreach ( $current_user->roles as $_r ) {

					// If the current user has another role that can promote users, it's
					// safe to remove the role.  Else, the current user needs to keep
					// the role.
					if ( $role !== $_r && in_array( 'promote_users', members_get_role( $_r )->granted_caps ) ) {

						$can_remove = true;
						break;
					}
				}

				if ( ! $can_remove ) {
					$update = 'members-error-remove-admin';
					continue;
				}
			}

			// Get the user object.
			$user = new \WP_User( $user_id );

			// If the user has the role, remove it.
			if ( in_array( $role, $user->roles ) )
				$user->remove_role( $role );
		}

		// Redirect to the users screen.
		wp_redirect( add_query_arg( 'update', $update, 'users.php' ) );
	}

	/**
	 * Print admin notices.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  string  $which
	 * @return void
	 */
	public function notices() {

		if ( $this->notices ) : ?>

			<?php foreach ( $this->notices as $notice ) : ?>

				<div class="notice notice-<?php echo esc_attr( $notice['type'] ); ?> is-dismissible">
					<?php echo wpautop( '<strong>' . $notice['message'] . '</strong>' ); ?>
				</div>

			<?php endforeach;

		endif;
	}

	/**
	 * Outputs "add role" and "remove role" dropdown select fields.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  string  $which
	 * @return void
	 */
	public function bulk_fields( $which ) {

		if ( ! current_user_can( 'promote_users' ) )
			return;

		wp_nonce_field( 'members-bulk-users', 'members-bulk-users-nonce' ); ?>

		<label class="screen-reader-text" for="<?php echo esc_attr( "members-add-role-{$which}" ); ?>">
			<?php esc_html_e( 'Add role&hellip;', 'members' ); ?>
		</label>

		<select name="<?php echo esc_attr( "members-add-role-{$which}" ); ?>" id="<?php echo esc_attr( "members-add-role-{$which}" ); ?>" style="display: inline-block; float: none;">
			<option value=""><?php esc_html_e( 'Add role&hellip;', 'members' ); ?></option>
			<?php wp_dropdown_roles(); ?>
		</select>

		<?php submit_button( esc_html__( 'Add', 'members' ), 'secondary', esc_attr( "members-add-role-submit-{$which}" ), false ); ?>

		<label class="screen-reader-text" for="<?php echo esc_attr( "members-remove-role-{$which}" ); ?>">
			<?php esc_html_e( 'Remove role&hellip;', 'members' ); ?>
		</label>

		<select name="<?php echo esc_attr( "members-remove-role-{$which}" ); ?>" id="<?php echo esc_attr( "members-remove-role-{$which}" ); ?>" style="display: inline-block; float: none;">
			<option value=""><?php esc_html_e( 'Remove role&hellip;', 'members' ); ?></option>
			<?php wp_dropdown_roles(); ?>
		</select>

		<?php submit_button( esc_html__( 'Remove', 'members' ), 'secondary', esc_attr( "members-remove-role-submit-{$which}" ), false );
	}

	/**
	 * Handles table column headers.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  array  $columns
	 * @return array
	 */
	public function manage_users_columns( $columns ) {

		// Make sure role column is named correctly.
		if ( isset( $columns['role'] ) )
			$columns['role'] = esc_html__( 'Roles', 'members' );

		return $columns;
	}

	/**
	 * Handles the output of the roles column on the `users.php` screen.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  string  $output
	 * @param  string  $column
	 * @param  int     $user_id
	 * @return string
	 */
	public function manage_users_custom_column( $output, $column, $user_id ) {

		if ( 'roles' === $column ) {

			$user = new \WP_User( $user_id );

			$user_roles = array();
			$output = esc_html__( 'None', 'members' );

			if ( is_array( $user->roles ) ) {

				foreach ( $user->roles as $role ) {

					if ( members_role_exists( $role ) )
						$user_roles[] = members_translate_role( $role );
				}

				$output = join( ', ', $user_roles );
			}
		}

		return $output;
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {

		wp_enqueue_script( 'jquery' );
	}

	/**
	 * Enqueue the plugin admin CSS.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function print_scripts() { ?>

		<script>
		jQuery( document ).ready( function() {

			jQuery(
				'label[for="new_role"], label[for="new_role2"], #new_role, #new_role2, #changeit, #changeit2'
			).remove();
		} );
		</script>

	<?php }

	/**
	 * Hides the core WP change role form fields because these are hardcoded in.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function print_styles() { ?>

		<style type="text/css">
			label[for="new_role"], #new_role, #changeit,
			label[for="new_role2"], #new_role2, #changeit2 { display: none !important; }
		</style>

	<?php }

	/**
	 * Returns the instance.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self;

			self::$instance->setup_actions();
		}

		return self::$instance;
	}
}

Manage_Users::get_instance();
