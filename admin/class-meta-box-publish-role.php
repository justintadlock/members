<?php

final class Members_Meta_Box_Publish_Role {

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

		add_meta_box( 'submitdiv', esc_html__( 'Update Role', 'members' ), array( $this, 'meta_box' ), 'members_edit_role', 'side', 'high' );
	}

	public function meta_box( $role ) { ?>

		<div class="submitbox" id="submitpost">

			<div id="misc-publishing-actions">

				<div class="misc-pub-section misc-pub-section-users">
					<i class="dashicons dashicons-admin-users"></i>
					<?php esc_html_e( 'Users:', 'members' ); ?>
					<strong class="user-count"><?php echo members_get_role_user_count( $role->name ); ?></strong>
				</div>

				<div class="misc-pub-section misc-pub-section-granted">
					<i class="dashicons dashicons-yes"></i>
					<?php esc_html_e( 'Granted:', 'members' ); ?>
					<strong class="granted-count"><?php echo members_get_role_granted_cap_count( $role->name ); ?></strong>
				</div>

				<div class="misc-pub-section misc-pub-section-denied">
					<i class="dashicons dashicons-no"></i>
					<?php esc_html_e( 'Denied:', 'members' ); ?>
					<strong class="denied-count"><?php echo members_get_role_denied_cap_count( $role->name ); ?></strong>
				</div>

			</div><!-- #misc-publishing-actions -->

			<div id="major-publishing-actions">

				<div id="delete-action">

					<?php if ( members_is_role_editable( $role->name ) ) : ?>
						<a class="submitdelete deletion" href="<?php echo members_get_delete_role_url( $role->name ); ?>"><?php echo esc_html_x( 'Delete', 'delete role', 'members' ); ?></a>
					<?php endif; ?>

					<script type="text/javascript">
						jQuery( '.submitdelete' ).click( function() {
							return window.confirm( '<?php esc_html_e( 'Are you sure you want to delete this role? This is a permanent action and cannot be undone.', 'members' ); ?>' );
						} );
					</script>

				</div>

				<div id="publishing-action">

					<span class="spinner"></span>

					<?php if ( members_is_role_editable( $role->name ) ) : ?>
						<?php submit_button( esc_attr__( 'Update', 'members' ), 'primary', 'publish', false, array( 'id' => 'publish' ) ); ?>
					<?php endif; ?>

				</div><!-- #publishing-action -->

				<div class="clear"></div>

			</div><!-- #major-publishing-actions -->

		</div><!-- .submitbox -->
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

Members_Meta_Box_Publish_Role::get_instance();
