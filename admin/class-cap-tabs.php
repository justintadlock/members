<?php

final class Members_Cap_Tabs {

	/**
	 * The role object that we're creating tabs for.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    object
	 */
	public $role;

	/**
	 * Array of caps shown by the cap tabs.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $added_caps = array();

	/**
	 * The caps the role has. Note that if this is a new role (new role screen), the default
	 * new role caps will be passed in.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $has_caps = array();

	/**
	 * Array of data to json encode.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $to_json = array();

	/**
	 * Sets up the cap tabs.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $role
	 * @param  array   $has_caps
	 * @return void
	 */
	public function __construct( $role = '', $has_caps = array() ) {

		// Action hook to run before cap tabs are loaded.
		do_action( 'members_pre_cap_tabs' );

		// Check if there were explicit caps passed in.
		if ( $has_caps )
			$this->has_caps = $has_caps;

		// Check if we have a role.
		if ( $role ) {
			$this->role = get_role( $role );

			// If no explicit caps were passed in, use the role's caps.
			if ( ! $has_caps )
				$this->has_caps = $this->role->capabilities;
		}
	}

	/**
	 * Displays the cap tabs.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function display() { ?>

		<div id="tabcapsdiv" class="postbox">

			<h3><?php esc_html_e( 'Edit Capabilities', 'members' ); ?></h3>

			<div class="inside">

				<div class="members-cap-tabs">
					<?php echo $this->get_tab_nav(); ?>
					<div class="members-tab-wrap"></div>
				</div><!-- .members-cap-tabs -->

			</div><!-- .inside -->

		</div><!-- .postbox -->

		<?php $this->add_tab_content(); ?>

		<script type="text/html" id="<?php echo esc_attr( "tmpl-members-tab-template" ); ?>">
			<?php $this->print_template(); ?>
		</script>

		<?php $this->print_script(); ?>
	<?php }

	/**
	 * Returns the tab nav.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function get_tab_nav() {

		$nav = '';

		foreach ( members_get_cap_groups() as $group ) {

			$nav .= sprintf(
				'<li class="members-tab-title"><a href="%s"><i class="dashicons %s"></i> %s</a></li>',
				esc_attr( "#members-tab-{$group->name}" ),
				sanitize_html_class( $group->icon ),
				esc_html( $group->label )
			);
		}

		return sprintf( '<ul class="members-tab-nav">%s</ul>', $nav );
	}

	/**
	 * Adds the tabs content.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function add_tab_content() {

		foreach ( members_get_cap_groups() as $group ) {

			$caps = $group->caps;

			if ( $group->diff_added )
				$caps = array_diff( $group->caps, $this->added_caps );

			if ( $group->count_added )
				$this->added_caps = array_unique( array_merge( $this->added_caps, $caps ) );

			$this->to_json( $group->name, $caps );
		}
	}

	/**
	 * Adds the json data for an individual tab.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function to_json( $id, $caps ) {

		$is_editable = $this->role ? members_is_role_editable( $this->role->name ) : true;

		$this->to_json[] = array(
			'id'          => sanitize_html_class( "members-tab-{$id}" ),
			'class'       => 'members-tab-content' . ( $is_editable ? ' editable-role' : '' ),
			'readonly'    => $is_editable ? '' : ' disabled="disabled" readonly="readonly"',
			'has_caps'    => $this->has_caps,
			'caps'        => $caps,
			'label'       => array(
				'cap'   => esc_html__( 'Capability', 'members' ),
				'grant' => esc_html__( 'Grant',      'members' ),
				'deny'  => esc_html__( 'Deny',       'members' )
			)
		);
	}

	/**
	 * Underscore JS template for printing tab content.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
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

	/**
	 * Outputs the JS to handle the Underscore JS template.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
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
