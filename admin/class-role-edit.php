<?php

/**
 * Class that displays the edit role screen and handles the form submissions for that page.
 *
 * @since  1.0.0
 * @access public
 */
final class Members_Admin_Role_Edit {

	/**
	 * Current role object to be edited/viewed.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    object
	 */
	protected $role;

	protected $members_role;

	/**
	 * Whether the current role can be edited.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    bool
	 */
	protected $is_editable = true;

	/**
	 * Available capabilities.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $capabilities = array();

	protected $added_caps = array();

	/**
	 * Whether the page was updated.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    bool
	 */
	protected $role_updated = false;

	/**
	 * Runs on the `load-{$page}` hook.  This is the handler for form submissions.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function load() {

		// If the current user can't edit roles, don't proceed.
		if ( ! current_user_can( 'edit_roles' ) )
			wp_die( esc_html__( 'Whoah, partner!', 'members' ) );

		// Get the current role object to edit.
		$this->role = get_role( members_sanitize_role( $_GET['role'] ) );

		// If we don't have a real role, die.
		if ( is_null( $this->role ) )
			wp_die( esc_html__( 'The requested role to edit does not exist.', 'members' ) );

		$this->members_role = members_get_role( $this->role->name );

		// Get all the capabilities.
		$this->capabilities = members_get_capabilities();

		// Is the role editable?
		$this->is_editable = members_is_role_editable( $this->role->name );

		// Check if the form has been submitted.
		if ( $this->is_editable && ( isset( $_POST['grant-caps'] ) || isset( $_POST['deny-caps'] ) || isset( $_POST['new-cap'] ) ) ) {

			$grant_caps = ! empty( $_POST['grant-caps'] ) ? array_unique( $_POST['grant-caps'] ) : array();
			$deny_caps  = ! empty( $_POST['deny-caps'] )  ? array_unique( $_POST['deny-caps']  ) : array();

			// Verify the nonce.
			check_admin_referer( 'edit_role', 'members_edit_role_nonce' );

			// Set the $role_updated variable to true.
			$this->role_updated = true;

			// Loop through all available capabilities.
			foreach ( $this->capabilities as $cap ) {

				// Get the posted capability.
				$grant_this_cap = in_array( $cap, $grant_caps );
				$deny_this_cap  = in_array( $cap, $deny_caps  );

				// Does the role have the cap?
				$is_granted_cap = $this->role->has_cap( $cap );
				$is_denied_cap  = isset( $this->role->capabilities[ $cap ] ) && false === $this->role->capabilities[ $cap ];

				if ( $grant_this_cap && ! $is_granted_cap )
					$this->role->add_cap( $cap );

				else if ( $deny_this_cap && ! $is_denied_cap )
					$this->role->add_cap( $cap, false );

				else if ( ! $grant_this_cap && $is_granted_cap )
					$this->role->remove_cap( $cap );

				else if ( ! $deny_this_cap && $is_denied_cap )
					$this->role->remove_cap( $cap );

			} // End loop through existing capabilities.

			// If new caps were added and are in an array, we need to add them.
			if ( ! empty( $_POST['new-cap'] ) && is_array( $_POST['new-cap'] ) ) {

				// Loop through each new capability from the edit roles form.
				foreach ( $_POST['new-cap'] as $new_cap ) {

					// Sanitize the new capability to remove any unwanted characters.
					$new_cap = sanitize_key( $new_cap );

					// Run one more check to make sure the new capability exists. Add the cap to the role.
					if ( ! empty( $new_cap ) && ! $this->role->has_cap( $new_cap ) )
						$this->role->add_cap( $new_cap );

				} // End loop through new capabilities.

				// If new caps are added, we need to reset the $capabilities array.
				$this->capabilities = members_get_capabilities();

			} // End check for new capabilities.

			$_nm_role = members_role_factory()->add_role( $this->role->name );

			$this->members_role = members_get_role( $this->role->name );

		} // End check for form submission.

		if ( $this->role_updated )
			add_settings_error( 'members_edit_role', 'role_updated', sprintf( esc_html__( '%s role updated.', 'members' ), members_get_role_name( $this->role->name ) ), 'updated' );

		if ( ! $this->is_editable )
			add_settings_error( 'members_edit_role', 'role_uneditable', sprintf( esc_html__( 'The %s role is not editable. This means that it is most likely added via another plugin for a special use or that you do not have permission to edit it.', 'members' ), members_get_role_name( $this->role->name ) ) );

		if ( isset( $_GET['message'] ) && 'role_added' === $_GET['message'] )
			add_settings_error( 'members_edit_role', 'role_added', sprintf( esc_html__( 'The %s role has been created.', 'members' ), members_get_role_name( $this->role->name ) ), 'updated' );

		// Enqueue scripts/styles.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'admin_head', array( $this, 'print_scripts' ) );

		// Add meta boxes.
		add_action( 'members_add_meta_boxes_role', array( $this, 'add_meta_boxes' ) );
	}

	public function enqueue() {
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );
	}

	public function print_scripts() { ?>
		<script type="text/javascript">
			jQuery(document).ready( function($) {
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				postboxes.add_postbox_toggles( 'members_edit_role' );
			});
		</script>
	<?php }
	/**
	 * Displays the page content.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function page() { ?>

		<div class="wrap">

			<h2>
				<?php esc_html_e( 'Edit Role', 'members' ); ?>

				<?php if ( current_user_can( 'create_roles' ) ) : ?>
					<?php printf( '<a class="page-title-action" href="%s">%s</a>', members_get_new_role_url(), esc_html__( 'Add New', 'members' ) ); ?>
				<?php endif; ?>
			</h2>

			<?php settings_errors( 'members_edit_role' ); ?>

			<div id="poststuff">

				<form name="form0" method="post" action="<?php echo members_get_edit_role_url( $this->role->name ); ?>">

					<?php wp_nonce_field( 'edit_role', 'members_edit_role_nonce' ); ?>

					<div id="post-body" class="columns-2">

						<div id="post-body-content">

							<div id="titlediv" class="members-title-div">

								<div id="titlewrap">
									<span class="screen-reader-text"><?php esc_html_e( 'Role Name', 'members' ); ?></span>
									<input type="text" disabled="disabled" readonly="readonly" value="<?php echo esc_attr( members_get_role_name( $this->role->name ) ); ?>" />
								</div><!-- #titlewrap -->

								<div class="inside">
									<div id="edit-slug-box" class="hide-if-no-js">
										<strong><?php esc_html_e( 'Role:', 'members' ); ?></strong> <?php echo esc_attr( $this->role->name ); ?> <!-- edit box -->
									</div>
								</div><!-- .inside -->

							</div><!-- .members-title-div -->

							<?php $this->cap_tabs(); ?>

						</div><!-- #post-body-content -->

						<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
						<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>

						<div id="postbox-container-1" class="post-box-container column-1 side">

							<?php do_action( 'members_add_meta_boxes_role', $this->members_role->role ); ?>
							<?php do_meta_boxes( 'members_edit_role', 'side', null ); ?>

						</div><!-- .post-box-container -->

					</div><!-- #post-body -->
				</form>

			</div><!-- #poststuff -->

		</div><!-- .wrap -->
	<?php }

	public function cap_tabs() { ?>

		<div id="tabcapsdiv" class="postbox">

			<h3><?php esc_html_e( 'Edit Capabilities', 'members' ); ?></h3>

			<div class="inside">

				<div class="members-cap-tabs">
					<?php $this->tab_nav(); ?>
					<?php $this->tab_wrap(); ?>
				</div><!-- .members-cap-tabs -->

			</div><!-- .inside -->
		</div><!-- .postbox -->
	<?php }

	public function tab_nav() {

		$post_types = get_post_types( array(), 'objects' ); ?>

		<ul class="members-tab-nav"><?php

			$this->tab_title( 'all', esc_html__( 'All', 'members' ), 'dashicons-plus' );
			$this->tab_title( 'general', esc_html__( 'General', 'members' ), 'dashicons-wordpress' );

			foreach ( $post_types as $type ) {

				if ( in_array( $type->name, array( 'revision', 'nav_menu_item' ) ) )
					continue;

				$has_caps = members_get_post_type_caps( $type->name );

				if ( empty( $has_caps ) )
					continue;

				$icon = $type->hierarchical ? 'dashicons-admin-page' : 'dashicons-admin-post';

				if ( is_string( $type->menu_icon ) && preg_match( '/dashicons-/i', $type->menu_icon ) )
					$icon = $type->menu_icon;
				else if ( 'attachment' === $type->name )
					$icon = 'dashicons-admin-media';
				else if ( 'download' === $type->name )
					$icon = 'dashicons-download'; // EDD
				else if ( 'product' === $type->name )
					$icon = 'dashicons-cart';
				else if ( ! $type->hierarchical )
					$icon = 'dashicons-admin-post';
				else if ( $type->hierarchical )
					$icon = 'dashicons-admin-page';

				$this->tab_title( "type-{$type->name}", $type->labels->name, $icon );
			}

			$this->tab_title( 'taxonomies', esc_html__( 'Taxonomies', 'members' ), 'dashicons-tag'              );
			$this->tab_title( 'themes',     esc_html__( 'Themes',     'members' ), 'dashicons-admin-appearance' );
			$this->tab_title( 'plugins',    esc_html__( 'Plugins',    'members' ), 'dashicons-admin-plugins'    );
			$this->tab_title( 'users',      esc_html__( 'Users',      'members' ), 'dashicons-admin-users'      );
			$this->tab_title( 'custom',     esc_html__( 'Other',      'members' ), 'dashicons-admin-generic'    );
		?></ul>
	<?php }

	public function tab_wrap() {

		$post_types = get_post_types( array(), 'objects' ); ?>

		<div class="members-tab-wrap"><?php

			$this->tab_content( $this->capabilities, 'all', false );
			$this->tab_content( members_get_wp_general_caps(), 'general' );

			foreach ( $post_types as $type ) {

				$has_caps = members_get_post_type_caps( $type->name );

				if ( ! empty( $has_caps ) )
					$this->tab_content( members_get_post_type_caps( $type->name ), "type-{$type->name}" );
			}

			$this->tab_content( array_diff( members_get_tax_caps(), $this->added_caps ), 'taxonomies' );
			$this->tab_content( members_get_wp_theme_caps(), 'themes' );
			$this->tab_content( members_get_wp_plugin_caps(), 'plugins' );
			$this->tab_content( members_get_wp_user_caps(), 'users' );

			$leftover_caps = array_diff( $this->capabilities, $this->added_caps );

			if ( ! empty( $leftover_caps ) )
				$this->tab_content( $leftover_caps, 'custom' );

		?></div>
	<?php }

	public function tab_title( $id, $title, $icon = 'dashicons-admin-generic' ) {

		printf(
			'<li class="members-tab-title"><a href="%s"><i class="dashicons %s"></i> %s</a></li>',
			esc_attr( "#members-tab-{$id}" ),
			sanitize_html_class( $icon ),
			esc_html( $title )
		);
	}

	public function tab_content( $caps, $id, $add = true ) {

		if ( $add )
			$this->added_caps = array_unique( array_merge( $this->added_caps, $caps ) );

		$disabled = $this->is_editable ? '' : ' disabled="disabled" readonly="readonly"'; ?>

		<div id="<?php echo sanitize_html_class( "members-tab-{$id}" ); ?>" class="members-tab-content">

		<table class="wp-list-table widefat fixed members-roles-select">

			<thead>
				<tr>
					<th><?php esc_html_e( 'Capability', 'members' ); ?></th>
					<th class="column-cb"><?php esc_html_e( 'Grant', 'members' ); ?></th>
					<th class="column-cb"><?php esc_html_e( 'Deny', 'members' ); ?></th>
				</tr>
			</thead>

			<tfoot>
				<tr>
					<th><?php esc_html_e( 'Capability', 'members' ); ?></th>
					<th class="column-cb"><?php esc_html_e( 'Grant', 'members' ); ?></th>
					<th class="column-cb"><?php esc_html_e( 'Deny', 'members' ); ?></th>
				</tr>
			</tfoot>

			<?php foreach ( $caps as $cap ) : ?>

				<tr class="members-cap-checklist">
					<?php $has_cap = $this->role->has_cap( $cap ) ? true : false; // Note: $role->has_cap() returns a string intead of TRUE. ?>
					<?php $denied_cap = in_array( $cap, $this->members_role->denied_caps ); ?>

					<td class="members-cap-name">
						<label><strong><?php echo esc_html( $cap ); ?></strong></label>
					</td>

					<td class="column-cb">
						<input class="members-grant-cb" type="checkbox" data-grant-cap="<?php echo esc_attr( $cap ); ?>" name="grant-caps[]" value="<?php echo esc_attr( $cap ); ?>" <?php checked( $has_cap ); ?><?php echo $disabled; ?> />
					</td>

					<td class="column-cb">
						<input class="members-deny-cb" type="checkbox" data-deny-cap="<?php echo esc_attr( $cap ); ?>" name="deny-caps[]" value="<?php echo esc_attr( $cap ); ?>" <?php checked( $denied_cap ); ?><?php echo $disabled; ?> />
					</td>
				</tr>

			<?php endforeach; ?>
		</table>

		</div><!-- .members-tab-content -->
	<?php }

	function add_meta_boxes( $role ) {

		add_meta_box(
			'submitdiv',
			esc_html__( 'Update Role', 'members' ),
			array( $this, 'submit_box' ),
			'members_edit_role',
			'side',
			'high'
		);

		add_meta_box(
			'newcapdiv',
			esc_html__( 'Custom Capability', 'members' ),
			array( $this, 'new_cap_box' ),
			'members_edit_role',
			'side',
			'core'
		);
	}

	function submit_box() { ?>

		<div class="submitbox" id="submitpost">

			<div id="misc-publishing-actions">

				<div class="misc-pub-section misc-pub-section-users">
					<i class="dashicons dashicons-admin-users"></i>
					<?php esc_html_e( 'Users:', 'members' ); ?>
					<strong class="user-count"><?php echo members_get_role_user_count( $this->role->name ); ?></strong>
				</div>

				<div class="misc-pub-section misc-pub-section-granted">
					<i class="dashicons dashicons-yes"></i>
					<?php esc_html_e( 'Granted:', 'members' ); ?>
					<strong class="granted-count"><?php echo members_get_role_granted_cap_count( $this->role->name ); ?></strong>
				</div>

				<div class="misc-pub-section misc-pub-section-denied">
					<i class="dashicons dashicons-no"></i>
					<?php esc_html_e( 'Denied:', 'members' ); ?>
					<strong class="denied-count"><?php echo members_get_role_denied_cap_count( $this->role->name ); ?></strong>
				</div>

			</div><!-- #misc-publishing-actions -->

			<div id="major-publishing-actions">

				<div id="delete-action">

					<?php if ( $this->is_editable ) : ?>
						<a class="submitdelete deletion" href="<?php echo members_get_delete_role_url( $this->role->name ); ?>"><?php echo esc_html_x( 'Delete', 'delete role', 'members' ); ?></a>
					<?php endif; ?>

					<script type="text/javascript">
						jQuery( '.submitdelete' ).click( function() {
							return window.confirm( '<?php esc_html_e( 'Are you sure you want to delete this role? This is a permanent action and cannot be undone.', 'members' ); ?>' );
						} );
					</script>

				</div>

				<div id="publishing-action">

					<span class="spinner"></span>

					<?php if ( $this->is_editable ) : ?>
						<?php submit_button( esc_attr__( 'Update', 'members' ), 'primary', 'publish', false, array( 'id' => 'publish' ) ); ?>
					<?php endif; ?>
				</div><!-- #publishing-action -->

				<div class="clear"></div>

			</div><!-- #major-publishing-actions -->

		</div><!-- .submitbox -->
	<?php }

	function new_cap_box() { ?>

<p class="description">Note: This doesn't work yet.</p>

<p>
<input type="text" class="widefat" />
</p>
<p>
<a class="button-secondary" id="members-add-new-cap"><?php esc_html_e( 'Add New', 'members' ); ?></a>
</p>

	<?php }
}
