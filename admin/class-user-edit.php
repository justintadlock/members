<?php
/**
 * Handles custom functionality on the edit user screen, such as multiple user roles.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Edit user screen class.
 *
 * @since  1.0.0
 * @access public
 */
final class Members_Admin_User_Edit {

	/**
	 * Holds the instances of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance;

	/**
	 * Sets up needed actions/filters for the admin to initialize.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		// If multiple roles per user is not enabled, bail.
		if ( ! members_multiple_user_roles_enabled() )
			return;

		// Only run our customization on the 'user-edit.php' page in the admin.
		add_action( 'load-user-edit.php', array( $this, 'load_user_edit' ) );
	}

	/**
	 * Adds actions/filters on load.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function load_user_edit() {

		add_action( 'admin_head', array( $this, 'print_styles' ) );

		add_action( 'show_user_profile', array( $this, 'profile_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'profile_fields' ) );

		// Must use `profile_update` to change role. Otherwise, WP will wipe it out.
		add_action( 'profile_update',  array( $this, 'role_update' ) );
	}

	/**
	 * Adds custom profile fields.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $user
	 * @return void
	 */
	public function profile_fields( $user ) {
		global $wp_roles;

		if ( ! current_user_can( 'promote_users' ) || ! current_user_can( 'edit_user', $user->ID ) )
			return;

		$user_roles = (array) $user->roles;

		$editable_roles = members_get_editable_role_names();

		asort( $editable_roles );

		wp_nonce_field( 'new_user_roles', 'members_new_user_roles_nonce' ); ?>

		<h3><?php esc_html_e( 'Roles', 'message-board' ); ?></h3>

		<table class="form-table">

			<tr>
				<th><?php esc_html_e( 'User Roles', 'members' ); ?></th>

				<td>
					<ul>
					<?php foreach ( $editable_roles as $role => $name ) : ?>
						<li>
							<label>
								<input type="checkbox" name="members_user_roles[]" value="<?php echo esc_attr( $role ); ?>" <?php checked( in_array( $role, $user_roles ) ); ?> />
								<?php echo esc_html( $name ); ?>
							</label>
						</li>
					<?php endforeach; ?>
					</ul>
				</td>
			</tr>

		</table>
	<?php }

	/**
	 * Callback function for handling user role changes.  Note that we needed to execute this function
	 * on a different hook, `profile_update`.  Using the normal hooks on the edit user screen won't work
	 * because WP will wipe out the role.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  int    $user_id
	 * @return void
	 */
	public function role_update( $user_id ) {

		// If the current user can't promote users or edit this particular user, bail.
		if ( ! current_user_can( 'promote_users' ) || ! current_user_can( 'edit_user', $user_id ) )
			return;

		// Is this a role change?
		if ( ! isset( $_POST['members_new_user_roles_nonce'] ) || ! wp_verify_nonce( $_POST['members_new_user_roles_nonce'], 'new_user_roles' ) )
			return;

		// Create a new user object.
		$user = new WP_User( $user_id );

		// If we have an array of roles.
		if ( ! empty( $_POST['members_user_roles'] ) ) {

			// Get the current user roles.
			$old_roles = (array) $user->roles;

			// Sanitize the posted roles.
			$new_roles = array_map( 'members_sanitize_role', $_POST['members_user_roles'] );

			// Loop through the posted roles.
			foreach ( $new_roles as $new_role ) {

				// If the user doesn't already have the role, add it.
				if ( ! in_array( $new_role, (array) $user->roles ) )
					$user->add_role( $new_role );
			}

			// Loop through the current user roles.
			foreach ( $old_roles as $old_role ) {

				// If the role is editable and not in the new roles array, remove it.
				if ( members_is_role_editable( $old_role ) && ! in_array( $old_role, $new_roles ) )
					$user->remove_role( $old_role );
			}

		// If the posted roles are empty.
		} else {

			// Loop through the current user roles.
			foreach ( (array) $user->roles as $old_role ) {

				// Remove the role if it is editable.
				if ( members_is_role_editable( $old_role ) )
					$user->remove_role( $old_role );
			}
		}
	}

	/**
	 * Enqueue the plugin admin CSS.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function print_styles() { ?>

		<style type="text/css">.user-role-wrap{ display: none !important; }</style>

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

Members_Admin_User_Edit::get_instance();
