<?php

final class Members_Cap_Section {

	public $json = array();
	public $section = '';
	public $manager;
	public $icon = 'dashicons-admin-generic';
	public $label = '';

	public function __construct( $manager, $section, $args = array() ) {

		foreach ( array_keys( get_object_vars( $this ) ) as $key ) {

			if ( isset( $args[ $key ] ) )
				$this->$key = $args[ $key ];
		}

		$this->manager = $manager;
		$this->section = $section;
	}

	public function json() {
		$this->to_json();
		return $this->json;
	}

	public function to_json() {

		$is_editable = $this->manager->role ? members_is_role_editable( $this->manager->role->name ) : true;

		$this->json['id']    = $this->section;
		$this->json['class'] = 'members-tab-content' . ( $is_editable ? ' editable-role' : '' );

		$this->json['labels'] = array(
			'cap'   => esc_html__( 'Capability', 'members' ),
			'grant' => esc_html__( 'Grant',      'members' ),
			'deny'  => esc_html__( 'Deny',       'members' )
		);
	}

	public function template() { ?>

		<div id="members-tab-{{ data.id }}" class="{{ data.class }}">

			<table class="wp-list-table widefat fixed members-roles-select">

				<thead>
					<tr>
						<th class="column-cap">{{ data.labels.cap }}</th>
						<th class="column-cb">{{ data.labels.grant }}</th>
						<th class="column-cb">{{ data.labels.deny }}</th>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<th class="column-cap">{{ data.labels.cap }}</th>
						<th class="column-cb">{{ data.labels.grant }}</th>
						<th class="column-cb">{{ data.labels.deny }}</th>
					</tr>
				</tfoot>

				<tbody></tbody>
			</table>
		</div>
	<?php }
}
