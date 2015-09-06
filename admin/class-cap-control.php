<?php
/**
 * Capability control class for use in the edit capabilities tabs.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/**
 * Cap control class.
 *
 * @since  1.0.0
 * @access public
 */
final class Members_Cap_Control {

	/**
	 * Stores the cap tabs object.
	 *
	 * @see    Members_Cap_Tabs
	 * @since  1.0.0
	 * @access public
	 * @var    object
	 */
	public $manager;

	/**
	 * Name of the capability the control is for.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $cap = '';

	/**
	 * ID of the section the control is for.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $section = '';

	/**
	 * Array of data to pass as a json object to the Underscore template.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $json = array();

	/**
	 * Creates a new control object.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $manager
	 * @param  string  $cap
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( $manager, $cap, $args = array() ) {

		foreach ( array_keys( get_object_vars( $this ) ) as $key ) {

			if ( isset( $args[ $key ] ) )
				$this->$key = $args[ $key ];
		}

		$this->manager = $manager;
		$this->cap     = $cap;
	}

	/**
	 * Returns the json array.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function json() {
		$this->to_json();
		return $this->json;
	}

	/**
	 * Adds custom data to the json array.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function to_json() {

		$is_editable = $this->manager->role ? members_is_role_editable( $this->manager->role->name ) : true;

		$this->json['cap']      = $this->cap;
		$this->json['readonly'] = $is_editable ? '' : ' disabled="disabled" readonly="readonly"';

		$this->json['labels'] = array(
			'grant_cap' => sprintf( esc_html__( 'Grant %s capability', 'members' ), "<code>{$this->cap}</code>" ),
			'deny_cap'  => sprintf( esc_html__( 'Deny %s capability',  'members' ), "<code>{$this->cap}</code>" ),
		);

		$this->json['is_granted_cap'] = isset( $this->manager->has_caps[ $this->cap ] ) && $this->manager->has_caps[ $this->cap ];
		$this->json['is_denied_cap']  = isset( $this->manager->has_caps[ $this->cap ] ) && false === $this->manager->has_caps[ $this->cap ];
	}

	/**
	 * Outputs the Underscore template.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function template() { ?>

		<tr class="members-cap-checklist">
			<td class="members-cap-name">
				<button type="button"><strong>{{ data.cap }}</strong> <i class="dashicons <?php echo is_rtl() ? 'dashicons-arrow-left' : 'dashicons-arrow-right'; ?>"></i></button>
			</td>

			<td class="column-cb">
				<span class="screen-reader-text">{{{ data.labels.grant_cap }}}</span>
				<input {{{ data.readonly }}} type="checkbox" name="grant-caps[]" data-grant-cap="{{ data.cap }}" value="{{ data.cap }}" <# if ( data.is_granted_cap ) { #>checked="checked"<# } #> />
			</td>

			<td class="column-cb">
				<span class="screen-reader-text">{{{ data.labels.deny_cap }}}</span>
				<input {{{ data.readonly }}} type="checkbox" name="deny-caps[]" data-deny-cap="{{ data.cap }}" value="{{ data.cap }}" <# if ( data.is_denied_cap ) { #>checked="checked"<# } #> />
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

		<script type="text/html" id="tmpl-members-cap-control">
			<?php $this->template(); ?>
		</script>
	<?php }
}
