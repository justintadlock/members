<?php
/**
 * Creates and adds the meta boxes to the Members Settings page in the admin.
 *
 * @package Members
 * @subpackage Admin
 */

/* Add the meta boxes for the settings page on the 'add_meta_boxes' hook. */
add_action( 'add_meta_boxes', 'members_settings_page_create_meta_boxes' );

/**
 * Adds the meta boxes to the Members plugin settings page.
 *
 * @since 0.2.0
 */
function members_settings_page_create_meta_boxes() {
	global $members;

	/* Add the 'About' meta box. */
	add_meta_box( 'members-about', _x( 'About', 'meta box', 'members' ), 'members_meta_box_display_about', $members->settings_page, 'side', 'default' );

	/* Add the 'Donate' meta box. */
	add_meta_box( 'members-donate', _x( 'Like this plugin?', 'meta box', 'members' ), 'members_meta_box_display_donate', $members->settings_page, 'side', 'high' );

	/* Add the 'Support' meta box. */
	add_meta_box( 'members-support', _x( 'Support', 'meta box', 'members' ), 'members_meta_box_display_support', $members->settings_page, 'side', 'low' );

	/* Add the 'Role Manager' meta box. */
	add_meta_box( 'members-role-manager', _x( 'Role Manager', 'meta box', 'members' ), 'members_meta_box_display_role_manager', $members->settings_page, 'normal', 'high' );

	/* Add the 'Content Permissions' meta box. */
	add_meta_box( 'members-content-permissions', _x( 'Content Permissions', 'meta box', 'members' ), 'members_meta_box_display_content_permissions', $members->settings_page, 'normal', 'high' );

	/* Add the 'Sidebar Widgets' meta box. */
	add_meta_box( 'members-widgets', _x( 'Sidebar Widgets', 'meta box', 'members' ), 'members_meta_box_display_widgets', $members->settings_page, 'normal', 'high' );

	/* Add the 'Private Site' meta box. */
	add_meta_box( 'members-private-site', _x( 'Private Site', 'meta box', 'members' ), 'members_meta_box_display_private_site', $members->settings_page, 'normal', 'high' );
}

/**
 * Displays the about plugin meta box.
 *
 * @since 0.2.0
 */
function members_meta_box_display_about( $object, $box ) {

	$plugin_data = get_plugin_data( MEMBERS_DIR . 'members.php' ); ?>

	<p>
		<strong><?php _e( 'Version:', 'members' ); ?></strong> <?php echo $plugin_data['Version']; ?>
	</p>
	<p>
		<strong><?php _e( 'Description:', 'members' ); ?></strong>
	</p>
	<p>
		<?php echo $plugin_data['Description']; ?>
	</p>
<?php }

/**
 * Displays the donation meta box.
 *
 * @since 0.2.0
 */
function members_meta_box_display_donate( $object, $box ) { ?>

	<p><?php _e( "Here's how you can give back:", 'members' ); ?></p>

	<ul>
		<li><a href="http://wordpress.org/extend/plugins/members" title="<?php esc_attr_e( 'Members on the WordPress plugin repository', 'members' ); ?>"><?php _e( 'Give the plugin a good rating.', 'members' ); ?></a></li>
		<li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=3687060" title="<?php esc_attr_e( 'Donate via PayPal', 'members' ); ?>"><?php _e( 'Donate a few dollars.', 'members' ); ?></a></li>
		<li><a href="http://amzn.com/w/31ZQROTXPR9IS" title="<?php esc_attr_e( "Justin Tadlock's Amazon Wish List", 'members' ); ?>"><?php _e( 'Get me something from my wish list.', 'members' ); ?></a></li>
	</ul>
<?php
}

/**
 * Displays the support meta box.
 *
 * @since 0.2.0
 */
function members_meta_box_display_support( $object, $box ) { ?>
	<p>
		<?php printf( __( 'Support for this plugin is provided via the support forums at %1$s. If you need any help using it, please ask your support questions there.', 'members' ), '<a href="http://themehybrid.com/support" title="' . esc_attr__( 'Theme Hybrid Support Forums', 'members' ) . '">' . __( 'Theme Hybrid', 'members' ) . '</a>' ); ?>
	</p>
<?php }

/**
 * Displays the role manager meta box.
 *
 * @since 0.2.0
 */
function members_meta_box_display_role_manager( $object, $box ) { ?>

	<p>
		<input type="checkbox" name="members_settings[role_manager]" id="members_settings-role_manager" value="1" <?php checked( 1, members_get_setting( 'role_manager' ) ); ?> /> 
		<label for="members_settings-role_manager"><?php _e( 'Enable the role manager.', 'members' ); ?></label>
	</p>
	<p>
		<span class="howto"><?php _e( 'Your roles and capabilities will not revert back to their previous settings after deactivating or uninstalling this plugin, so use this feature wisely.', 'members' ); ?></span>
	</p>

<?php }

/**
 * Displays the content permissions meta box.
 *
 * @since 0.2.0
 */
function members_meta_box_display_content_permissions( $object, $box ) { ?>

	<p>
		<input type="checkbox" name="members_settings[content_permissions]" id="members_settings-content_permissions" value="1" <?php checked( 1, members_get_setting( 'content_permissions' ) ); ?> /> 
		<label for="members_settings-content_permissions"><?php _e( 'Enable the content permissions feature.', 'members' ); ?></label>
	</p>

	<p>
		<label for="members_settings-content_permissions_error"><?Php _e( 'Default post error message:', 'members' ); ?></label>
		<textarea name="members_settings[content_permissions_error]" id="members_settings-content_permissions_error"><?php echo esc_textarea( members_get_setting( 'content_permissions_error' ) ); ?></textarea>
		<label for="members_settings-content_permissions_error"><?php _e( 'You can use <abbr title="Hypertext Markup Language">HTML</abbr> and/or shortcodes to create a custom error message for users that don\'t have permission to view posts.', 'members' ); ?></label>
	</p>

<?php }

/**
 * Displays the widgets meta box.
 *
 * @since 0.2.0
 */
function members_meta_box_display_widgets( $object, $box ) { ?>

	<p>
		<input type="checkbox" name="members_settings[login_form_widget]" id="members_settings-login_form_widget" value="1" <?php checked( 1, members_get_setting( 'login_form_widget' ) ); ?> /> 
		<label for="members_settings-login_form_widget"><?php _e( 'Enable the login form widget.', 'members' ); ?></label>
	</p>

	<p>
		<input type="checkbox" name="members_settings[users_widget]" id="members_settings-users_widget" value="1" <?php checked( 1, members_get_setting( 'users_widget' ) ); ?> /> 
		<label for="members_settings-users_widget"><?php _e( 'Enable the users widget.', 'members' ); ?></label>
	</p>

<?php }

/**
 * Displays the private site meta box.
 *
 * @since 0.2.0
 */
function members_meta_box_display_private_site( $object, $box ) { ?>

	<p>
		<input type="checkbox" name="members_settings[private_blog]" id="members_settings-private_blog" value="1" <?php checked( 1, members_get_setting( 'private_blog' ) ); ?> /> 
		<label for="members_settings-private_blog"><?php _e( 'Redirect all logged-out users to the login page before allowing them to view the site.', 'members' ); ?></label>
	</p>

	<p>
		<input type="checkbox" name="members_settings[private_feed]" id="members_settings-private_feed" value="1" <?php checked( 1, members_get_setting( 'private_feed' ) ); ?> /> 
		<label for="members_settings-private_feed"><?php _e( 'Show error message for feed items.', 'members' ); ?></label>
	</p>

	<p>
		<label for="members_settings-private_feed_error"><?php _e( 'Feed error message:', 'members' ); ?></label>
		<textarea name="members_settings[private_feed_error]" id="members_settings-private_feed_error"><?php echo esc_textarea( members_get_setting( 'private_feed_error' ) ); ?></textarea>
		<br />
		<label for="members_settings-private_feed_error"><?php _e( 'You can use <abbr title="Hypertext Markup Language">HTML</abbr> and/or shortcodes to create a custom error message to display instead of feed item content.', 'members' ); ?></label>
	</p>

<?php }

?>