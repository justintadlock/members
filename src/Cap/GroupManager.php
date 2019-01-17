<?php

namespace Members\Cap;

use Members\Contracts\Bootable;

class GroupManager implements Bootable {

	protected $groups;

	public function __construct( Groups $groups ) {

		$this->groups = $groups;
	}

	public function boot() {

		// Set up the registration hook.
		add_action( 'init', [ $this, 'register' ], 95 );

		// Register default caps.
		add_action( 'members/cap/register/groups', [ $this, 'registerDefaultGroups' ], 5 );
	}

	public function register() {

		do_action( 'members/cap/register/groups', $this->groups );

		// back-compat
		do_action( 'members_register_cap_groups' );
	}

	public function registerDefaultGroups( $groups ) {

		// Internal core WP post types that we don't want to create a
		// group for b/c they have no specific caps.
		$disallowed_types = [
			'revision',
			'nav_menu_item',
			'custom_css',
			'customize_changeset'
		];

		// Registers the general group.
		$groups->add( 'general', [
			'label'    => __( 'General', 'members' ),
			'icon'     => 'dashicons-wordpress',
			'priority' => 5
		] );

		// Loop through every custom post type.
		foreach ( get_post_types( [], 'objects' ) as $type ) {

			// Skip disallowed post types.
			if ( in_array( $type->name, $disallowed_types ) ) {
				continue;
			}

			// Get the caps for the post type.
		//	$has_caps = members_get_post_type_group_caps( $type->name );
			$has_caps = false;

			// Skip if the post type doesn't have caps.
			if ( ! $has_caps ) {
				continue;
			}

			// Set the default post type icon.
			$icon = $type->hierarchical ? 'dashicons-admin-page' : 'dashicons-admin-post';

			// Get the post type icon.
			if ( is_string( $type->menu_icon ) && preg_match( '/dashicons-/i', $type->menu_icon ) )
				$icon = $type->menu_icon;

			elseif ( 'attachment' === $type->name )
				$icon = 'dashicons-admin-media';

			elseif ( 'download' === $type->name )
				$icon = 'dashicons-download'; // EDD

			elseif ( 'product' === $type->name )
				$icon = 'dashicons-cart';

			// Register the post type cap group.
			$groups->add( "type-{$type->name}",
				array(
					'label'    => $type->labels->name,
					'caps'     => $has_caps,
					'icon'     => $icon,
					'priority' => 10
				)
			);
		}

		// Register the taxonomy group.
		$groups->add( 'taxonomy',
			array(
				'label'      => esc_html__( 'Taxonomies', 'members' ),
				'caps'       => [], //members_get_taxonomy_group_caps(),
				'icon'       => 'dashicons-tag',
				'diff_added' => true,
				'priority'   => 15
			)
		);

		// Register the theme group.
		$groups->add( 'theme',
			array(
				'label'    => esc_html__( 'Appearance', 'members' ),
				'icon'     => 'dashicons-admin-appearance',
				'priority' => 20
			)
		);

		// Register the plugin group.
		$groups->add( 'plugin',
			array(
				'label'    => esc_html__( 'Plugins', 'members' ),
				'icon'     => 'dashicons-admin-plugins',
				'priority' => 25
			)
		);

		// Register the user group.
		$groups->add( 'user',
			array(
				'label'    => esc_html__( 'Users', 'members' ),
				'icon'     => 'dashicons-admin-users',
				'priority' => 30
			)
		);

		// Register the custom group.
		$groups->add( 'custom',
			array(
				'label'      => esc_html__( 'Custom', 'members' ),
				'caps'       => [], // members_get_capabilities(),
				'icon'       => 'dashicons-admin-generic',
				'diff_added' => true,
				'priority'   => 995
			)
		);
	}
}
