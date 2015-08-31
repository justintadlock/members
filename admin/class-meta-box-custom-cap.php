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

	public function add_meta_boxes() {

		add_meta_box( 'newcapdiv', esc_html__( 'Custom Capability', 'members' ), array( $this, 'meta_box' ), 'members_edit_role', 'side', 'core' );
	}

	public function meta_box( $role ) { ?>

		<p class="description">Note: This doesn't work yet.</p>

		<p>
			<input type="text" class="widefat" />
		</p>

		<p>
			<a class="button-secondary" id="members-add-new-cap"><?php esc_html_e( 'Add New', 'members' ); ?></a>
		</p>

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
