<?php

	//	require_once( 'class-cfm-list-table.php' );

		$table = new Members_Role_List_Table(); ?>

		<div class="wrap">
			<h2>
				<?php esc_html_e( 'Roles', 'members' ); ?>
				<?php if ( current_user_can( 'create_roles' ) ) : ?>
					<a href="<?php echo esc_url( add_query_arg( 'page', 'role-new', admin_url( 'users.php' ) ) ); ?>" class="add-new-h2"><?php esc_html_e( 'Add New', 'members' ); ?></a>
				<?php endif; ?>
			</h2>

			<?php $table->prepare_items(); ?>
			<?php $table->display(); ?>
		</div>

<?php
class Members_Role_List_Table extends WP_List_Table {

	public $meta_type = 'post';
	public $meta_post = array();
	public $meta_key = '';
	public $meta_comment = array();
	public $meta_user = array();
	public $admin_url = '';
	public $post_id = '';

	public $role_view = 'all';
	public $default_role = 'subscriber';

	public $current_user = '';

	public function __construct() {

		parent::__construct();

		$this->admin_url = esc_url( add_query_arg( 'page', 'roles', admin_url( 'users.php' ) ) );

		$this->current_user = new WP_User( get_current_user_id() );

		$this->default_role = get_option( 'default_role' );

		if ( isset( $_GET['role_view'] ) && in_array( $_GET['role_view'], array( 'all', 'mine', 'active', 'inactive', 'editable', 'uneditable' ) ) )
			$this->role_view = $_GET['role_view'];
	}

	function prepare_items() {

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable, 'title' );

		$per_page = 30;
		$current_page = $this->get_pagenum();

		if ( 'mine' === $this->role_view )
			$roles = members_get_user_role_names( $this->current_user->ID );

		elseif ( 'active' === $this->role_view )
			$roles = members_get_active_role_names();

		elseif ( 'inactive' === $this->role_view )
			$roles = members_get_inactive_role_names();

		elseif ( 'editable' === $this->role_view )
			$roles = members_get_editable_role_names();

		elseif ( 'uneditable' === $this->role_view )
			$roles = members_get_uneditable_role_names();

		else
			$roles = members_get_role_names();

		if ( isset( $_GET['orderby'] ) && isset( $_GET['order'] ) ) {

			if ( 'role_name' === $_GET['orderby'] && 'desc' === $_GET['order'] ) {
				arsort( $roles );
			} else {
				asort( $roles );
			}

		} else {
			asort( $roles );
		}

			$_values = array_keys( $roles );


			$total_items = count( $_values );

			$found = array_slice(
				$_values,
				( $current_page - 1 ) * $per_page,
				$per_page
			);


		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page
			)
		);

		$this->items = $found;
	}


	function get_columns() {

		$columns = array(
			'cb'         => '<input type="checkbox" />',
			'title'      => esc_html__( 'Role Name', 'members' ),
			'role'       => esc_html__( 'Role', 'members' ),
			'users'      => esc_html__( 'Users', 'members' ),
			'caps'       => esc_html__( 'Capabilities', 'members' )
		);

		return $columns;
	}

	public function column_default( $role, $column ) {

		switch( $column ) {

			case 'title' :

				$states = array();
				$role_states = '';

				if ( $role == get_option( 'default_role' ) )
					$states[] = esc_html__( 'Default Role', 'members' );

				if ( in_array( $role, $this->current_user->roles ) )
					$states[] = esc_html__( 'Your Role', 'members' );

				$states = apply_filters( 'members_role_states', $states );

				if ( !empty( $states ) ) {

					foreach ( $states as $state )
						$role_states .= sprintf( '<span class="role-state">%s</span>', $state );

					$role_states = ' &ndash; ' . $role_states;
				}

				$edit_link = members_get_edit_role_url( $role ); ?>

				<?php if ( current_user_can( 'edit_roles' ) && members_is_role_editable( $role ) ) { ?>
					<strong><a class="row-title" href="<?php echo esc_url( $edit_link ); ?>"><?php members_role_name( $role ); ?></a><?php echo $role_states; ?></strong>
				<?php } else { ?>
					<strong><?php members_role_name( $role ); ?><?php echo $role_states; ?></strong>
				<?php }

				break;

			case 'role' :

				echo $role;

				break;
			case 'users' :

				echo members_get_role_user_count( $role );

				break;
			case 'caps' :

				echo count( members_remove_old_levels( array_keys( get_role( $role )->capabilities ) ) );

				break;
			default :
				return '';
				break;
		}
	}

	protected function get_default_primary_column_name() {
		return( 'title' );
	}

	protected function handle_row_actions( $role, $column_name, $primary ) {

		$actions = array();

		if ( 'title' === $column_name ) {

			if ( members_is_role_editable( $role ) ) {

				if ( current_user_can( 'edit_roles' ) )
					$actions['edit'] = sprintf( '<a href="%s">%s</a>', members_get_edit_role_url( $role ), esc_html__( 'Edit', 'members' ) );

				if ( ( is_multisite() && is_super_admin() && $role !== $this->default_role ) || ( current_user_can( 'delete_roles' ) && $role !== $this->default_role && !current_user_can( $role ) ) )
					$actions['delete'] = sprintf( '<a href="%s">%s</a>', members_get_delete_role_url( $role ), esc_html__( 'Delete', 'members' ) );

			}

			if ( current_user_can( 'create_roles' ) )
				$actions['clone'] = sprintf( '<a href="%s">%s</a>', members_get_clone_role_url( $role ), esc_html__( 'Clone', 'members' ) );

			if ( current_user_can( 'manage_options' ) && $role === $this->default_role )
				$actions['default_role'] = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'options-general.php#default_role' ) ), esc_html__( 'Change Default', 'members' ) );

			if ( current_user_can( 'list_users' ) )
				$actions['view_users'] = sprintf( '<a href="%s">%s</a>', members_get_role_users_url( $role ), esc_html__( 'View Users', 'members' ) );

			$actions = apply_filters( 'members_roles_row_actions', $actions, $role );

			return $this->row_actions( $actions );
		}
	}

	public function get_sortable_columns() {

		$columns = array(
			'title' => array( 'role_name',  true  ),
			'role'  => array( 'role',       false ),
		//	'users' => array( 'user_count', false ),
		//	'caps'  => array( 'cap_count',  false )
		);

		return $columns;
	}

	public function column_cb( $role ) {

		if ( $role == get_option( 'default_role' ) || in_array( $role, $this->current_user->roles ) || ! members_is_role_editable( $role ) )
			return '';

		return sprintf( '<input type="checkbox" name="roles[%1$s]" value="%1$s" />', esc_attr( $role ) );
	}

	public function get_views() {

		$views   = array();

		$current = ' class="current"';

		$all_url        = esc_url( add_query_arg( array( 'page' => 'roles'                              ), admin_url( 'users.php' ) ) );
		$mine_url       = esc_url( add_query_arg( array( 'page' => 'roles', 'role_view' => 'mine'       ), admin_url( 'users.php' ) ) );
		$active_url     = esc_url( add_query_arg( array( 'page' => 'roles', 'role_view' => 'active'     ), admin_url( 'users.php' ) ) );
		$inactive_url   = esc_url( add_query_arg( array( 'page' => 'roles', 'role_view' => 'inactive'   ), admin_url( 'users.php' ) ) );
		$editable_url   = esc_url( add_query_arg( array( 'page' => 'roles', 'role_view' => 'editable'   ), admin_url( 'users.php' ) ) );
		$uneditable_url = esc_url( add_query_arg( array( 'page' => 'roles', 'role_view' => 'uneditable' ), admin_url( 'users.php' ) ) );

		$all_count        = count( members_get_role_names()            );
		$mine_count       = count( $this->current_user->roles          );
		$active_count     = count( members_get_active_roles()          );
		$inactive_count   = count( members_get_inactive_roles()        );
		$editable_count   = count( members_get_editable_role_names()   );
		$uneditable_count = count( members_get_uneditable_role_names() );

		$_views = array(
			'all'        => array( 'url' => $all_url,        'label' => sprintf( _n( 'All %s',        'All %s',        $all_count,        'members' ), sprintf( '<span class="count">(%s)</span>', number_format_i18n( $all_count        ) ) ) ),
			'mine'       => array( 'url' => $mine_url,       'label' => sprintf( _n( 'Mine %s',       'Mine %s',       $mine_count,       'members' ), sprintf( '<span class="count">(%s)</span>', number_format_i18n( $mine_count       ) ) ) ),
			'active'     => array( 'url' => $active_url,     'label' => sprintf( _n( 'Has Users %s',  'Has Users %s',  $active_count,     'members' ), sprintf( '<span class="count">(%s)</span>', number_format_i18n( $active_count     ) ) ) ),
			'inactive'   => array( 'url' => $inactive_url,   'label' => sprintf( _n( 'No Users %s',   'No Users %s',   $inactive_count,   'members' ), sprintf( '<span class="count">(%s)</span>', number_format_i18n( $inactive_count   ) ) ) ),
			'editable'   => array( 'url' => $editable_url,   'label' => sprintf( _n( 'Editable %s',   'Editable %s',   $editable_count,   'members' ), sprintf( '<span class="count">(%s)</span>', number_format_i18n( $editable_count   ) ) ) ),
			'uneditable' => array( 'url' => $uneditable_url, 'label' => sprintf( _n( 'Uneditable %s', 'Uneditable %s', $uneditable_count, 'members' ), sprintf( '<span class="count">(%s)</span>', number_format_i18n( $uneditable_count ) ) ) )
		);


		foreach ( $_views as $view => $view_args )
			$views[ $view ] = sprintf( '<a%s href="%s">%s</a>', $view === $this->role_view ? $current : '', $view_args['url'], $view_args['label'] );

		return $views;
	}

	public function display() {

		$this->display_breadcrumbs();

		$this->views();

		parent::display();
	}


	public function display_breadcrumbs() {
	}

	protected function get_bulk_actions() {
		$actions = array();

		if ( current_user_can( 'delete_roles' ) )
			$actions['delete'] = esc_html__( 'Delete', 'members' );

		return $actions;
	}
}
