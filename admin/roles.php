<?php
/**
 * @package Members
 * @subpackage Admin
 */

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
			require_once( MEMBERS_ADMIN . 'roles-list-table.php' );
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

				/* Delete the role and move its users to the default role. */
				members_delete_role( esc_attr( strip_tags( $role ) ) );
			}

			/* Load the edit roles page. */
			require_once( MEMBERS_ADMIN . 'roles-list-table.php' );
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
		$role = esc_attr( strip_tags( $_GET['role'] ) );

		/* Delete the role and move its users to the default role. */
		members_delete_role( $role );

		/* Load the edit roles page. */
		require_once( MEMBERS_ADMIN . 'roles-list-table.php' );
		break;

	/* If a role has been updated.  Is this needed still? */
	case 'role-updated' :

		/* Set some default variables. */
		$role = $_GET['role'];

		/* Load the edit role form. */
		require_once( MEMBERS_ADMIN . 'role-edit.php' );
		break;

	/* If a role has been selected to be edited. */
	case 'edit' :

		/* Verify the referer. */
		check_admin_referer( members_get_nonce( 'edit-roles' ) );

		/* Set some default variables. */
		$role = $_GET['role'];

		/* Load the edit role form. */
		require_once( MEMBERS_ADMIN . 'role-edit.php' );

		break;

	/* The default page is the edit roles page. */
	default :

		/* Load the edit roles page.*/
		require_once( MEMBERS_ADMIN . 'roles-list-table.php' );
		break;
}

?>