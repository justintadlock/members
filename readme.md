# Members

Members is a plugin that extends your control over your blog.  It's a user, role, and capability management plugin that was created to make WordPress a more powerful CMS.

It puts you in control over permissions on your site by providing a user interface (UI) for WordPress' powerful role and cap system, which is traditionally only available to developers who know how to code this by hand.

## Plugin Features

* **Role Manager:** Allows you to edit, create, and delete roles as well as capabilities for these roles.
* **Multiple User Roles:** Give one, two, or even more roles to any user.
* **Explicitly Deny Capabilities:** Deny specific caps to specific user roles.
* **Clone Roles:** Build a new role by cloning an existing role.
* **Content Permissions:** Gives you control over which users (by role) have access to post content.
* **Shortcodes:** Shortcodes to control who has access to content.
* **Widgets:**  A login form widget and users widget to show in your theme's sidebars.
* **Private Site:** You can make your site and its feed completely private if you want.

## Professional Support

If you need professional plugin support from me, the plugin author, you can access the support forums at [Theme Hybrid](https://themehybrid.com/board/topics), which is a professional WordPress help/support site where I handle support for all my plugins and themes for a community of 75,000+ users (and growing).

## Copyright and License

This project is licensed under the [GNU GPL](http://www.gnu.org/licenses/old-licenses/gpl-2.0.html), version 2 or later.

2009&thinsp;&ndash;&thinsp;2018 &copy; [Justin Tadlock](http://justintadlock.com).

## Documentation

### The relationship of users, roles, and capabilities

This is the most important thing to understand with this plugin.  It's so important that I took the time out of my day to write a complete tutorial on understanding this:  [Users, roles, and capabilities in WordPress](http://justintadlock.com/archives/2009/08/30/users-roles-and-capabilities-in-wordpress).  If you don't understand this concept, you won't understand what this plugin does.  This is not a concept created by the plugin.  This is how it's done in WordPress.

I highly recommend reading that blog post, but here's the short version:

* **Users** are people that have registered on your site.  I'm sure you already knew that.  In WordPress, users are assigned a specific role.  This role defines what the user can/can't do.
* **Roles** are a way of grouping users.  Each user on your site will have a specific role.  Roles are a set of capabilities.  It is important to note that **roles are not hierarchical**.  For example, "Administrator" is not higher than "Subscriber" in WordPress.  You could literally give the Subscriber role more capabilities than the Administrator role.  It's very important that you grasp this concept.
* **Capabilities** give meaning to roles.  It's a permissions system.  They're a way of saying a role *can* do something or a role *can't* do something (e.g., Role A can `edit_posts`, Role B can't `activate_plugins`, etc.).

### How to use the plugin

This plugin is set up to have a components-based system.  The reason for this is that I don't want to stick everyone with a bunch of features they don't need.  There's no point in using the Role Manger feature if all you need is just a login widget and some shortcodes.  So, it's a *use-only-what-you-want* system.

To activate certain features, look for the "Members" link under your "Settings" menu while in your WordPress admin.  When on the new page, you'll be able to select the features you want to use.

I recommend at least activating Role Manager feature.  It is at the heart of this plugin, and many other features will likely require its use in some form.

### Role management

The Role Manager feature allows you to edit and add new roles as well as add and remove both default capabilities and custom capabilities from roles.  It is an extremely powerful system.

Any changes you make to users and roles using this feature are permanent changes.  What I mean by this is that if you deactivate or uninstall this plugin, the changes won't revert to their previous state.  This plugin merely provides a user interface for you to make changes directly to your WordPress database.  Please use this feature wisely.

#### Editing/Adding Roles

This feature can be both a blessing and a curse, so I'm going to ask that you use it wisely.  Use extreme caution when assigning new capabilities to roles. You wouldn't want to grant Average Joe the `edit_plugins` capability, for example.

You can find the settings page for this feature under the "Users" menu.  It will be labeled "Roles".  When clicking on the menu item, you'll be take to a screen similar to the edit post/page screen, only it'll be for editing a role.

In the "Edit Capabilities" box on that screen, you simply have to tick the checkbox next to the capability you want to grant or deny.

#### Grant, deny, or neither?

Every capability can have one of three "states" for a role.  The role can be *granted*, *denied*, or simply not have a capability.

* **Granting** a capability to a role means that users of that role will have permission to perform the given capability.
* **Denying** a capability means that the role's users are explicitly denied permission.
* A role that is neither granted nor denied a capability simply doesn't have that capability.

**Note #1:** If you were using a pre-1.0.0 version of Members, the concept of denied capabilities was not built in.  In those versions, you could only grant or remove a capability.

**Note #2:** When assigning multiple roles to a single user that have a conflicting capability (e.g., granted `publish_posts` and denied `published_posts` cap), it's best to enable the denied capabilities override via the Members Settings screen.  This will consistently make sure that denied capabilities always overrule granted capabilities.  With this setting disabled, WordPress will decide based on the *last* role given to the user, which can mean for extremely inconsistent behavior depending on the roles a user has.

### Multiple user roles

You can assign a user more than one role by going to that edit user screen in the admin and locating the "Roles" section.  There will be a checkbox for every role.

You can also multiple roles to a user from the add new user screen.

On the "Users" screen in the admin, you can bulk add or remove single roles from multiple users.

### Content permissions feature

The Content Permissions feature adds an additional meta box on the post editing screen.

For any public post type (posts, pages, etc.), you'll see a "Content Permissions" meta box on the post editing screen.  This meta box allows you to select which roles can view the content of the post/page.  If no roles are selected, anyone can view the content.  The post author, users that can edit the post, and any users of roles with the `restrict_content` capability can **always** view the post, regardless of their role.

You can add a custom error message for individual posts.  Otherwise, the error message will default to whatever you have set under the plugin settings.

**Big important note:** This feature only blocks the post content (that's what you write in the post editor), post excerpt, and post comments.  It does not block anything else.

### Shortcodes

There are several shortcodes that you can use in your post editor or any shortcode-ready area..

#### [members_access]

The `[members_access]` shortcode is for hiding content from particular roles and capabilities.  You need to wrap your content when using this shortcode:

	[members_access role="editor"]Hide this content from everyone but editors.[/members_access]

The plugin accepts the following parameters (mixing and matching won't work):

* `role` - A single or comma-separated list of roles.
* `capability` - A single or comma-separated list of capabilities.
* `user_name` - A single or comma-separated list of usernames.
* `user_id` - A single or comma-separated list of user IDs.
* `user_email` - A single or comma-separated list of user email addresses.

**Parameters:**

* `capability`:  A capability that has been assigned to a role.
* `role`: A user role from WordPress or one that you've created.
* `operator`: Accepts `!` to negate the role or capability.

Note that `capability` and `role` parameters aren't used in conjunction.  The code first checks for the capability (if input) then checks for the role (if input).

To check for multiple capabilities or multiple roles, simply add a comma between each capability/role.  For example, the following code checks for an editor or administrator:

	[members_access role="administrator,editor"]Show this content to administrators or editors only.[/members_access]

To check that the user does not have a role:

	[members_access role="administrator" operator="!"]Show this content to anyone who is not an administrator.[/members_access]

#### [members_logged_in]

The `[members_logged_in]` shortcode should be used to check if a user is currently logged into the site.  If not, the content will be hidden.

	[members_logged_in]This content is only shown to logged-in users.[/members_logged_in]

This shortcode has no parameters.

##### [members_not_logged_in]

The `[members_not_logged_in]` shortcode should be used to show content to users who are not logged into the site.  If the user is logged in, the content will be hidden.

	[members_not_logged_in]This content is only shown to logged-out visitors.[/members_not_logged_in]

#### [members_login_form]

The `[members_login_form]` shortcode is used to show a login form on the page.

	[members_login_form /]

This shortcode has no parameters.

### Widgets

The widgets component provides easy-to-use widgets for your site.  They can be used in any WordPress widget area (provided by your theme).  Currently, there's the Login Form and Users widgets.

#### Login Form widget

The Login Form gives you a login form.  It's a mixture of a text widget and login form.  It can also show your avatar.

#### Users widget

The Users widget allows you to list users in any widget area.  It's based off the `get_users()` function, so all of the [parameters are the same](http://codex.wordpress.org/Function_Reference/get_users).

### Private site

The Private Site features makes sure that only logged-in users can see anything on your site.  If a user visits your site and is not logged in, they are immediately redirected to your `wp-login.php` (WordPress login) page.

You also have the option of disabling the viewing of feed content and setting an error message for feed items.

### Checking if the current user has a capability

In plugins and your theme template files, you might sometimes need to check if the currently logged in user has permission to do something.  We do this by using the WordPress function `current_user_can()`.  The basic format looks like this:

	<?php if ( current_user_can( 'capability_name' ) ) echo 'This user can do something'; ?>

For a more practical situation, let's say you created a new capability called `read_pages`.  Well, you might want to hide the content within your `page.php` template by adding this:

	<?php if ( current_user_can( 'read_pages ' ) ) : ?>
		<?php the_content(); ?>
	<?php endif; ?>

Only users with a role that has the `read_pages` capability will be able to see the content.

### Checking if a user has a role

Before beginning, I want to note that you really shouldn't do this.  It's better to check against capabilities.  However, for those times when you need to break the rules, you can do so like:

	if ( members_user_has_role( $user_id, $role ) )

Or, you can check against the current user:

	if ( members_current_user_has_role( $role ) )

### Need the old user levels system?

Some plugins and themes might rely on the old user level system in WordPress.  These were deprecated in WordPress version 2.1 and should not be used at all.  WordPress still has minimal legacy support for these, but I highly suggest contacting your theme/plugin author if user levels are being used.

By default, the levels aren't shown.  They still exist, but are tucked away behind the scenes.  While not recommended, if you need to control who has what level (levels are just capabilities), add this to your plugin or your theme's `functions.php`:

	add_filter( 'members_remove_old_levels', '__return_false' );

### Registering capabilities

If you're a plugin developer with custom capabilities, beginning with version 2.0.0 of Members, you can register your capabilities with Members.  Essentially, this allows users to see your capabilities in a nicely-formatted, human-readable form (e.g., `Publish Posts` instead of `publish_posts`).  This also means that it can be translated so that it's easier to understand for users who do not read English.

	add_action( 'members_register_caps', 'th_register_caps' );

	function th_register_caps() {

		members_register_cap(
			'your_cap_name',
			array(
				'label' => __( 'Your Capability Label', 'example-textdomain' ),
				'group' => 'example'
			)
		);
	}

The `group` argument is not required, but will allow you to assign the capability to a cap group.

### Registering cap groups

Members groups capabilities so that users can more easily find them when editing roles.  If your plugin has multiple capabilities, you should consider creating a custom cap group.

	add_action( 'members_register_cap_groups', 'th_register_cap_groups' );

	function th_register_cap_groups() {

		members_register_cap_group(
			'your_group_name',
			array(
				'label'    => __( 'Your Group Label', 'example-textdomain' ),
				'caps'     => array(),
				'icon'     => 'dashicons-admin-generic',
				'priority' => 10
			)
		);
	}

The arguments for the array are:

* `label` - An internationalized text label for your group.
* `caps` - An array of initial capabilities to add to your group.
* `icon` - The name of one of core WP's [dashicons](https://developer.wordpress.org/resource/dashicons/) or a custom class (would need to be styled by your plugin in this case).
* `priority` - The priority of your group compared to other groups.  `10` is the default.

_Note that custom post types are automatically registered as groups with Members.  So, if you want to do something custom with that, you simply need to unregister the group before registering your own._

	members_unregister_cap_group( "type-{$post_type}" );
