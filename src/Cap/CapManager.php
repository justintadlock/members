<?php

namespace Members\Cap;

use Members\Contracts\Bootable;

class CapManager implements Bootable {

	protected $caps;

	public function __construct( Caps $caps ) {

		$this->caps = $caps;
	}

	public function boot() {

		// Set up the registration hook.
		add_action( 'init', [ $this, 'register' ], 95 );

		// Register default caps.
		add_action( 'members/cap/register/caps', [ $this, 'registerDefaultCaps' ], 5           );
		add_action( 'members/cap/register/caps', [ $this, 'registerExtraCaps'   ], PHP_INT_MAX );
	}

	public function register() {

		do_action( 'members/cap/register/caps', $this->caps );

		// back-compat
		do_action( 'members_register_caps' );
	}

	public function registerDefaultCaps( $caps ) {

		$groups = [];

		// General caps.
		$groups['general'] = [
			'edit_dashboard'    => __( 'Edit Dashboard',    'members' ),
			'edit_files'        => __( 'Edit Files',        'members' ),
			'export'            => __( 'Export',            'members' ),
			'import'            => __( 'Import',            'members' ),
			'manage_links'      => __( 'Manage Links',      'members' ),
			'manage_options'    => __( 'Manage Options',    'members' ),
			'moderate_comments' => __( 'Moderate Comments', 'members' ),
			'read'              => __( 'Read',              'members' ),
			'unfiltered_html'   => __( 'Unfiltered HTML',   'members' ),
			'update_core'       => __( 'Update Core',       'members' )
		];

		// Post caps.
		$groups['type-post'] = [
			'delete_others_posts'    => __( "Delete Others' Posts",   'members' ),
			'delete_posts'           => __( 'Delete Posts',           'members' ),
			'delete_private_posts'   => __( 'Delete Private Posts',   'members' ),
			'delete_published_posts' => __( 'Delete Published Posts', 'members' ),
			'edit_others_posts'      => __( "Edit Others' Posts",     'members' ),
			'edit_posts'             => __( 'Edit Posts',             'members' ),
			'edit_private_posts'     => __( 'Edit Private Posts',     'members' ),
			'edit_published_posts'   => __( 'Edit Published Posts',   'members' ),
			'publish_posts'          => __( 'Publish Posts',          'members' ),
			'read_private_posts'     => __( 'Read Private Posts',     'members' )
		];

		// Page caps.
		$groups['type-page'] = [
			'delete_others_pages'     => __( "Delete Others' Pages",   'members' ),
			'delete_pages'            => __( 'Delete Pages',           'members' ),
			'delete_private_pages'    => __( 'Delete Private Pages',   'members' ),
			'delete_published_pages'  => __( 'Delete Published Pages', 'members' ),
			'edit_others_pages'       => __( "Edit Others' Pages",     'members' ),
			'edit_pages'              => __( 'Edit Pages',             'members' ),
			'edit_private_pages'      => __( 'Edit Private Pages',     'members' ),
			'edit_published_pages'    => __( 'Edit Published Pages',   'members' ),
			'publish_pages'           => __( 'Publish Pages',          'members' ),
			'read_private_pages'      => __( 'Read Private Pages',     'members' )
		];

		// Attachment caps.
		$groups['type-attachment'] = [
			'upload_files'  => __( 'Upload Files', 'members' )
		];

		// Taxonomy caps.
		$groups['taxonomy'] = [
			'manage_categories'  => __( 'Manage Categories', 'members' )
		];

		// Theme caps.
		$groups['theme'] = [
			'delete_themes'       => __( 'Delete Themes',      'members' ),
			'edit_theme_options'  => __( 'Edit Theme Options', 'members' ),
			'edit_themes'         => __( 'Edit Themes',        'members' ),
			'install_themes'      => __( 'Install Themes',     'members' ),
			'switch_themes'       => __( 'Switch Themes',      'members' ),
			'update_themes'       => __( 'Update Themes',      'members' )
		];

		// Plugin caps.
		$groups['plugin'] = [
			'activate_plugins'  => __( 'Activate Plugins', 'members' ),
			'delete_plugins'    => __( 'Delete Plugins',   'members' ),
			'edit_plugins'      => __( 'Edit Plugins',     'members' ),
			'install_plugins'   => __( 'Install Plugins',  'members' ),
			'update_plugins'    => __( 'Update Plugins',   'members' )
		];

		// User caps.
		$groups['user'] = [
			'create_roles'   => __( 'Create Roles',  'members' ),
			'create_users'   => __( 'Create Users',  'members' ),
			'delete_roles'   => __( 'Delete Roles',  'members' ),
			'delete_users'   => __( 'Delete Users',  'members' ),
			'edit_roles'     => __( 'Edit Roles',    'members' ),
			'edit_users'     => __( 'Edit Users',    'members' ),
			'list_roles'     => __( 'List Roles',    'members' ),
			'list_users'     => __( 'List Users',    'members' ),
			'promote_users'  => __( 'Promote Users', 'members' ),
			'remove_users'   => __( 'Remove Users',  'members' )
		];

		// Custom caps.
		$groups['custom'] = [
			'restrict_content'  => __( 'Restrict Content', 'members' )
		];

		// === Category and Tag caps. ===

		// These are mapped to `manage_categories` in a default WP
		// install. However, it's possible for another plugin to map
		// these differently and handle them correctly. So, we're only
		// going to register the caps if they've been assigned to a role.
		// There's no other way to reliably detect if they've been mapped.

	//	$role_caps = array_values( members_get_role_capabilities() );
		$role_caps = [];

		$tax_caps  = [
			'assign_categories' => __( 'Assign Categories', 'members' ),
			'edit_categories'   => __( 'Edit Categories',   'members' ),
			'delete_categories' => __( 'Delete Categories', 'members' ),
			'assign_post_tags'  => __( 'Assign Post Tags',  'members' ),
			'edit_post_tags'    => __( 'Edit Post Tags',    'members' ),
			'delete_post_tags'  => __( 'Delete Post Tags',  'members' ),
			'manage_post_tags'  => __( 'Manage Post Tags',  'members' )
		];

		foreach ( $tax_caps as $tax_cap => $label ) {

			if ( in_array( $tax_cap, $role_caps ) ) {

				$groups['taxonomy'][ $tax_cap ] = $label;
			}
		}

		// Loop through all of the groups and add each of their caps.
		foreach ( $groups as $group => $group_caps ) {

			foreach ( $group_caps as $cap => $label ) {

				$caps->add( $cap, [ 'label' => $label, 'group' => $group ] );
			}
		}
	}

	public function registerExtraCaps( $caps ) {

		// The following is a quick way to register capabilities that technically
		// exist (i.e., caps that have been added to a role).  These are caps that
		// we don't know about because they haven't been registered.

	//	$role_caps    = array_values( members_get_role_capabilities() );
		$role_caps    = [];
		$unregistered = array_diff( $role_caps, array_keys( $caps->all() ) );

		foreach ( $unregistered as $cap ) {

			$caps->add( $cap, [ 'label' => $cap ] );
		}
	}
}
