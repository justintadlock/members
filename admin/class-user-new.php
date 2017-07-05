<?php
/**
 * Handles custom functionality on the new user screen, such as multiple user roles.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Members\Admin;

/**
 * Edit user screen class.
 *
 * @since  2.0.0
 * @access public
 */
final class User_New {

	/**
	 * Holds the instances of this class.
	 *
	 * @since  2.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance;

	/**
	 * Constructor method.
	 *
	 * @since  2.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Sets up needed actions/filters for the admin to initialize.
	 *
	 * @since  2.0.0
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		// If multiple roles per user is not enabled, bail.
		if ( ! members_multiple_user_roles_enabled() )
			return;

		// Only run our customization on the 'user-edit.php' page in the admin.
		add_action( 'load-user-new.php', array( $this, 'load' ) );
	}

	/**
	 * Adds actions/filters on load.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function load() {

		// Adds the profile fields.
		add_action( 'user_new_form', array( $this, 'profile_fields' ) );

		// Sets the new user's roles.
		add_action( 'user_register', array( $this, 'user_register' ) );

		// Handle scripts.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'admin_footer',          array( $this, 'print_scripts' ), 25 );
	}

	/**
	 * Adds custom profile fields.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function profile_fields() {

		if ( ! current_user_can( 'promote_users' ) )
			return;

		// Get the default user roles.
		$new_user_roles = apply_filters( 'members_default_user_roles', array( get_option( 'default_role' ) ) );

		// If the form was submitted but didn't go through, get the posted roles.
		if ( isset( $_POST['createuser'] ) && ! empty( $_POST['members_user_roles'] ) )
			$new_user_roles = array_map( 'members_sanitize_role', $_POST['members_user_roles'] );

		$roles = members_get_roles();

		ksort( $roles );

		wp_nonce_field( 'new_user_roles', 'members_new_user_roles_nonce' ); ?>

		<table class="form-table">

			<tr>
				<th><?php esc_html_e( 'User Roles', 'members' ); ?></th>

				<td>
					<div class="wp-tab-panel">
						<ul>
						<?php foreach ( $roles as $role ) : ?>

							<?php if ( members_is_role_editable( $role->name ) ) :?>
							<li>
								<label>
									<input type="checkbox" name="members_user_roles[]" value="<?php echo esc_attr( $role->name ); ?>" <?php checked( in_array( $role->name, $new_user_roles ) ); ?> />
									<?php echo esc_html( $role->label ); ?>
								</label>
							</li>
							<?php endif; ?>

						<?php endforeach; ?>
						</ul>
					</div>
				</td>
			</tr>

		</table>
	<?php }

	/**
	 * Handles the new user's roles once the form has been submitted.
	 *
	 * @since  2.0.0
	 * @access public
	 * @param  int    $user_id
	 * @return void
	 */
	public function user_register( $user_id ) {

		// If the current user can't promote users or edit this particular user, bail.
		if ( ! current_user_can( 'promote_users' ) )
			return;

		// Is this a role change?
		if ( ! isset( $_POST['members_new_user_roles_nonce'] ) || ! wp_verify_nonce( $_POST['members_new_user_roles_nonce'], 'new_user_roles' ) )
			return;

		// Create a new user object.
		$user = new \WP_User( $user_id );

		// If we have an array of roles.
		if ( ! empty( $_POST['members_user_roles'] ) ) {

			// Get the current user roles.
			$old_roles = (array) $user->roles;

			// Sanitize the posted roles.
			$new_roles = array_map( 'members_sanitize_role', $_POST['members_user_roles'] );

			// Loop through the posted roles.
			foreach ( $new_roles as $new_role ) {

				// If the user doesn't already have the role, add it.
				if ( members_is_role_editable( $new_role ) && ! in_array( $new_role, (array) $user->roles ) )
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

			var roles_dropdown = jQuery('select#role');
			roles_dropdown.closest( 'tr' ).remove();
		} );
		</script>

	<?php }

	/**
	 * Returns the instance.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		if ( ! self::$instance ) {
			self::$instance = new self;

			self::$instance->setup_actions();
		}

		return self::$instance;
	}
}

User_New::get_instance();
