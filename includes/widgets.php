<?php
/**
 * @package Members
 * @subpackage Functions
 */

add_action( 'widgets_init', 'members_register_widgets' );

function members_register_widgets() {

	if ( members_get_setting( 'login_form_widget' ) ) {
		require_once( MEMBERS_INCLUDES . 'widget-login-form.php' );
		register_widget( 'Members_Widget_Login' );
	}

	if ( members_get_setting( 'users_widget' ) ) {
		require_once( MEMBERS_INCLUDES . 'widget-users.php' );
		register_widget( 'Members_Widget_users' );
	}
}

?>