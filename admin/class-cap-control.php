<?php

final class Members_Cap_Control {

	public $manager;
	public $cap = '';
	public $section = '';
	public $json = array();

	public function __construct( $manager, $cap, $args = array() ) {

		foreach ( array_keys( get_object_vars( $this ) ) as $key ) {

			if ( isset( $args[ $key ] ) )
				$this->$key = $args[ $key ];
		}

		$this->manager = $manager;
		$this->cap     = $cap;
	}

	public function json() {
		$this->to_json();
		return $this->json;
	}

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
}
