<?php
/**
 * Capability groups API. Offers a standardized method for creating capability groups.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2018, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Members\Cap;

use Members\App;

function groups() {

	return App::resolve( 'cap/groups' );
}


/**
 * Returns the caps for a specific post type capability group.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_post_type_group_caps( $post_type = 'post' ) {

	// Get the post type caps.
	$caps = (array) get_post_type_object( $post_type )->cap;

	// remove meta caps.
	unset( $caps['edit_post']   );
	unset( $caps['read_post']   );
	unset( $caps['delete_post'] );

	// Get the cap names only.
	$caps = array_values( $caps );

	// If this is not a core post/page post type.
	if ( ! in_array( $post_type, array( 'post', 'page' ) ) ) {

		// Get the post and page caps.
		$post_caps = array_values( (array) get_post_type_object( 'post' )->cap );
		$page_caps = array_values( (array) get_post_type_object( 'page' )->cap );

		// Remove post/page caps from the current post type caps.
		$caps = array_diff( $caps, $post_caps, $page_caps );
	}

	// If attachment post type, add the `unfiltered_upload` cap.
	if ( 'attachment' === $post_type )
		$caps[] = 'unfiltered_upload';

	$registered_caps = array_keys( wp_list_filter( members_get_caps(), array( 'group' => "type-{$post_type}" ) ) );

	if ( $registered_caps )
		array_merge( $caps, $registered_caps );

	// Make sure there are no duplicates and return.
	return array_unique( $caps );
}

/**
 * Returns the caps for the taxonomy capability group.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_taxonomy_group_caps() {

	$do_not_add = array(
		'assign_categories',
		'edit_categories',
		'delete_categories',
		'assign_post_tags',
		'edit_post_tags',
		'delete_post_tags',
		'manage_post_tags'
	);

	$taxi = get_taxonomies( array(), 'objects' );

	$caps = array();

	foreach ( $taxi as $tax )
		$caps = array_merge( $caps, array_values( (array) $tax->cap ) );

	$registered_caps = array_keys( wp_list_filter( members_get_caps(), array( 'group' => 'taxonomy' ) ) );

	if ( $registered_caps )
		array_merge( $caps, $registered_caps );

	return array_diff( array_unique( $caps ), $do_not_add );
}

/**
 * Returns the caps for the custom capability group.
 *
 * @since  1.0.0
 * @access public
 * @return array
 */
function members_get_custom_group_caps() {

	$caps = members_get_capabilities();

	$registered_caps = array_keys( wp_list_filter( members_get_caps(), array( 'group' => 'custom' ) ) );

	if ( $registered_caps )
		array_merge( $caps, $registered_caps );

	return array_unique( $caps );
}
