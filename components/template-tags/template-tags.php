<?php
/**
 * The Template Tags component is for adding functions that could be useful within the 
 * template files of a WordPress theme.
 *
 * @todo Add members_count_users( $role = '' )
 *
 * @package Members
 * @subpackage Components
 */

/**
 * Use to put an author box at the end/beginning of a post.  This template
 * tag should be used within The Loop.
 *
 * @since 0.1
 * @uses get_avatar() Gets the current author's avatar.
 * @uses get_the_author_meta() Grabs information about the author.
 */
function members_author_profile() { ?>
	<div class="author-profile vcard">
		<?php echo get_avatar( get_the_author_meta( 'user_email' ), '100', '', get_the_author_meta( 'display_name' ) ); ?>
		<h4 class="author-name fn n"><?php the_author_posts_link(); ?></h4>
		<p class="author-description author-bio">
			<?php the_author_meta( 'description' ); ?>
		</p>
	</div>
<?php
}

if ( !function_exists( 'has_role' ) ) {

	/**
	 * Checks if a given ID of a user has a specific role.
	 *
	 * @since 0.1
	 * @uses WP_User() Gets a user object based on an ID.
	 * @param $role string Role to check for against the user.
	 * @param $user_id int The ID of the user to check.
	 * @return true|false bool Whether the user has the role.
	 */
	function has_role( $role = '', $user_id = '' ) {

		/* If no role or user ID was added, return false. */
		if ( !$role || !$user_id )
			return false;

		/* Make sure the ID is an integer. */
		$user_id = (int)$user_id;

		/* Get the user object. */
		$user = new WP_User( $user_id );

		/* If the user has the role, return true. */
		if ( $user->has_cap( $role ) )
			return true;

		/* Return false if the user doesn't have the role. */
		return false;
	}
}

if ( !function_exists( 'current_user_has_role' ) ) {

	/**
	 * Checks if the currently logged-in user has a specific role.
	 *
	 * @since 0.1
	 * @uses current_user_can() Checks whether the user has the given role.
	 * @param $role string The role to check for.
	 * @return true|false bool Whether the user has the role.
	 */
	function current_user_has_role( $role = '' ) {

		/* If no role was input, return false. */
		if ( !$role )
			return false;

		/* If the current user has the role, return true. */
		if ( current_user_can( $role ) )
			return true;

		/* If the current user doesn't have the role, return false. */
		return false;
	}
}

?>