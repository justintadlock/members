<?php

namespace Members\Util;

class Options {

	public static function setting( $option ) {
		$defaults = static::defaults();

		$settings = wp_parse_args( get_option( 'members_settings', $defaults ), $defaults );

		return isset( $settings[ $option ] ) ? $settings[ $option ] : false;
	}

	public static function roleManagerEnabled() {
		return static::setting( 'role_manager' );
	}

	public static function explicitlyDenyCaps() {
		return static::setting( 'explicit_denied_caps' );
	}

	public static function showHumanCaps() {
		return static::setting( 'show_human_caps' );
	}

	public static function multipleUserRolesEnabled() {
		return static::setting( 'multi_roles' );
	}

	public static function contentPermissionsEnabled() {
		return static::setting( 'content_permissions' );
	}

	public static function loginWidgetEnabled() {
		return static::setting( 'login_form_widget' );
	}

	public static function usersWidgetEnabled() {
		return static::setting( 'users_widget' );
	}

	public static function isPrivateBlog() {
		return (bool) static::setting( 'private_blog' );
	}

	public static function isPrivateRestApi() {
		return (bool) static::setting( 'private_rest_api' );
	}

	public static function isPrivateFeed() {
		return (bool) static::setting( 'private_feed' );
	}

	public static function defaults() {

		return [
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
			'explicit_denied_caps' => true,
			'multi_roles'          => true,

			// @since 2.0.0
			'show_human_caps'      => true,
			'private_rest_api'     => false,
		];
	}
}
