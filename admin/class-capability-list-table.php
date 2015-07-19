<?php

/**
 * Role list table for the roles management page in the admin. Extends the core `WP_List_Table`
 * class in the admin.
 *
 * @since  1.0.0
 * @access public
 */
class Members_Capability_List_Table extends WP_List_Table {

	/**
	 * The current view.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $cap_view = 'all';

	/**
	 * Allowed role views.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $allowed_cap_views = array( 'all', 'mine', 'core', 'members' );

	/**
	 * The current user object.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    object
	 */
	public $current_user = '';

	public $current_user_caps = array();

	/**
	 * Sets up the list table.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();

		// Get the current user object.
		$this->current_user = new WP_User( get_current_user_id() );

		$this->current_user_caps = members_remove_old_levels( array_keys( $this->current_user->get_role_caps() ) );
		$this->current_user_caps = array_diff( $this->current_user_caps, $this->current_user->roles );

		//count( $this->current_user->get_role_caps() ) - count( $this->current_user->caps )

		// Get the defined default role.
		$this->default_role = get_option( 'default_role', $this->default_role );

		// Allow plugin devs to alter the allowed views.
		$this->allowed_cap_views = apply_filters( 'members_allowed_cap_views', $this->allowed_cap_views );

		// Get the current view.
		if ( isset( $_GET['cap_view'] ) && in_array( $_GET['cap_view'], $this->allowed_cap_views ) )
			$this->cap_view = $_GET['cap_view'];
	}

	/**
	 * Sets up the items (capabilities) to list.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function prepare_items() {

		// Get the correct roles array based on the view.
		if ( 'all' === $this->cap_view )
			$caps = members_get_capabilities();

		elseif ( 'mine' === $this->cap_view )
			$caps = $this->current_user_caps;

		elseif ( 'core' === $this->cap_view )
			$caps = members_get_default_capabilities();

		elseif ( 'members' === $this->cap_view )
			$caps = members_get_additional_capabilities();

		if ( isset( $_GET['orderby'] ) && isset( $_GET['order'] ) ) {

			if ( 'title' === $_GET['orderby'] && 'desc' === $_GET['order'] ) {
				arsort( $caps );
			} else {
				asort( $caps );
			}

		} else {
			asort( $caps );
		}

		// Ste up some variables we need.
		$option     = $this->screen->get_option( 'per_page', 'option' );

		if ( ! $option ) {
			$option = str_replace( '-', '_', "{$this->screen->id}_per_page" );
		}

		$per_page = (int) get_user_option( $option );
		if ( empty( $per_page ) || $per_page < 1 ) {
			$per_page = $this->screen->get_option( 'per_page', 'default' );
			if ( ! $per_page ) {
				$per_page = 20;
			}
		}

		$current_page = $this->get_pagenum();
		$items        = $caps;
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
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_columns() {
		return get_column_headers( $this->screen );
	}

	/**
	 * The checkbox column callback.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param  string     $cap
	 * @return string
	 */
	protected function column_cb( $cap ) {

		if ( in_array( $cap, members_get_default_capabilities() ) )
			$out = '';

		else
			$out = sprintf( '<input type="checkbox" name="caps[%1$s]" value="%1$s" />', esc_attr( $cap ) );

		return apply_filters( 'members_manage_capabilities_column_cb', $out, $cap );
	}

	/**
	 * The role name column callback.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param  string     $cap
	 * @return string
	 */
	protected function column_title( $cap ) {

		$states = array();
		$cap_states = '';

		$states = apply_filters( 'members_cap_states', $states );

		if ( !empty( $states ) ) {

			foreach ( $states as $state )
				$cap_states .= sprintf( '<span class="cap-state">%s</span>', $state );

			$cap_states = ' &ndash; ' . $cap_states;
		}

		//$url = current_user_can( 'edit_capabilities' ) ? members_get_edit_role_url( $cap ) : members_get_view_role_url( $cap );

		$out = sprintf( '<strong>%s</strong>', $cap, $cap_states );

		return apply_filters( 'members_manage_capabilities_column_title', $out, $cap );

	}

	/**
	 * The role column callback.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param  string     $cap
	 * @return string
	 */
	protected function column_roles( $cap ) {
		global $wp_roles;



		return apply_filters( 'members_manage_roles_column_role', members_sanitize_role( $cap ), $cap );
	}

	/**
	 * Returns the name of the primary column.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return string
	 */
	protected function get_default_primary_column_name() {
		return( 'title' );
	}

	/**
	 * Handles the row actions.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @param  string     $cap
	 * @param  string     $column_name
	 * @param  string     $primary
	 * @return array
	 */
	protected function handle_row_actions( $cap, $column_name, $primary ) {

		$actions = array();

		if ( $primary === $column_name ) {

			//if ( current_user_can( 'delete_capabilities' ) )
				$actions['delete'] = sprintf( '<a class="members-delete-role-link" href="%s">%s</a>', '', esc_html__( 'Delete', 'members' ) );

			//if ( current_user_can( 'create_roles' ) )
			//	$actions['clone'] = sprintf( '<a href="%s">%s</a>', members_get_clone_role_url( $cap ), esc_html__( 'Clone', 'members' ) );

			$actions = apply_filters( 'members_capabilities_row_actions', $actions, $cap );
		}

		return $this->row_actions( $actions );
	}

	/**
	 * Returns an array of sortable columns.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return array
	 */
	protected function get_sortable_columns() {

		$columns = array(
			'title' => array( 'title',  true  ),
		//	'role'      => array( 'role',       false ),
		//	'users' => array( 'user_count', false ),
		//	'caps'  => array( 'cap_count',  false )
		);

		return $columns;
	}

	/**
	 * Returns an array of views for the list table.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @return array
	 */
	protected function get_views() {

		$views   = array();
		$current = ' class="current"';

		$url = add_query_arg( 'page', 'capabilities', admin_url( 'users.php' ) );

		$all_url        = esc_url( $url );
		$mine_url        = esc_url( add_query_arg( 'cap_view', 'mine', $url ) );
		$core_url       = esc_url( add_query_arg( 'cap_view', 'core', $url ) );
		$members_url    = esc_url( add_query_arg( 'cap_view', 'members', $url ) );

		$all_count        = count( members_get_capabilities()            );
		$mine_count       = count( $this->current_user_caps );
		$core_count       = count( members_get_default_capabilities()          );
		$members_count     = count( members_get_additional_capabilities()          );

		$_views = array(
			'all'        => array( 'url' => $all_url,        'label' => sprintf( _n( 'All %s',        'All %s',        $all_count,        'members' ), sprintf( '<span class="count">(%s)</span>', number_format_i18n( $all_count        ) ) ) ),
			'mine'       => array( 'url' => $mine_url,       'label' => sprintf( _n( 'Mine %s',           'Mine %s',      $mine_count, 'members' ), sprintf( '<span class="count">(%s)</span>', number_format_i18n( $mine_count ) ) ) ),
			'core'       => array( 'url' => $core_url,       'label' => sprintf( _n( 'WordPress %s',       'WordPress %s',       $core_count,       'members' ), sprintf( '<span class="count">(%s)</span>', number_format_i18n( $core_count       ) ) ) ),
			'members'     => array( 'url' => $members_url,     'label' => sprintf( _n( 'Members %s',  'Members %s',  $members_count,     'members' ), sprintf( '<span class="count">(%s)</span>', number_format_i18n( $members_count     ) ) ) ),
		);

		foreach ( $_views as $view => $view_args )
			$views[ $view ] = sprintf( '<a%s href="%s">%s</a>', $view === $this->cap_view ? $current : '', $view_args['url'], $view_args['label'] );

		return apply_filters( 'members_manage_capabilities_views', $views );
	}

	/**
	 * Displays the list table.
	 *
	 * @since  1.0.0
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
	 * @since  1.0.0
	 * @access protected
	 * @return array
	 */
	protected function get_bulk_actions() {
		$actions = array();

	//	if ( current_user_can( 'delete_roles' ) )
	//		$actions['delete'] = esc_html__( 'Delete', 'members' );

		return apply_filters( 'members_manage_roles_bulk_actions', $actions );
	}
}
