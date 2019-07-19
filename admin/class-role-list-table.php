<?php
/**
 * Handles the roles table on the Roles admin screen.
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
 * Role list table for the roles management page in the admin. Extends the core `WP_List_Table`
 * class in the admin.
 *
 * @since  2.0.0
 * @access public
 */
class Role_List_Table extends \WP_List_Table {

	/**
	 * The current view.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $role_view = 'all';

	/**
	 * Array of role views.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    array
	 */
	public $role_views = array();

	/**
	 * Allowed role views.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    array
	 */
	public $allowed_role_views = array();

	/**
	 * The default role.  This will be assigned the value of `get_option( 'default_role' )`.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    string
	 */
	public $default_role = 'subscriber';

	/**
	 * The current user object.
	 *
	 * @since  2.0.0
	 * @access public
	 * @var    object
	 */
	public $current_user = '';

	/**
	 * Sets up the list table.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		$args = array(
			'plural'   => 'roles',
			'singular' => 'role',
		);

		parent::__construct( $args );

		// Get the current user object.
		$this->current_user = new \WP_User( get_current_user_id() );

		// Get the defined default role.
		$this->default_role = get_option( 'default_role', $this->default_role );

		// Get the role views.
		$this->allowed_role_views = array_keys( $this->get_views() );

		// Get the current view.
		if ( isset( $_GET['view'] ) && in_array( $_GET['view'], $this->allowed_role_views ) )
			$this->role_view = $_GET['view'];
	}

	/**
	 * Sets up the items (roles) to list.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function prepare_items() {

		$roles = array();

		// Get the roles for the view.
		if ( ! empty( $this->role_views[ $this->role_view ]['roles'] ) )
			$roles = $this->role_views[ $this->role_view ]['roles'];

		// Allow devs to filter the items.
		$roles = apply_filters( 'members_manage_roles_items', $roles, $this->role_view );

		// Sort the roles if given something to sort by.
		if ( isset( $_GET['orderby'] ) && isset( $_GET['order'] ) ) {

			// Sort by title/role name, descending.
			if ( 'title' === $_GET['orderby'] && 'desc' === $_GET['order'] )
				arsort( $roles );

			// Sort by role, ascending.
			elseif ( 'role' === $_GET['orderby'] && 'asc' === $_GET['order'] )
				ksort( $roles );

			// Sort by role, descending.
			elseif ( 'role' === $_GET['orderby'] && 'desc' === $_GET['order'] )
				krsort( $roles );

			// Sort by title/role name, ascending.
			else
				asort( $roles );

		// Sort by title/role name, ascending.
		} else {
			asort( $roles );
		}

		// Get the per page option name.
		$option = $this->screen->get_option( 'per_page', 'option' );

		if ( ! $option )
			$option = str_replace( '-', '_', "{$this->screen->id}_per_page" );

		// Get the number of roles to show per page.
		$per_page = (int) get_user_option( $option );

		if ( ! $per_page || $per_page < 1 ) {

			$per_page = $this->screen->get_option( 'per_page', 'default' );

			if ( ! $per_page )
				$per_page = 20;
		}

		// Set up some current page variables.
		$current_page = $this->get_pagenum();
		$items        = $roles;
		$total_count  = count( $items );

		// Set the current page items.
		$this->items = array_slice( $items, ( $current_page - 1 ) * $per_page, $per_page );

		// Set the pagination arguments.
		$this->set_pagination_args( array( 'total_items' => $total_count, 'per_page' => $per_page ) );
	}

	/**
	 * Returns an array of columns to show.
	 *
	 * @see    members_manage_roles_columns()
	 * @since  2.0.0
	 * @access public
	 * @return array
	 */
	public function get_columns() {
		return get_column_headers( $this->screen );
	}

	/**
	 * Returns the default column output. By default, this will return an empty string. The
	 * purpose of the method is to provide plugin authors a hook to return content for
	 * custom columns: `members_manage_roles_column_{$column_name}`.
	 *
	 * @since  1.1.0
	 * @access protected
	 * @param  string     $role
	 * @param  string     $column_name
	 * @return string
	 */
	protected function column_default( $role, $column_name ) {

		return apply_filters( "members_manage_roles_column_{$column_name}", '', $role );
	}

	/**
	 * The checkbox column callback.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @param  string     $role
	 * @return string
	 */
	protected function column_cb( $role ) {

		if ( $role == get_option( 'default_role' ) || in_array( $role, $this->current_user->roles ) || ! members_is_role_editable( $role ) )
			$out = '';

		else
			$out = sprintf( '<input type="checkbox" name="roles[%1$s]" value="%1$s" />', esc_attr( $role ) );

		return apply_filters( 'members_manage_roles_column_cb', $out, $role );
	}

	/**
	 * The role name column callback.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @param  string     $role
	 * @return string
	 */
	protected function column_title( $role ) {

		$states      = array();
		$role_states = '';

		// If the role is the default role.
		if ( $role == get_option( 'default_role' ) )
			$states['default'] = esc_html__( 'Default Role', 'members' );

		// If the current user has this role.
		if ( members_current_user_has_role( $role ) )
			$states['mine'] = esc_html__( 'Your Role', 'members' );

		// Allow devs to filter the role states.
		$states = apply_filters( 'members_role_states', $states, $role );

		// If we have states, string them together.
		if ( ! empty( $states ) ) {

			foreach ( $states as $state => $label )
				$states[ $state ] = sprintf( '<span class="role-state">%s</span>', $label );

			$role_states = ' &ndash; ' . join( ', ', $states );
		}

		// Add the title and role states.
		if ( current_user_can( 'edit_roles' ) )
			$title = sprintf( '<strong><a class="row-title" href="%s">%s</a>%s</strong>', esc_url( members_get_edit_role_url( $role ) ), esc_html( members_get_role( $role )->get( 'label' ) ), $role_states );

		else
			$title = sprintf( '<strong><span class="row-title">%s</span>%s</strong>', esc_html( members_get_role( $role )->get( 'label' ) ), $role_states );

		return apply_filters( 'members_manage_roles_column_role_name', $title, $role );
	}

	/**
	 * The role column callback.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @param  string     $role
	 * @return string
	 */
	protected function column_role( $role ) {
		return apply_filters( 'members_manage_roles_column_role', members_sanitize_role( $role ), $role );
	}

	/**
	 * The users column callback.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @param  string     $role
	 * @return string
	 */
	protected function column_users( $role ) {

		$user_count = members_get_role_user_count( $role );

		$output = number_format_i18n( $user_count );

		if ( 0 < absint( $user_count ) && current_user_can( 'list_users' ) ) {

			$output = sprintf(
				'<a href="%s">%s</a>',
				esc_url( add_query_arg( 'role', $role, admin_url( 'users.php' ) ) ),
				$output
			);
		}

		return apply_filters( 'members_manage_roles_column_users', $output, $role );
	}

	/**
	 * The caps column callback.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @param  string     $role
	 * @return string
	 */
	protected function column_granted_caps( $role ) {
		return apply_filters( 'members_manage_roles_column_granted_caps', members_get_role_granted_cap_count( $role ), $role );
	}

	/**
	 * The caps column callback.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @param  string     $role
	 * @return string
	 */
	protected function column_denied_caps( $role ) {
		return apply_filters( 'members_manage_roles_column_denied_caps', members_get_role_denied_cap_count( $role ), $role );
	}

	/**
	 * Returns the name of the primary column.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @return string
	 */
	protected function get_default_primary_column_name() {
		return 'title';
	}

	/**
	 * Handles the row actions.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @param  string     $role
	 * @param  string     $column_name
	 * @param  string     $primary
	 * @return array
	 */
	protected function handle_row_actions( $role, $column_name, $primary ) {

		$actions = array();

		// Only add row actions on the primary column (title/role name).
		if ( $primary === $column_name ) {

			// If the role can be edited.
			if ( members_is_role_editable( $role ) ) {

				// If the current user can edit the role, add an edit link.
				if ( current_user_can( 'edit_roles' ) )
					$actions['edit'] = sprintf( '<a href="%s">%s</a>', esc_url( members_get_edit_role_url( $role ) ), esc_html__( 'Edit', 'members' ) );

				// If the current user can delete the role, add a delete link.
				if ( ( is_multisite() && is_super_admin() && $role !== $this->default_role ) || ( current_user_can( 'delete_roles' ) && $role !== $this->default_role && ! current_user_can( $role ) ) )
					$actions['delete'] = sprintf( '<a class="members-delete-role-link" href="%s">%s</a>', esc_url( members_get_delete_role_url( $role ) ), esc_html__( 'Delete', 'members' ) );

			// If the role cannot be edited.
			} elseif ( current_user_can( 'edit_roles' ) ) {

				// Add the view role link.
				$actions['view'] = sprintf( '<a href="%s">%s</a>', esc_url( members_get_edit_role_url( $role ) ), esc_html__( 'View', 'members' ) );
			}

			// If the current user can create roles, add the clone role link.
			if ( current_user_can( 'create_roles' ) )
				$actions['clone'] = sprintf( '<a href="%s">%s</a>', esc_url( members_get_clone_role_url( $role ) ), esc_html__( 'Clone', 'members' ) );

			// If this is the default role and the current user can manage options, add a default role change link.
			if ( current_user_can( 'manage_options' ) && $role === $this->default_role )
				$actions['default_role'] = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'options-general.php#default_role' ) ), esc_html__( 'Change Default', 'members' ) );

			// If the currrent user can view users, add a users link.
			if ( current_user_can( 'list_users' ) )
				$actions['users'] = sprintf( '<a href="%s">%s</a>', members_get_role_users_url( $role ), esc_html__( 'Users', 'members' ) );

			// Allow devs to filter the row actions.
			$actions = apply_filters( 'members_roles_row_actions', $actions, $role );
		}

		return $this->row_actions( $actions );
	}

	/**
	 * Returns an array of sortable columns.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @return array
	 */
	protected function get_sortable_columns() {

		return array(
			'title' => array( 'title',  true  ),
			'role'  => array( 'role',   false ),
		);
	}

	/**
	 * Returns an array of views for the list table.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @return array
	 */
	protected function get_views() {

		// Get the current user.
		$current_user = wp_get_current_user();

		$views     = array();
		$current   = ' class="current"';

		$this->role_views['all'] = array(
			'label_count' => _n_noop( 'All %s', 'All %s', 'members' ),
			'roles'       => array_keys( members_get_roles() )
		);

		$this->role_views['mine'] = array(
			'label_count' => _n_noop( 'Mine %s', 'Mine %s', 'members' ),
			'roles'       => $current_user->roles
		);

		$this->role_views['active'] = array(
			'label_count' => _n_noop( 'Has Users %s', 'Has Users %s', 'members' ),
			'roles'       => members_get_active_roles()
		);

		$this->role_views['inactive'] = array(
			'label_count' => _n_noop( 'No Users %s', 'No Users %s', 'members' ),
			'roles'       =>members_get_inactive_roles()
		);

		$this->role_views['editable'] = array(
			'label_count' => _n_noop( 'Editable %s', 'Editable %s', 'members' ),
			'roles'       => members_get_editable_roles()
		);

		$this->role_views['uneditable'] = array(
			'label_count' => _n_noop( 'Uneditable %s', 'Uneditable %s', 'members' ),
			'roles'       => members_get_uneditable_roles()
		);

		// Loop through the role groups and put them into the view list.
		foreach ( members_get_role_groups() as $group ) {

			// Skip role groups that shouldn't be shown in the view list.
			if ( ! $group->show_in_view_list )
				continue;

			$this->role_views[ "group-{$group->name}" ] = array(
				'label_count' => $group->label_count,
				'roles'       => $group->roles
			);
		}

		// Loop through the default views and put them into the view list.
		foreach ( $this->role_views as $view => $args ) {

			$count = count( $args['roles'] );

			// Skip any views with 0 roles.
			if ( 0 >= $count )
				continue;

			$noop = $args['label_count'];

			// Add the view link.
			$views[ $view ] = sprintf(
				'<a%s href="%s">%s</a>',
				$view === $this->role_view ? $current : '',
				'all' === $view ? esc_url( members_get_edit_roles_url() ) : esc_url( members_get_role_view_url( $view ) ),
				sprintf( translate_nooped_plural( $noop, $count, $noop['domain'] ), sprintf( '<span class="count">(%s)</span>', number_format_i18n( $count ) ) )
			);
		}

		return apply_filters( 'members_manage_roles_views', $views, $this->role_view, members_get_edit_roles_url() );
	}

	/**
	 * Displays the list table.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function display() {

		$this->views();

		parent::display();
	}

	/**
	 * Returns an array of bulk actions available.
	 *
	 * @since  2.0.0
	 * @access protected
	 * @return array
	 */
	protected function get_bulk_actions() {
		$actions = array();

		if ( current_user_can( 'delete_roles' ) )
			$actions['delete'] = esc_html__( 'Delete', 'members' );

		return apply_filters( 'members_manage_roles_bulk_actions', $actions );
	}
}
