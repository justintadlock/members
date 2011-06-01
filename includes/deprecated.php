<?php
/**
 * Deprecated functions that are being phased out completely or should be replaced with other functions.
 *
 * @package Members
 * @subpackage Functions
 */

/**
 * @since 0.1.0
 * @deprecated 0.2.0 This is theme functionality. Let's just leave it to themes.
 */
function members_author_profile() {
	_deprecated_function( __FUNCTION__, '0.2.0', '' ); ?>

	<div class="author-profile vcard">
		<?php echo get_avatar( get_the_author_meta( 'user_email' ), '100', '', get_the_author_meta( 'display_name' ) ); ?>
		<h4 class="author-name fn n"><?php the_author_posts_link(); ?></h4>
		<p class="author-description author-bio">
			<?php echo the_author_meta( 'description' ); ?>
		</p>
	</div>
<?php
}

/**
 * @since 0.1.0
 * @deprecated 0.2.0 Use wp_login_form() instead.
 */
function members_login_form() {
	_deprecated_function( __FUNCTION__, '0.2.0', 'wp_login_form' );

	wp_login_form( array( 'echo' => true ) );
}

/**
 * @since 0.1.0
 * @deprecated 0.2.0
 */
function members_get_login_form() {
	_deprecated_function( __FUNCTION__, '0.2.0', 'wp_login_form' );

	wp_login_form( array( 'echo' => false ) );
}

if ( !function_exists( 'has_role' ) ) {

	/**
	 * @since 0.1.0
	 * @deprecated 0.2.0
	 */
	function has_role( $role, $user_id ) {
		_deprecated_function( __FUNCTION__, '0.2.0', 'user_can' );

		return user_can( $user_id, $role );
	}
}

if ( !function_exists( 'current_user_has_role' ) ) {

	/**
	 * @since 0.1.0
	 * @deprecated 0.2.0
	 */
	function current_user_has_role() {
		_deprecated_function( __FUNCTION__, '0.2.0', 'current_user_can' );

		return current_user_can( $role );
	}
}

/**
 * @since 0.1.0
 * @deprecated 0.2.0
 */
function members_get_avatar_shortcode( $attr ) {
	_deprecated_function( __FUNCTION__, '0.2.0', '' );

	/* Set up our default attributes. */
	$defaults = array(
		'id' => '',
		'email' => '',
		'size' => 96,
		'default' => '',
		'alt' => ''
	);

	/* Merge the input attributes and the defaults. */
	extract( shortcode_atts( $defaults, $attr ) );

	/* If an email was input, use it.  Else, use the ID. */
	$id_or_email = ( !empty( $email ) ? $email : $id );

	/* Return the avatar. */
	return get_avatar( $id_or_email, $size, $default, $alt );
}

?>