<?php
/**
 * Edit Capabilities tab section on the edit/new role screen.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Handles building the edit caps tabs.
 *
 * @since  1.0.0
 * @access public
 */
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
	 * Array of section json data.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $sections_json = array();

	/**
	 * Array of control json data.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $controls_json = array();

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
		add_action( 'admin_footer', array( $this, 'localize_scripts' ), 0 );
		add_action( 'admin_footer', array( $this, 'print_templates'  )    );
	}

	/**
	 * Registers the sections (and each section's controls) that will be used for
	 * the tab content.
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
			if ( $group->merge_added )
				$this->added_caps = array_unique( array_merge( $this->added_caps, $caps ) );

			// Create a new section.
			$this->sections[] = $section = new Members_Cap_Section( $this, $group->name, array( 'icon' => $group->icon, 'label' => $group->label ) );

			// Get the section json data.
			$this->sections_json[] = $section->json();

			// Create new controls for each cap.
			foreach ( $caps as $cap ) {

				$this->controls[] = $control = new Members_Cap_Control( $this, $cap, array( 'section' => $group->name ) );

				// Get the control json data.
				$this->controls_json[] = $control->json();
			}
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

			<h3><?php printf( esc_html__( 'Edit Capabilities: %s', 'members' ), '<span class="members-which-tab"></span>' ); ?></h3>

			<div class="inside">

				<div class="members-cap-tabs">
					<?php $this->tab_nav(); ?>
					<div class="members-tab-wrap"></div>
				</div><!-- .members-cap-tabs -->

			</div><!-- .inside -->

		</div><!-- .postbox -->
	<?php }

	/**
	 * Outputs the tab nav.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function tab_nav() { ?>

		<ul class="members-tab-nav">

		<?php foreach ( $this->sections as $section ) : ?>

			<?php $icon = preg_match( '/dashicons-/', $section->icon ) ? sprintf( 'dashicons %s', sanitize_html_class( $section->icon ) ) : esc_attr( $section->icon ); ?>

			<li class="members-tab-title">
				<a href="<?php echo esc_attr( "#members-tab-{$section->section}" ); ?>"><i class="<?php echo $icon; ?>"></i> <span class="label"><?php echo esc_html( $section->label ); ?></span></a>
			</li>

		<?php endforeach; ?>

		</ul><!-- .members-tab-nav -->
	<?php }

	/**
	 * Passes our sections and controls data as json to the `edit-role.js` file.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function localize_scripts() {

		wp_localize_script( 'members-edit-role', 'members_sections', $this->sections_json );
		wp_localize_script( 'members-edit-role', 'members_controls', $this->controls_json );
	}

	/**
	 * Outputs the Underscore JS templates.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function print_templates() { ?>

		<script type="text/html" id="tmpl-members-cap-section">
			<?php members_get_underscore_template( 'cap-section' ); ?>
		</script>

		<script type="text/html" id="tmpl-members-cap-control">
			<?php members_get_underscore_template( 'cap-control' ); ?>
		</script>
	<?php }
}
