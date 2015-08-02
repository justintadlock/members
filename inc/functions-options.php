<?php

function members_role_manager_enabled() {
	return apply_filters( 'members_role_manager_enabled', members_get_setting( 'role_manager' ) );
}

function members_cap_manager_enabled() {
	return apply_filters( 'members_cap_manager_enabled', members_get_setting( 'cap_manager' ) );
}

function members_content_permissions_enabled() {
	return apply_filters( 'members_content_permissions_enabled', members_get_setting( 'content_permissions' ) );
}

/**
 * Gets a setting from from the plugin settings in the database.
 *
 * @since  0.2.0
 * @access public
 * @return mixed
 */
function members_get_setting( $option = '' ) {

	$defaults = members_get_default_settings();

	$settings = wp_parse_args( get_option( 'members_settings', $defaults ), $defaults );

	return isset( $settings[ $option ] ) ? $settings[ $option ] : false;
}

/**
 * Returns an array of the default plugin settings.
 *
 * @since  0.2.0
 * @access public
 * @return array
 */
function members_get_default_settings() {

	return array(

		// @since 0.1.0
		'role_manager'        => 1,
		'content_permissions' => 1,
		'private_blog'        => 0,

		// @since 0.2.0
		'private_feed'              => 0,
		'login_form_widget'         => 0,
		'users_widget'              => 0,
		'content_permissions_error' => esc_html__( 'Sorry, but you do not have permission to view this content.', 'members' ),
		'private_feed_error'        => esc_html__( 'You must be logged into the site to view this content.',      'members' ),

		// @since 1.0.0
		'cap_manager' => false,
	);
}
