<?php
/**
 * User-related functions and filters.
 *
 * @package    Members
 * @subpackage Includes
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// Filter `user_has_cap` if denied caps should take precedence.
if ( members_explicitly_deny_caps() ) {
	add_filter( 'user_has_cap', 'members_user_has_cap_filter', 10, 4 );
}

/**
 * Filter on `user_has_cap` to explicitly deny caps if there are conflicting caps when a
 * user has multiple roles.  WordPress doesn't consistently handle two or more roles that
 * have the same capability but a conflict between being granted or denied.  Core WP
 * merges the role caps so that the last role the user has will take precedence.  This
 * has the potential for granting permission for things that a user shouldn't have
 * permission to do.
 *
 * @since  1.0.0
 * @access public
 * @param  array  $allcaps
 * @param  array  $caps
 * @param  array  $args
 * @param  object $user
 * @return array
 */
function members_user_has_cap_filter( $allcaps, $caps, $args, $user ) {

	// If the user doesn't have more than one role, bail.
	if ( 1 >= count( (array) $user->roles ) )
		return $allcaps;

	// Get the denied caps.
	$denied_caps = array_keys( $allcaps, false );

	// Loop through the user's roles and find any denied caps.
	foreach ( (array) $user->roles as $role ) {
		$denied_caps = array_merge( $denied_caps, array_keys( get_role( $role )->capabilities, false ) );
	}

	// If there are any denied caps, make sure they take precedence.
	if ( $denied_caps ) {

		foreach ( $denied_caps as $denied_cap ) {
			$allcaps[ $denied_cap ] = false;
		}
	}

	// Return all the user caps.
	return $allcaps;
}
