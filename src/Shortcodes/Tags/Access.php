<?php

namespace Members\Shortcodes\Tags;

class Access extends Shortcode {

	public function tag() {
		return 'members_access';
	}

	public function aliases() {
		return [ 'access' ];
	}

	public function render( $attr = [], $content = null ) {

		// If there's no content or if viewing a feed, return an empty string.
		if ( is_null( $content ) || is_feed() ) {
			return '';
		}

		$user_can = false;

		// Merge the input attributes and the defaults.
		$attr = $this->options( $attr, [
			'capability' => '',  // Single capability or comma-separated multiple capabilities.
			'role'       => '',  // Single role or comma-separated multiple roles.
			'user_id'    => '',  // Single user ID or comma-separated multiple IDs.
			'user_name'  => '',  // Single user name or comma-separated multiple names.
			'user_email' => '',  // Single user email or comma-separated multiple emails.
			'operator'   => 'or' // Only the `!` operator is supported for now.  Everything else falls back to `or`.
		] );

		// Get the operator.
		$operator = strtolower( $attr['operator'] );

		// If the current user has the capability, show the content.
		if ( $attr['capability'] ) {

			// Get the capabilities.
			$caps = explode( ',', $attr['capability'] );

			if ( '!' === $operator )
				return members_current_user_can_any( $caps ) ? '' : do_shortcode( $content );

			return members_current_user_can_any( $caps ) ? do_shortcode( $content ) : '';
		}

		// If the current user has the role, show the content.
		if ( $attr['role'] ) {

			// Get the roles.
			$roles = explode( ',', $attr['role'] );

			if ( '!' === $operator )
				return members_current_user_has_role( $roles ) ? '' : do_shortcode( $content );

			return members_current_user_has_role( $roles ) ? do_shortcode( $content ) : '';
		}

		$user_id = 0;
		$user_name = $user_email = '';

		if ( is_user_logged_in() ) {

			$user       = wp_get_current_user();
			$user_id    = get_current_user_id();
			$user_name  = $user->user_login;
			$user_email = $user->user_email;
		}

		// If the current user has one of the user ids.
		if ( $attr['user_id'] ) {

			// Get the user IDs.
			$ids = array_map( 'trim', explode( ',', $attr['user_id'] ) );

			if ( '!' === $operator ) {
				return in_array( $user_id, $ids ) ? '' : do_shortcode( $content );
			}

			return in_array( $user_id, $ids ) ? do_shortcode( $content ) : '';
		}

		// If the current user has one of the user names.
		if ( $attr['user_name'] ) {

			// Get the user names.
			$names = array_map( 'trim', explode( ',', $attr['user_name'] ) );

			if ( '!' === $operator ) {
				return in_array( $user_name, $names ) ? '' : do_shortcode( $content );
			}

			return in_array( $user_name, $names ) ? do_shortcode( $content ) : '';
		}

		// If the current user has one of the user emails.
		if ( $attr['user_email'] ) {

			// Get the user emails.
			$emails = array_map( 'trim', explode( ',', $attr['user_email'] ) );

			if ( '!' === $operator ) {
				return in_array( $user_email, $emails ) ? '' : do_shortcode( $content );
			}

			return in_array( $user_email, $emails ) ? do_shortcode( $content ) : '';
		}

		// Return an empty string if we've made it to this point.
		return '';
	}
}
