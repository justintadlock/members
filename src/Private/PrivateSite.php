<?php

namespace Members\Private;

use Members\Contracts\Bootable;

class PrivateSite implements Bootable {

	public function boot() {

		# Redirects users to the login page.
		add_action( 'template_redirect', [ $this, 'maybeRedirect' ], 0 );

		# Authenticate when accessing the REST API.
		add_filter( 'rest_authentication_errors', [ $this, 'restApi' ], 95 );

		# Disable content in feeds if the feed should be private.
		add_filter( 'the_content_feed', [ $this, 'feed' ], 95 );
		add_filter( 'the_excerpt_rss',  [ $this, 'feed' ], 95 );
		add_filter( 'comment_text_rss', [ $this, 'feed' ], 95 );

		# Filters for the feed error message.
		add_filter( 'members/private/feed/message', [ $GLOBALS['wp_embed'], 'run_shortcode' ],   5 );
		add_filter( 'members/private/feed/message', [ $GLOBALS['wp_embed'], 'autoembed'     ],   5 );
		add_filter( 'members/private/feed/message',                         'wptexturize',       10 );
		add_filter( 'members/private/feed/message',                         'convert_smilies',   15 );
		add_filter( 'members/private/feed/message',                         'convert_chars',     20 );
		add_filter( 'members/private/feed/message',                         'wpautop',           25 );
		add_filter( 'members/private/feed/message',                         'do_shortcode',      30 );
		add_filter( 'members/private/feed/message',                         'shortcode_unautop', 35 );
	}

	/**
	 * Conditional tag to see if we have a private blog.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return bool
	 */
	public function isPrivateBlog() {

		$is_private = apply_filters(
			'members/private/blog',
			members_get_setting( 'private_blog' )
		);

		return apply_filters( 'members_is_private_blog', $is_private );
	}

	/**
	 * Conditional tag to see if we have a private feed.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return bool
	 */
	public function isPrivateFeed() {

		$is_private = apply_filters(
			'members/private/feed',
			members_get_setting( 'private_feed' )
		);

		return apply_filters( 'members_is_private_feed', $is_private );
	}

	/**
	 * Conditional tag to see if we have a private REST API
	 *
	 * @since  2.0.0
	 * @access public
	 * @return bool
	 */
	public function isPrivateRestApi() {

		$is_private = apply_filters(
			'members/private/rest',
			members_get_setting( 'private_rest_api' )
		);

		return apply_filters( 'members_is_private_rest_api', $is_private );
	}

	/**
	 * Redirects users that are not logged in to the 'wp-login.php' page.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function maybeRedirect() {

		// If private blog is not enabled, bail.
		if ( ! $this->isPrivateBlog() ) {
			return;
		}

		// If this is a multisite instance and the user is logged into the network.
		if ( is_multisite() && is_user_logged_in() && ! is_user_member_of_blog() && ! is_super_admin() ) {
			$this->msBlogDie();
		}

		// Check if the private blog feature is active and if the user is not logged in.
		if ( ! is_user_logged_in() && $this->isPrivatePage() ) {

			auth_redirect();
			exit;
		}
	}

	/**
	 * Function for determining whether a page should be public even though we're in private
	 * site mode.  Plugin devs can filter this to make specific pages public.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return bool
	 */
	public function isPrivatePage() {

		$is_private = true;

		if ( function_exists( 'bp_is_current_component' ) && ( bp_is_current_component( 'register' ) || bp_is_current_component( 'activate' ) ) )
			$is_private = false;

		// WooCommerce support.
		if ( class_exists( 'WooCommerce' ) ) {
			$page_id = get_option( 'woocommerce_myaccount_page_id' );

			if ( $page_id && is_page( $page_id ) )
				$is_private = false;
		}

		$is_private = apply_filters( 'members/private/page', $is_private );

		return apply_filters( 'members_is_private_page', $is_private );
	}

	/**
	 * Blocks feed items if the user has selected the private feed feature.
	 *
	 * @since  0.2.0
	 * @access public
	 * @param  string  $content
	 * @return string
	 */
	public function feed( $content ) {

		return $this->isPrivateFeed() ? $this->feedMessage() : $content;
	}

	/**
	 * Returns the private feed error message.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return string
	 */
	public function feedMessage() {

		return apply_filters(
			'members/private/feed/message',
			members_get_setting( 'private_feed_error' )
		);
	}

	/**
	 * Returns an error if the REST API is accessed by an unauthenticated user.
	 *
	 * @link   https://developer.wordpress.org/rest-api/using-the-rest-api/frequently-asked-questions/#require-authentication-for-all-requests
	 * @since  2.0.0
	 * @access public
	 * @param  object  $result
	 * @return object
	 */
	public function restApi( $result ) {

		if ( empty( $result ) && members_is_private_rest_api() && ! is_user_logged_in() ) {

			return new WP_Error(
				'rest_not_logged_in',
				esc_html(
					apply_filters(
						'members/private/rest/message',
						__( 'You are not currently logged in.', 'members' )
					)
				),
				array( 'status' => 401 )
			);
		}

		return $result;
	}

	/**
	 * Outputs an error message if a user attempts to access a site that they do not have
	 * access to on multisite.
	 *
	 * @since  2.0.0
	 * @access public
	 * @return void
	 */
	public function msBlogDie() {

		$blogs = get_blogs_of_user( get_current_user_id() );

		$blogname = get_bloginfo( 'name' );

		$message = __( 'You do not currently have access to the "%s" site. If you believe you should have access, please contact your network administrator.', 'members' );

		if ( empty( $blogs ) )
			wp_die( sprintf( $message, $blogname ), 403 );

		$output = '<p>' . sprintf( $message, $blogname ) . '</p>';

		$output .= sprintf( '<p>%s</p>', __( 'If you reached this page by accident and meant to visit one of your own sites, try one of the following links.', 'members' ) );

		$output .= '<ul>';

		foreach ( $blogs as $blog )
			$output .= sprintf( '<li><a href="%s">%s</a></li>', esc_url( get_home_url( $blog->userblog_id ) ), esc_html( $blog->blogname ) );

		$output .= '</ul>';

		wp_die( $output, 403 );
	}
