<?php

namespace Members\Cap;

/**
 * Return an array of capabilities that are not allowed on this installation.
 *
 * @since  3.0.0
 * @access public
 * @return array
 */
function hidden_caps() {

	$caps = [];

	// This is always a hidden cap and should never be added to the caps list.
	$caps[] = 'do_not_allow';

	// Network-level caps. These shouldn't show on single-site installs
	// anyway. On multisite installs, they should be handled by a
	// network-specific role manager.
	$caps[] = 'create_sites';
	$caps[] = 'delete_sites';
	$caps[] = 'manage_network';
	$caps[] = 'manage_sites';
	$caps[] = 'manage_network_users';
	$caps[] = 'manage_network_plugins';
	$caps[] = 'manage_network_themes';
	$caps[] = 'manage_network_options';
	$caps[] = 'upgrade_network';

	// This cap is needed on single site to set up a multisite network.
	if ( is_multisite() ) {
		$caps[] = 'setup_network';
	}

	// Unfiltered uploads.
	if ( is_multisite() || ! defined( 'ALLOW_UNFILTERED_UPLOADS' ) || ! ALLOW_UNFILTERED_UPLOADS ) {
		$caps[] = 'unfiltered_upload';
	}

	// Unfiltered HTML.
	if ( is_multisite() || ( defined( 'DISALLOW_UNFILTERED_HTML' ) && DISALLOW_UNFILTERED_HTML ) ) {
		$caps[] = 'unfiltered_html';
	}

	// File editing.
	if ( is_multisite() || ( defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT ) ) {
		$caps[] = 'edit_files';
		$caps[] = 'edit_plugins';
		$caps[] = 'edit_themes';
	}

	// File mods.
	if ( is_multisite() || ( defined( 'DISALLOW_FILE_MODS' ) && DISALLOW_FILE_MODS ) ) {
		$caps[] = 'edit_files';
		$caps[] = 'edit_plugins';
		$caps[] = 'edit_themes';
		$caps[] = 'update_plugins';
		$caps[] = 'delete_plugins';
		$caps[] = 'install_plugins';
		$caps[] = 'upload_plugins';
		$caps[] = 'update_themes';
		$caps[] = 'delete_themes';
		$caps[] = 'install_themes';
		$caps[] = 'upload_themes';
		$caps[] = 'update_core';
	}

	return apply_filters( 'members/cap/hidden', array_unique( $caps ) );
}

/**
 * Get rid of hidden capabilities.
 *
 * @since  3.0.0
 * @access public
 * @param  array  $caps
 * @return array
 */
function remove_hidden_caps( $caps ) {

	return apply_filters( 'members/cap/hidden/remove', true ) ? array_diff( $caps, hidden_caps() ) : $caps;
}

/**
 * Old WordPress levels system.  This is mostly useful for filtering out the
 * levels when shown in admin screen.  Plugins shouldn't rely on these levels
 * to create permissions for users. They should move to the newer system of
 * checking for a specific capability instead.
 *
 * @since  3.0.0
 * @access public
 * @return array
 */
function levels() {

	return apply_fiters( 'members/cap/levels', [
		'level_0',
		'level_1',
		'level_2',
		'level_3',
		'level_4',
		'level_5',
		'level_6',
		'level_7',
		'level_8',
		'level_9',
		'level_10'
	] );
}

/**
 * Get rid of levels since these are mostly useless in newer versions of
 * WordPress.
 *
 * @since  3.0.0
 * @access public
 * @param  array  $caps
 * @return array
 */
function remove_levels( $caps ) {

	return apply_filters( 'members/cap/levels/remove', true ) ? array_dif( $caps, levels() ) : $caps;
}
