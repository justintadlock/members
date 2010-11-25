<?php
/**
 * Members Components API.  This file contains everything you need to build custom components.
 * Rather than limiting the plugin to self-contained settings, the Components API was created so
 * that it could be extended by other plugins and themes.  Registering a component is as simple as
 * using the registration function and creating a custom callback function for the component.
 *
 * To register a component, use the register_component() function.  When registering a custom 
 * component, your registration function should be hooked to 'members_register_components'.
 *
 * @package Members
 * @subpackage Components
 */

/* Register the default components shippedd with the plugin. */
add_action( 'init', 'members_create_default_components', 0 );

/* Load the callback functions for each of the registered components. Components should be registered on 'init' with a priority less than '10'. */
add_action( 'init', 'members_load_components', 5 );

/**
 * Registers the initial components packaged with the plugin.
 * @uses register_members_component() Registers a component.
 *
 * @since 0.1
 */
function members_create_default_components() {
	register_members_component( array( 'name' => 'role_manager', 'label' => __( 'Role Manager', 'members' ), 'callback' => 'members_component_role_manager', 'requires' => false, 'description' => __('The <em>Role Manager</em> component allows you to manage roles on your site by giving you the ability to create, edit, and delete any role.', 'members' ) ) );
	//register_members_component( array( 'name' => 'edit_roles', 'label' => __('Edit Roles', 'members'), 'callback' => 'members_component_edit_roles', 'requires' => false, 'description' => __('The <em>Edit Roles</em> component allows you to manage all roles on your site. You can change which capabilities individual roles have. Once you\'ve selected this component, you should immediately give at least one role the <code>edit_roles</code> capability. This makes sure only the roles you select can edit roles.', 'members') ) );
	//register_members_component( array( 'name' => 'new_roles', 'label' => __('New Roles', 'members'), 'callback' => 'members_component_new_roles', 'requires' => false, 'description' => __('The <em>New Roles</em> component allows you to create new roles on your site. To use this component, you must have the <code>create_roles</code> capability. This makes sure only the roles you select can create new roles.', 'members') ) );
	register_members_component( array( 'name' => 'content_permissions', 'label' => __('Content Permissions', 'members'), 'callback' => 'members_component_content_permissions', 'requires' => false, 'description' => __('Adds an additional meta box for the post/page editor that allows you to grant permissions for who can read the content based on the the user\'s capabilities or role. Only roles with the <code>restrict_content</code> capability will be able to use this component.', 'members') ) );
	register_members_component( array( 'name' => 'shortcodes', 'label' => __('Shortcodes', 'members'), 'callback' => 'members_component_shortcodes', 'requires' => false, 'description' => __('Provides a set of shortcodes that may be used to restrict or provide access to certain areas of your site from within the post editor (or other areas where shortcodes are allowed).', 'members') ) );
	register_members_component( array( 'name' => 'template_tags', 'label' => __('Template Tags', 'members'), 'callback' => 'members_component_template_tags', 'requires' => false, 'description' => __('Provides additional template tags for use within your WordPress theme for restricting or providing access to certain content.', 'members') ) );
	register_members_component( array( 'hook' => 'widgets_init', 'name' => 'widgets', 'label' => __('Widgets', 'members'), 'callback' => 'members_component_widgets', 'requires' => false, 'description' => __('Creates additional widgets for use in any widget area on your site. The current widgets are Login Form and Users.', 'members') ) );
	register_members_component( array( 'name' => 'private_blog', 'label' => __('Private Blog', 'members'), 'callback' => 'members_component_private_blog', 'requires' => false, 'description' => __('Forces all users to log into the site before viewing it. It will always redirect users to the login page. Note that this component does not block public access to your feeds.', 'members') ) );
	register_members_component( array( 'name' => 'stats', 'label' => __( 'Statistics', 'members' ), 'callback' => 'members_component_stats', 'requires' => false, 'description' => __( 'Adds statistics for user signups based on role.', 'members' ) ) );
	//register_members_component( array( 'name' => 'user_fields', 'label' => __('User Fields', 'members'), 'callback' => 'members_component_user_fields', 'requires' => false, 'description' => __('Provides an interface for building additional user profile fields.  Users will then be able provide this additional information when editing their profile', 'members') ) );

	do_action( 'members_register_components' ); // Available hook to register components.
}

/**
 * Function for registering an additional component for the plugin, which the user
 * may choose to use.  Note that you should add in all the arguments to properly register
 * your component.  The exception is $requires.
 *
 * Your callback function should make use of the is_active_members_component() function
 * as soon as it fires.  This allows you to check if the user has activated the component before
 * loading all of your code.  Please use this system as it keeps the plugin as light as possible.
 *
 * @since 0.1
 * @global $members object The global members object.
 *
 * @param $args array Arguments for registering a custom component.
 * @param $args[$name] string Name of the component (only letters, numbers, and spaces).
 * @param $args[$label] string Display name of the component.
 * @param $args[$description] string Description of the component.
 * @param $args[$callback] string Function that will be called when the components system is loaded.
 * @param $args[$requires] array|bool Names of other components that are required to run this component.
 */
function register_members_component( $args = array() ) {
	global $members;

	$name = $args['name'];

	$members->registered_components[$name] = (object) $args;
}

/**
 * Loops through the registered components and load the necessary callback function.
 * If a $component->hook is added and a $component->callback function, the plugin
 * will add the function to the action hook automatically.  Else if the $component->callback 
 * function exists but not $component->hook, the function will simply be called.
 *
 * @since 0.2
 * @global $members object The global members object.
 */
function members_load_components() {
	global $members;

	/* Check if there are any registered components. If not, return false. */
	if ( !is_array( $members->registered_components ) )
		return false;

	/* Loop through each of the registered components and execute the desired action. */
	foreach ( $members->registered_components as $component ) {

		/* If a callback function has been input, continue the process. */
		if ( $component->callback && function_exists( $component->callback ) ) {

			/* If a hook has been input, add the component callback function to the action hook. */
			if ( isset($component->hook) )
				add_action( $component->hook, $component->callback );

			/* Call the function directly if there is no hook. */
			else
				call_user_func( $component->callback, $component );
		}
	}
}

/**
 * Function for getting a specific component object from the list of registered
 * plugin components.
 *
 * @since 0.1
 * @global $members object The global members object.
 * @param $component string Required. The name of the component.
 * @return $members->registered_components[$component] object
 */
function get_members_component( $component ) {
	global $members;
	return $members->registered_components[$component_name];
}

/**
 * Checks if a component has been activated by the user. If it has, return true. If not,
 * return false.
 *
 * @since 0.1
 * @global $members object The global members object.
 * @param $component string Name of the component to check for.
 * @return true|false bool
 */
function is_active_members_component( $component = '' ) {
	global $members;
	
	return (bool) isset( $members->active_components[$component] );
}

/* Default Components */

/**
 * Mange roles component.  This allows you to create, edit, and delete roles
 * and each role's capabilities.
 *
 * @since 0.2
 */
function members_component_role_manager() {
	if ( is_admin() && is_active_members_component( 'role_manager' ) )
		require_once( MEMBERS_COMPONENTS . '/role-manager/default.php' );
}

/**
 * Manage roles component.  This allows you to manage each role's set
 * of capabilities.
 *
 * @since 0.1
 * @deprecated 0.2
 */
function members_component_edit_roles() {
	if ( is_admin() && is_active_members_component( 'edit_roles' ) )
		require_once( MEMBERS_COMPONENTS . '/edit-roles/default.php' );
}

/**
 * New roles component. This allows you to create new roles.
 *
 * @since 0.1
 * @deprecated 0.2
 */
function members_component_new_roles() {
	if ( is_admin() && is_active_members_component( 'new_roles' ) )
		require_once( MEMBERS_COMPONENTS . '/new-roles/default.php' );
}

/**
 * Loads the content permissions component.
 *
 * @since 0.1
 */
function members_component_content_permissions() {
	if ( is_active_members_component( 'content_permissions' ) ) {
		require_once( MEMBERS_COMPONENTS . '/content-permissions/content-permissions.php' );
		require_once( MEMBERS_COMPONENTS . '/content-permissions/meta-box.php' );
	}
}

/**
 * Loads the shortcodes component.
 *
 * @since 0.1
 */
function members_component_shortcodes() {
	if ( !is_admin() && is_active_members_component( 'shortcodes' ) )
		require_once( MEMBERS_COMPONENTS . '/shortcodes/shortcodes.php' );
}

/**
 * Loads the template tags component.
 *
 * @since 0.1
 */
function members_component_template_tags() {
	if ( is_active_members_component( 'template_tags' ) )
		require_once( MEMBERS_COMPONENTS . '/template-tags/template-tags.php' );
}

/**
 * Loads the widgets component.
 *
 * @since 0.1
 */
function members_component_widgets() {
	if ( is_active_members_component( 'widgets' ) ) {
	
		/* Load each of the widget files. */
		require_once( MEMBERS_COMPONENTS . '/widgets/login.php' );
		require_once( MEMBERS_COMPONENTS . '/widgets/users.php' );
	
		/* Register each widget. */
		register_widget( 'Members_Widget_Login' );
		register_widget( 'Members_Widget_Users' );
	}
}

/**
 * Loads the private blog component.
 *
 * @since 0.1
 */
function members_component_private_blog() {
	if ( is_active_members_component( 'private_blog' ) )
		require_once( MEMBERS_COMPONENTS . '/private-blog/default.php' );
}

/**
 * Loads the stats component.
 *
 * @since 0.2
 */
function members_component_stats() {
	if ( is_admin() && is_active_members_component( 'stats' ) )
		require_once( MEMBERS_COMPONENTS . '/statistics/default.php' );
}

/**
 * Loads the user fields component.
 *
 * @internal Do not use this function.  The component isn't ready.
 * @todo Pretty much everything.
 *
 * @since 0.1
 */
function members_component_user_fields() {
	if ( is_admin() && is_active_members_component( 'user_fields' ) )
		require_once( MEMBERS_COMPONENTS . '/user-fields/default.php' );
}

?>