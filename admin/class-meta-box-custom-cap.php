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

		<script type="text/javascript">
			jQuery( document ).ready( function() {

				jQuery( '#members-add-new-cap' ).prop( 'disabled', true );

				jQuery( '#members-new-cap-field' ).on( 'input',
					function() {

						if ( jQuery( this ).val() ) {

							jQuery( '#members-add-new-cap' ).prop( 'disabled', false );
						} else {
							jQuery( '#members-add-new-cap' ).prop( 'disabled', true );
						}
					}
				);

				jQuery( '#members-add-new-cap' ).click(
					function() {
						var new_cap = jQuery( '#members-new-cap-field' ).val();

						// Sanitize the new cap.
						new_cap = new_cap.replace( /<.*?>/g, '' ).replace( /\s/g, '_' ).replace( /[^a-zA-Z0-9_]/g, '' );

						if ( new_cap ) {

							jQuery( 'a[href="#members-tab-custom"]' ).trigger( 'click' );

							var new_cap_template = wp.template( 'members-new-cap-control' );

							var data = { cap : new_cap, is_granted_cap : true, is_denied_cap : false };

							jQuery( '#members-tab-custom tbody' ).prepend(
								new_cap_template( data )
							);

							jQuery( '#members-new-cap-field' ).val( '' );

							jQuery( '#members-add-new-cap' ).prop( 'disabled', true );

							jQuery( '.members-cap-checklist input[data-deny-cap="' + new_cap + '"]' ).trigger( 'change' );
						}
					}
				);
			} );
		</script>
	<?php }

	public function template() { ?>

		<tr class="members-cap-checklist">
			<td class="members-cap-name">
				<label><strong>{{ data.cap }}</strong></label>
			</td>

			<td class="column-cb">
				<input type="checkbox" name="grant-new-caps[]" data-grant-cap="{{ data.cap }}" value="{{ data.cap }}" <# if ( data.is_granted_cap ) { #>checked="checked"<# } #> />
			</td>

			<td class="column-cb">
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
