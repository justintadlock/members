<?php
/**
 * Handles the 'Roles' page in the admin.  This file checks $_POST and $_GET data to decide which file should
 * be loaded.
 *
 * @package Members
 * @subpackage Admin
 */

/* Get the current action performed by the user. */
$action = isset( $_REQUEST['action'] ) ? esc_attr( $_REQUEST['action'] ) : false;

/* If the bulk delete has been selected. */
if ( ( isset( $_POST['action'] ) && 'delete' == $_POST['action'] ) || ( isset( $_POST['action2'] ) && 'delete' == $_POST['action2'] ) )
	$action = 'bulk-delete';

/* Choose which actions to perform and pages to load according to the $action variable. */
switch( $action ) {

	// If the bulk delete was selected.
	case 'bulk-delete' :

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
		}

		// Load the edit roles page.
		require_once( MEMBERS_ADMIN . 'page-roles.php' );

		// Break out of switch statement.
		break;

	/* If a single role has been chosen to be deleted. */
	case 'delete' :

		/* Make sure the current user can delete roles. */
		if ( current_user_can( 'delete_roles' ) ) {

			/* Verify the referer. */
			check_admin_referer( members_get_nonce( 'edit-roles' ) );

			/* Send role deleted message. */
			add_action( 'members_pre_edit_roles_form', 'members_message_role_deleted' );

			/* Get the role we want to delete. */
			$role = esc_attr( strip_tags( $_GET['role'] ) );

			/* Delete the role and move its users to the default role. */
			if ( !empty( $role ) )
				members_delete_role( $role );
		}

		/* Load the edit roles page. */
		require_once( MEMBERS_ADMIN . 'page-roles.php' );

		/* Break out of switch statement. */
		break;

	/* If a role has been selected to be edited. */
	case 'edit' :

		/* Make sure the current user can edit roles. */
		if ( current_user_can( 'edit_roles' ) ) {

			/* Verify the referer. */
			check_admin_referer( members_get_nonce( 'edit-roles' ) );

			/* Load the edit role form. */
			require_once( MEMBERS_ADMIN . 'role-edit.php' );
		}

		/* If the user can't edit roles. */
		else {
			/* Load the edit roles page.*/
			require_once( MEMBERS_ADMIN . 'page-roles.php' );
		}

		/* Break out of switch statement. */
		break;

	/* The default page is the edit roles page. */
	default :

		/* Load the edit roles page.*/
		require_once( MEMBERS_ADMIN . 'page-roles.php' );

		/* Break out of switch statement. */
		break;
}
