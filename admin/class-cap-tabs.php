<?php

final class Members_Cap_Tabs {

	public $role;
	public $members_role;
	public $capabilities = array();
	public $added_caps = array();

	public function __construct( $role = '' ) {

		if ( $role ) {
			$this->role = get_role( $role );

			$this->members_role = members_get_role( $this->role->name );
		}

		// Is the role editable?
		$this->is_editable = $role ? members_is_role_editable( $this->role->name ) : true;

		// Get all the capabilities.
		$this->capabilities = members_get_capabilities();

	}

	public function display() { ?>

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
					<?php $has_cap = $this->role && $this->role->has_cap( $cap ) ? true : false; // Note: $role->has_cap() returns a string intead of TRUE. ?>
					<?php $denied_cap = $this->role && in_array( $cap, $this->members_role->denied_caps ); ?>

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
}
