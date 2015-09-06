<?php

final class Members_Meta_Box_Custom_Cap {

	/**
	 * Holds the instances of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance;

	protected function __construct() {

		add_action( 'members_add_meta_boxes_role', array( $this, 'add_meta_boxes' ) );
	}

	public function add_meta_boxes( $role = '' ) {

		if ( ! $role || members_is_role_editable( $role ) )
			add_meta_box( 'newcapdiv', esc_html__( 'Custom Capability', 'members' ), array( $this, 'meta_box' ), 'members_edit_role', 'side', 'core' );
	}

	public function meta_box( $role ) { ?>

		<p>
			<input type="text" id="members-new-cap-field" class="widefat" />
		</p>

		<p>
			<button type="button" class="button-secondary" id="members-add-new-cap"><?php esc_html_e( 'Add New', 'members' ); ?></button>
		</p>

		<script type="text/html" id="tmpl-members-new-cap-control">
			<?php $this->template(); ?>
		</script>
	<?php }

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
