<?php
/**
 * Add new/custom capability meta box.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Class to handle the new cap meta box on the edit/new role screen.
 *
 * @since  1.0.0
 * @access public
 */
final class Members_Meta_Box_Custom_Cap {

	/**
	 * Holds the instances of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance;

	/**
	 * Adds our methods to the proper hooks.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	protected function __construct() {

		add_action( 'members_add_meta_boxes_role', array( $this, 'add_meta_boxes' ) );
	}

	/**
	 * Adds the meta box.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $role
	 * @return void
	 */
	public function add_meta_boxes( $role = '' ) {

		// If role isn't editable, bail.
		if ( $role && ! members_is_role_editable( $role ) )
			return;

		// Add the meta box.
		add_meta_box( 'newcapdiv', esc_html__( 'Custom Capability', 'members' ), array( $this, 'meta_box' ), 'members_edit_role', 'side', 'core' );

		// Print Underscore template in the footer.
		add_action( 'admin_footer', array( $this, 'print_template' ) );
	}

	/**
	 * Outputs the meta box HTML.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function meta_box() { ?>

		<p>
			<input type="text" id="members-new-cap-field" class="widefat" />
		</p>

		<p>
			<button type="button" class="button-secondary" id="members-add-new-cap"><?php esc_html_e( 'Add New', 'members' ); ?></button>
		</p>
	<?php }

	/**
	 * Outputs the Underscore JS template.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function template() { ?>

		<tr class="members-cap-checklist">
			<td class="column-cap">
				<button type="button"><strong>{{ data.cap }}</strong></button>
				<i class="dashicons <?php echo is_rtl() ? 'dashicons-arrow-left' : 'dashicons-arrow-right'; ?>"></i>
			</td>

			<td class="column-grant">
				<span class="screen-reader-text">{{{ data.labels.grant_cap }}}</span>
				<input type="checkbox" name="grant-new-caps[]" data-grant-cap="{{ data.cap }}" value="{{ data.cap }}" <# if ( data.is_granted_cap ) { #>checked="checked"<# } #> />
			</td>

			<td class="column-deny">
				<span class="screen-reader-text">{{{ data.labels.deny_cap }}}</span>
				<input type="checkbox" name="deny-new-caps[]" data-deny-cap="{{ data.cap }}" value="{{ data.cap }}" <# if ( data.is_denied_cap ) { #>checked="checked"<# } #> />
			</td>
		</tr>
	<?php }

	/**
	 * Prints the Underscore JS `<script>` wrapper and template.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function print_template() { ?>

		<script type="text/html" id="tmpl-members-new-cap-control">
			<?php $this->template(); ?>
		</script>
	<?php }

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		if ( ! self::$instance )
			self::$instance = new self;

		return self::$instance;
	}
}

Members_Meta_Box_Custom_Cap::get_instance();
