<?php

final class Members_Cap_Tabs {

	public $role;
	public $members_role;
	public $is_editable = true;
	public $capabilities = array();
	public $added_caps = array();
	public $has_caps = array();
	public $to_json = array();

	public function __construct( $role = '', $has_caps = array() ) {

		if ( $has_caps )
			$this->has_caps = $has_caps;

		if ( $role ) {
			$this->role = get_role( $role );

			if ( ! $has_caps )
				$this->has_caps = $this->role->capabilities;

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

		<?php $this->print_tab_template(); ?>
		<?php $this->print_script(); ?>
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

		$this->to_json( $id, $caps ); ?>

	<?php }

	public function to_json( $id, $caps ) {

		$this->to_json[] = array(
			'id'          => sanitize_html_class( "members-tab-{$id}" ),
			'class'       => 'members-tab-content' . ( $this->is_editable ? ' editable-role' : '' ),
			'readonly'    => $this->is_editable ? '' : ' disabled="disabled" readonly="readonly"',
			'has_caps'    => $this->has_caps,
			'caps'        => $caps,
			'label'       => array(
				'cap'   => esc_html__( 'Capability', 'members' ),
				'grant' => esc_html__( 'Grant', 'members' ),
				'deny'  => esc_html__( 'Deny', 'members' )
			)
		);
	}

	public function print_tab_template() { ?>
		<script type="text/html" id="<?php echo esc_attr( "tmpl-members-tab-template" ); ?>">
			<?php $this->print_template(); ?>
		</script>
	<?php }

	public function print_template() { ?>

		<div id="{{ data.id }}" class="{{ data.class }}">

		<table class="wp-list-table widefat fixed members-roles-select">

			<thead>
				<tr>
					<th class="column-cap">{{ data.label.cap }}</th>
					<th class="column-cb">{{ data.label.grant }}</th>
					<th class="column-cb">{{ data.label.deny }}</th>
				</tr>
			</thead>

			<tfoot>
				<tr>
					<th class="column-cap">{{ data.label.cap }}</th>
					<th class="column-cb">{{ data.label.grant }}</th>
					<th class="column-cb">{{ data.label.deny }}</th>
				</tr>
			</tfoot>

			<tbody>

			<# _.each( data.caps, function( cap ) { #>

				<tr class="members-cap-checklist">
					<td class="members-cap-name">
						<label><strong>{{ cap }}</strong></label>
					</td>

					<td class="column-cb">
						<input {{{ data.readonly }}} type="checkbox" name="grant-caps[]" data-grant-cap="{{ cap }}" value="{{ cap }}" <# if ( true === data.has_caps[ cap ] ) { #>checked="checked"<# } #> />
					</td>

					<td class="column-cb">
						<input {{{ data.readonly }}} type="checkbox" name="deny-caps[]" data-deny-cap="{{ cap }}" value="{{ cap }}" <# if ( false === data.has_caps[ cap ] ) { #>checked="checked"<# } #> />
					</td>
				</tr>

			<# } ) #>
			</tbody>
		</table>
		</div>
	<?php }

	public function print_script() { ?>

		<script type="text/javascript">
			jQuery( document ).ready( function() {

				var template = wp.template( 'members-tab-template' );

				<?php foreach ( $this->to_json as $data ) { ?>
					jQuery( '.members-tab-wrap' ).append(
						template( <?php echo wp_json_encode( $data ); ?> )
					);
				<?php } ?>
			} );
		</script>
	<?php }
}
