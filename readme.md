# Members

Members is a plugin that extends your control over your blog.  It's a user, role, and content management plugin that was created to make WordPress a more powerful CMS.

The foundation of the plugin is its extensive role and capability management system.  This is the backbone of all the current features and planned future features.

## Plugin Features:

* Role Manager: Allows you to edit, create, and delete roles as well as capabilities for these roles.
* Content Permissions: Gives you control over which users (by role) have access to post content.
* Shortcodes: Shortcodes to control who has access to content.
* Widgets:  A login form widget and users widget to show in your theme's sidebars.
* Private Site: You can make your site and its feed completely private if you want.

## Professional Support

If you need professional plugin support from me, the plugin author, you can access the support forums at [Theme Hybrid](http://themehybrid.com/support), which is a professional WordPress help/support site where I handle support for all my plugins and themes for a community of 60,000+ users (and growing).

## Copyright and License

This project is licensed under the [GNU GPL](http://www.gnu.org/licenses/old-licenses/gpl-2.0.html), version 2 or later.

2009&thinsp;&ndash;&thinsp;2015 &copy; [Justin Tadlock](http://justintadlock.com).

## Documentation

### The relationship of users, roles, and capabilities

This is the most important thing to understand with this plugin.  It's so important that I took the time out of my day to write a complete tutorial on understanding this:  [Users, roles, and capabilities in WordPress](http://justintadlock.com/archives/2009/08/30/users-roles-and-capabilities-in-wordpress).  If you don't understand this concept, you won't understand what this plugin does.  This is not a concept created by the plugin.  This is how it's done in WordPress.

I highly recommend reading that blog post, but here's the short version:

* **Users** are people that have registered on your site.  I'm sure you already knew that.  In WordPress, users are assigned a specific role.  This role defines what the user can/can't do.
* **Roles** are a way of grouping users.  Each user on your site will have a specific role.  Roles are a set of capabilities.  It is important to note that **roles are not hierarchical**.  For example, "Administrator" is not higher than "Subscriber" in WordPress.  You could literally give the Subscriber role more capabilities than the Administrator role.  It's very important that you grasp this concept.
* **Capabilities** give meaning to roles.  It's a permissions system.  They're a way of saying a role *can* do something or a role *can't* do something (e.g., Role A can `edit_posts`, Role B can't `activate_plugins`, etc.).

### How to use the plugin

This plugin is set up to have a components-based system.  The reason for this is that I don't want to stick everyone with a bunch of features they don't need.  There's no point in using the Role Manger feature if all you need is just a login widget and some shortcodes.  So, it's a *use-only-what-you-want* system.

To activate certain features, look for the *Members* link under your *Settings* menu while in your WordPress admin.  When on the new page, you'll be able to select the features you want to use.

I recommend at least activating Role Manager feature.  It is at the heart of this plugin, and many other features will likely require its use in some form.

### Role management

The Role Manager feature allows you to edit and add new roles as well as add and remove both default capabilities and custom capabilities from roles.  It is an extremely powerful system.

Any changes you make to users and roles using this feature are permanent changes.  What I mean by this is that if you deactivate or uninstall this plugin, the changes won't revert to their previous state.  This plugin merely provides a user interface for you to make changes directly to your WordPress database.  Please use this feature wisely.

#### Editing existing roles

This feature can be both a blessing and a curse, so I'm going to ask that you use it wisely.  Use extreme caution when assigning new capabilities to roles. You wouldn't want to give Average Joe the `edit_plugins` capability, for example.

You can find the settings page for this feature under the *Users* menu.  It will be labeled *Roles*.  When clicking on the menu item, you'll get a list of all the available roles.  From there, you can select a role to edit.

When selecting a role to edit, you will be taken to a new screen that lists all of the available capabilities you can add to a role.  You simply have to tick the checkbox next to the capability you want to add/remove for a particular role and save.

#### Adding new roles

The menu item for adding new roles is located under the *Users* menu and is labeled *Add New Role*.

Adding new roles is pretty straightforward.  You need to input a *Role Name* (only use letters, numbers, and underscores), *Role Label*, and select which capabilities the new role should have.  You can later edit this role.

You can assign new roles to users from the *Users* screen in WordPress.  This is nothing particular to the plugin and is a default part of WordPress.  I believe you need the `edit_users` capability to do this.

### Content permissions feature

The *Content Permissions* feature will be the heart and soul of this plugin in the future.  Right now, it only adds an additional meta box on the post editing screen.

For any public post type (posts, pages, etc.), you'll see a "Content Permissions" meta box on the post editing screen.  This meta box allows you to select which roles can view the content of the post/page.  If no roles are selected, anyone can view the content.  The post author, users that can edit the post, and any users of roles with the `restrict_content` capability can **always** view the post, regardless of their role.

You can add a custom error message for individual posts.  Otherwise, the error message will default to whatever you have set under the *Members* plugin settings.

### Shortcodes

There are several shortcodes that you can use in your post editor or any shortcode-ready area..

#### [access]

The `[access]` shortcode is for hiding content from particular roles and capabilities.  You need to wrap your content when using this shortcode:

	[access role="editor"]Hide this content from everyone but editors.[/access]

**Parameters:**

* `capability`:  A capability that has been assigned to a role.
* `role`: A user role from WordPress or one that you've created.
* `feed`: Set to `true` if you'd like to show the content in feeds.

Note that `capability` and `role` parameters aren't used in conjunction.  The code first checks for the capability (if input) then checks for the role (if input).

To check for multiple capabilities or multiple roles, simply add a comma between each capability/role.  For example, the following code checks for an editor or administrator:

	[access role="administrator,editor"]Hide this content from everyone but administrators and editors.[/access]

#### [is_user_logged_in]

The `[is_user_logged_in]` shortcode should be used to check if a user is currently logged into the site.  If not, the content will be hidden.

	[is_user_logged_in]This content is only shown to logged-in users.[/is_user_logged_in]

This shortcode has no parameters.

#### [feed]

If you have content you only want to show to subscribers of your feed, wrap it in this shortcode:

	[feed]This content will only be shown in feeds.[/feed]

This shortcode has no parameters.

### Widgets

The widgets component provides easy-to-use widgets for your site.  They can be used in any WordPress widget area (provided by your theme).  Currently, there's the *Login Form* and *Users* widgets.

#### Login Form widget

The *Login Form* gives you a login form.  It's a mixture of a text widget and login form.  It can also show your avatar.

It's pretty straightforward, but I'll provide more documentation later.

#### Users widget

The *Users* widget allows you to list users in any widget area.  It's based off the `get_users()` function, so all of the [parameters are the same](http://codex.wordpress.org/Function_Reference/get_users).

### Private site

The Private Site features makes sure that only logged-in users can see anything on your site.  If a user visits your site and is not logged in, they are immediately redirected to your `wp-login.php` (WordPress login) page.

You also have the option of disabling the viewing of feed content and setting an error message for feed items.

### Checking if the current user has a capability/role

In plugins and your theme template files, you might sometimes need to check if the currently logged in user has permission to do something.  We do this by using the WordPress function `current_user_can()`.  The basic format looks like this:

	<?php if ( current_user_can( 'capability_name' ) ) echo 'This user can do something'; ?>

For a more practical situation, let's say you created a new capability called `read_pages`.  Well, you might want to hide the content within your `page.php` template by adding this:

	<?php if ( current_user_can( 'read_pages ' ) ) { ?>
		<?php the_content(); ?>
	<?php } ?>

Only users with a role that has the `read_pages` capability will be able to see the content.

You can check for a specific role by inputting the role name instead of the capability name.  It works the same way.

### Adding new default capabilities

Your plugin/theme can add new capabilities to the *Edit Roles* component if needed.  This will allow users to easily select the additional capabilities for whichever roles they choose.

	add_filter( 'members_get_capabilities', 'my_plugin_new_caps' );
	
	function my_plugin_new_caps( $capabilities ) {
	
		$capabilities[] = 'cap_name_1';
		$capabilities[] = 'cap_name_2';
		$capabilities[] = 'cap_name_3';
	
		return $capabilities;
	}

Note that you need to respect the existing capabilities and return the original array.

### Checking for capabilities

In WordPress, you can use the `current_user_can()` function to check if the current user has a particular capability.  Since you don't know whether a user has this plugin installed, you might want to check first.

The `members_check_for_cap()` function (only use in admin) checks if any role has a particular capability.  This can be useful in setting up something like admin menus.  For example, you can set up a theme settings menu for users that have the `edit_themes` capability.  But, if this plugin is installed and a user has the `edit_my_theme` capability, that'll be used instead.

	if ( function_exists( 'members_check_for_cap' ) && members_check_for_cap( 'some_cap' ) ) {
		/* Do something if any role has the 'some_cap' capability. */
	else {
		/* Do something for people without the plugin. */
	}

### Need the old user levels system?

Some plugins and themes might rely on the old user level system in WordPress.  These were deprecated in WordPress version 2.1 and should not be used at all.  WordPress still has minimal legacy support for these, but I highly suggest contacting your theme/plugin author if user levels are being used.

By default, the levels aren't shown.  They still exist, but are tucked away behind the scenes.  While not recommended, if you need to control who has what level (levels are just capabilities), add this to your plugin or your theme's `functions.php`:

	remove_filter( 'members_get_capabilities', 'members_remove_old_levels' );
