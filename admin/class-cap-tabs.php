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
	 * Array of tab sections.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $sections = array();

	/**
	 * Array of single cap controls.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $controls = array();

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

		// Add sections and controls.
		$this->register();

		// Print custom JS in the footer.
		add_action( 'admin_footer', array( $this, 'print_scripts' ) );
	}

	/**
	 * Adds the sections that will be used for the tab content.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function register() {

		// Hook before registering.
		do_action( 'members_pre_edit_caps_manager_register' );

		// Get and loop through the available capability groups.
		foreach ( members_get_cap_groups() as $group ) {

			$caps = $group->caps;

			// Remove added caps.
			if ( $group->diff_added )
				$caps = array_diff( $group->caps, $this->added_caps );

			// Add group's caps to the added caps array.
			if ( $group->count_added )
				$this->added_caps = array_unique( array_merge( $this->added_caps, $caps ) );

			// Create a new section.
			$this->sections[] = new Members_Cap_Section( $this, $group->name, array( 'icon' => $group->icon, 'label' => $group->label ) );

			// Create new controls for each cap.
			foreach ( $caps as $cap )
				$this->controls[] = new Members_Cap_Control( $this, $cap, array( 'section' => $group->name ) );
		}

		// Hook after registering.
		do_action( 'members_edit_caps_manager_register' );
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

		<script type="text/html" id="tmpl-members-cap-section">
			<?php $control = new Members_Cap_Section( $this, null ); ?>
			<?php $control->template(); ?>
		</script>

		<script type="text/html" id="tmpl-members-cap-control">
			<?php $control = new Members_Cap_Control( $this, null ); ?>
			<?php $control->template(); ?>
		</script>
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

		foreach ( $this->sections as $section ) {

			$nav .= sprintf(
				'<li class="members-tab-title"><a href="%s"><i class="dashicons %s"></i> %s</a></li>',
				esc_attr( "#members-tab-{$section->section}" ),
				sanitize_html_class( $section->icon ),
				esc_html( $section->label )
			);
		}

		return sprintf( '<ul class="members-tab-nav">%s</ul>', $nav );
	}

	/**
	 * Outputs the JS to handle the Underscore JS template.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function print_scripts() { ?>

		<script type="text/javascript">
			jQuery( document ).ready( function() {

				// Underscore JS templates.
				var section_template = wp.template( 'members-cap-section' );
				var control_template = wp.template( 'members-cap-control' );

				<?php foreach ( $this->sections as $section ) { ?>
					jQuery( '.members-tab-wrap' ).append(
						section_template( <?php echo wp_json_encode( $section->json() ); ?> )
					);
				<?php } ?>

				<?php foreach ( $this->controls as $control ) { ?>
					jQuery( '#members-tab-<?php echo esc_attr( $control->section ); ?> tbody' ).append(
						control_template( <?php echo wp_json_encode( $control->json() ); ?> )
					);
				<?php } ?>
			} );
		</script>
	<?php }
}
