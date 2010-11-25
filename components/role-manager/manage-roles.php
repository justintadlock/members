<?php
/**
 * The main file of the Edit Roles component. This is where we run checks to see which page
 * needs to be loaded. It also checks if actions have been performed on the Edit Roles page.
 *
 * @package Members
 * @subpackage Components
 */

/* Get the $wp_roles variable and $wpdb. Do we need $wpdb (need to check)? */
global $wp_roles, $wpdb;

/* Get the current action performed by the user. */
$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : false;

/* If a role has been updated, set the action to 'role-updated'. */
if ( isset($_POST['edit-role-saved']) and $_POST['edit-role-saved'] == 'Y' )
	$action = 'role-updated';

/* If the bulk delete (first submit) has been selected. */
elseif ( 'delete' == $action && ( isset( $_POST['doaction'] ) && __('Apply', 'members') == $_POST['doaction']) )
	$action = 'bulk-delete';

/* If the bulk delete (second submit) has been selected. */
elseif ( 'delete' == $action && ( isset($_POST['doaction2']) && __('Apply', 'members') == $_POST['doaction2']) )
	$action = 'bulk-delete';

/* Choose which actions to perform and pages to load according to the $action variable. */
switch( $action ) {

	/* If the bulk delete was selected. */
	case 'bulk-delete' :

		/* Get the default role (we don't want to delete this). */
		$default_role = get_option( 'default_role' );

		/* Get all roles checked for deletion. */
		$delete_roles = $_POST['roles'];

		/* If no roles were selected, break. Just load up the edit roles page. */
		if ( !is_array( $delete_roles ) ) {
			require_once( 'edit-roles.php' );
			break;
		}

		/* If roles were selected, let's delete some roles. */
		else {

			/* Verify the nonce. */
			check_admin_referer( members_get_nonce( 'edit-roles' ) );

			/* Send through roles deleted message. */
			add_action( 'members_pre_edit_roles_form', 'members_message_roles_deleted' );

			/* Loop through each of the selected roles. */
			foreach ( $delete_roles as $role ) {

				/* Get all users with the current role of the loop. */
				$wp_user_search = new WP_User_Search( '', '', $role );
				$change_users = $wp_user_search->get_results();

				/* If there are users with the role, let's delete them and give them the default role. */
				if ( isset( $change_users ) && is_array( $change_users ) ) {

					/* Loop through each of the users we need to change. */
					foreach( $change_users as $move_user ) {
						$new_user = new WP_User( $move_user );

						/* If the user has the role, remove it and set the default role. Do we need this additional check? */
						if ( $new_user->has_cap( $role ) ) {
							$new_user->remove_role( $role );
							$new_user->set_role( $default_role );
						}
					}
				}

				/* Remove the role. */
				remove_role( $role );
			}

			/* Load the edit roles page. */
			require_once( 'edit-roles.php' );
			break;
		}
		break;

	/* If a single role has been chosen to be deleted. */
	case 'delete' :

		/* Verify the referer. */
		check_admin_referer( members_get_nonce( 'edit-roles' ) );

		/* Send role deleted message. */
		add_action( 'members_pre_edit_roles_form', 'members_message_role_deleted' );

		/* Get the default role. */
		$default_role = get_option( 'default_role' );

		/* Get the role we want to delete. */
		$role = $_GET['role'];

		/* Get all users with the role to be deleted. */
		$wp_user_search = new WP_User_Search( '', '', $role );
		$change_users = $wp_user_search->get_results();

		/* If there are users with the role we're deleting, loop through them, remove the role, and set the default role. */
		if ( isset( $change_users ) && is_array( $change_users ) ) {
			foreach( $change_users as $move_user ) {
				$new_user = new WP_User( $move_user );

				/* If the user has the role, remove it and set the default. Do we need this check? */
				if ( $new_user->has_cap( $role ) ) {
					$new_user->remove_role( $role );
					$new_user->set_role( $default_role );
				}
			}
		}

		/* Remove the role. */
		remove_role( $role );

		/* Load the edit roles page. */
		require_once( 'edit-roles.php' );
		break;

	/* If a role has been updated.  Is this needed still? */
	case 'role-updated' :

		/* Set some default variables. */
		$title = __('Edit Role', 'members');
		$role = $_GET['role'];

		/* Load the edit role form. */
		require_once( 'edit-role-form.php' );
		break;

	/* If a role has been selected to be edited. */
	case 'edit' :

		/* Verify the referer. */
		check_admin_referer( members_get_nonce( 'edit-roles' ) );

		/* Set some default variables. */
		$title = __('Edit Role', 'members');
		$role = $_GET['role'];

		/* Load the edit role form. */
		require_once( 'edit-role-form.php' );

		break;

	/* The default page is the edit roles page. */
	default :

		/* Load the edit roles page.*/
		require_once( 'edit-roles.php' );
		break;
}

?>