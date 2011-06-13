<?php
/**
 * Creates and handles all of the functionality needed for the 'Members Settings' page in the WordPress admin.
 *
 * @package Members
 * @subpackage Admin
 */

/* Set up the administration functionality. */
add_action( 'admin_menu', 'members_settings_page_setup' );

/**
 * Initializes and sets up the main plugin settings page.
 *
 * @since 0.2.0
 */
function members_settings_page_setup() {
	global $members;

	/* If no settings are available, add the default settings to the database. */
	if ( false === get_option( 'members_settings' ) )
		add_option( 'members_settings', members_get_default_settings(), '', 'yes' );

	/* Register the plugin settings. */
	add_action( 'admin_init', 'members_register_settings' );

	/* Add Members settings page. */
	$members->settings_page = add_submenu_page( 'options-general.php', esc_attr__( 'Members Settings', 'members' ), esc_attr__( 'Members', 'members' ), apply_filters( 'members_settings_capability', 'manage_options' ), 'members-settings', 'members_settings_page' );

	/* Add media for the settings page. */
	add_action( 'admin_enqueue_scripts', 'members_admin_enqueue_style' );
	add_action( 'admin_enqueue_scripts', 'members_settings_page_media' );
	add_action( "admin_head-{$members->settings_page}", 'members_settings_page_scripts' );

	/* Load the meta boxes. */
	add_action( "load-{$members->settings_page}", 'members_settings_page_load_meta_boxes' );

	/* Create a hook for adding meta boxes. */
	add_action( "load-{$members->settings_page}", 'members_settings_page_add_meta_boxes' );
}

/**
 * Registers the Members plugin settings with WordPress.
 *
 * @since 0.2.0
 */
function members_register_settings() {
	register_setting( 'members_settings', 'members_settings', 'members_validate_settings' );
}

/**
 * Executes the 'add_meta_boxes' action hook because WordPress doesn't fire this on custom admin pages.
 *
 * @since 0.2.0
 */
function members_settings_page_add_meta_boxes() {
	global $members;
	$plugin_data = get_plugin_data( MEMBERS_DIR . 'members.php' );
	do_action( 'add_meta_boxes', $members->settings_page, $plugin_data );
}

/**
 * Loads the plugin settings page meta boxes.
 *
 * @since 0.2.0
 */
function members_settings_page_load_meta_boxes() {
	require_once( MEMBERS_ADMIN . 'meta-box-plugin-settings.php' );
}

/**
 * Function for validating the settings input from the plugin settings page.
 *
 * @since 0.2.0
 */
function members_validate_settings( $input ) {

	/* Check if the role manager is active. */
	$settings['role_manager'] = ( isset( $input['role_manager'] ) ? 1 : 0 );

	/* Check if the content permissions feature is active. */
	$settings['content_permissions'] = ( isset( $input['content_permissions'] ) ? 1 : 0 );

	/* Set the content permissions error text and kill evil scripts. */
	if ( current_user_can( 'unfiltered_html' ) && isset( $input['content_permissions_error'] ) )
		$settings['content_permissions_error'] = stripslashes( wp_filter_post_kses( addslashes( $input['content_permissions_error'] ) ) );

	elseif ( isset( $input['content_permissions_error'] ) )
		$settings['content_permissions_error'] = $input['content_permissions_error'];

	/* Check if the login form and users widgets are active. */
	$settings['login_form_widget'] = ( isset( $input['login_form_widget'] ) ? 1 : 0 );
	$settings['users_widget'] = ( isset( $input['users_widget'] ) ? 1 : 0 );

	/* Check if the private blog and private feed features are active. */
	$settings['private_blog'] = ( isset( $input['private_blog'] ) ? 1 : 0 );
	$settings['private_feed'] = ( isset( $input['private_feed'] ) ? 1 : 0 );

	/* Set the private feed error text and kill evil scripts. */
	if ( current_user_can( 'unfiltered_html' ) && isset( $input['private_feed_error'] ) )
		$settings['private_feed_error'] = stripslashes( wp_filter_post_kses( addslashes( $input['private_feed_error'] ) ) );

	elseif ( isset( $input['private_feed_error'] ) )
		$settings['private_feed_error'] = $input['private_feed_error'];

	/* Return the validated/sanitized settings. */
	return $settings;
}

/**
 * Displays the HTML and meta boxes for the plugin settings page.
 *
 * @since 0.2.0
 */
function members_settings_page() {
	global $members; ?>

	<div class="wrap">

		<?php screen_icon(); ?>

		<h2><?php _e( 'Members Plugin Settings', 'members' ); ?></h2>

		<div id="poststuff">

			<form method="post" action="options.php">

				<?php settings_fields( 'members_settings' ); ?>
				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>

				<div class="metabox-holder">
					<div class="post-box-container column-1 normal"><?php do_meta_boxes( $members->settings_page, 'normal', null ); ?></div>
					<div class="post-box-container column-2 side"><?php do_meta_boxes( $members->settings_page, 'side', null ); ?></div>
				</div>

				<?php submit_button( esc_attr__( 'Update Settings', 'members' ) ); ?>

			</form>

		</div><!-- #poststuff -->

	</div><!-- .wrap --><?php
}

/**
 * Loads needed JavaScript files for handling the meta boxes on the settings page.
 *
 * @since 0.2.0
 * @param string $hook_suffix The hook for the current page in the admin.
 */
function members_settings_page_media( $hook_suffix ) {
	global $members;

	if ( isset( $members->settings_page ) && $hook_suffix == $members->settings_page ) {
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );
	}
}

/**
 * Loads JavaScript for handling the open/closed state of each meta box.
 *
 * @since 0.2.0
 * @global $members The path of the settings page.
 */
function members_settings_page_scripts() {
	global $members; ?>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			postboxes.add_postbox_toggles( '<?php echo $members->settings_page; ?>' );
		});
		//]]>
	</script>
<?php }

?>