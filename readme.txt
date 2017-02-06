=== Members ===

Contributors: greenshady
Donate link: http://themehybrid.com/donate
Tags: admin, role, roles, member, members, profile, shortcode, user, users, widget, widgets
Requires at least: 4.3
Tested up to: 4.7.2
Stable tag: 1.1.3

The most powerful user, role, and capability management plugin for WordPress.

== Description ==

Members is a plugin that extends your control over your blog.  It's a user, role, and capability management plugin that was created to make WordPress a more powerful CMS.

It puts you in control over permissions on your site by providing a user interface (UI) for WordPress' powerful role and cap system, which is traditionally only available to developers who know how to code this by hand.

### Plugin Features

* **Role Manager:** Allows you to edit, create, and delete roles as well as capabilities for these roles.
* **Multiple User Roles:** Give one, two, or even more roles to any user.
* **Explicitly Deny Capabilities:** Deny specific caps to specific user roles.
* **Clone Roles:** Build a new role by cloning an existing role.
* **Content Permissions:** Gives you control over which users (by role) have access to post content.
* **Shortcodes:** Shortcodes to control who has access to content.
* **Widgets:**  A login form widget and users widget to show in your theme's sidebars.
* **Private Site:** You can make your site and its feed completely private if you want.
* **Plugin Integration:** Members is highly recommended by  other WordPress developers. Many existing plugins integrate their custom roles and caps directly into it.

For more info, vist the [Members plugin home page](http://themehybrid.com/plugins/members).

### Like this plugin?

The Members plugin is a massive project with 1,000s of lines of code to maintain.  A major update can take weeks or months of work.  I don't make any money directly from this plugin while other, similar plugins charge substantial fees to even download them or get updates.  Please consider helping the cause by:

* [Making a donation](http://themehybrid.com/donate).
* [Signing up at my site](http://themehybrid.com/club).
* [Rating the plugin](https://wordpress.org/support/view/plugin-reviews/members?rate=5#postform).

### Professional Support

If you need professional plugin support from me, the plugin author, you can access the support forums at [Theme Hybrid](http://themehybrid.com/board/topics), which is a professional WordPress help/support site where I handle support for all my plugins and themes for a community of 60,000+ users (and growing).

### Plugin Development

If you're a theme author, plugin author, or just a code hobbyist, you can follow the development of this plugin on it's [GitHub repository](https://github.com/justintadlock/members).

== Installation ==

1. Upload `members` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to "Settings > Members" to select which settings you'd like to use.

More detailed instructions are included in the plugin's `readme.html` file.

== Frequently Asked Questions ==

### Why was this plugin created?

I wasn't satisfied with the current user, role, and permissions plugins available.  Yes, some of them are good, but nothing fit what I had in mind perfectly.  Some offered few features.  Some worked completely outside of the WordPress APIs.  Others lacked the GPL license.

So, I just built something I actually enjoyed using.

### How do I use it?

Most things should be fairly straightforward, but I've included an in-depth guide in the plugin download.  It's a file called `readme.md` in the plugin folder.

You can also [view the readme](https://github.com/justintadlock/members/blob/master/readme.md) online.

### I can't access the "Role Manager" features.

When the plugin is first activated, it runs a script that sets specific capabilities to the "Administrator" role on your site that grants you access to this feature.  So, you must be logged in with the administrator account to access the role manager.

If, for some reason, you do have the administrator role and the role manager is still inaccessible to you, deactivate the plugin.  Then, reactivate it.

### Help! I've locked myself out of my site!

Please read the documentation for the plugin before actually using it, especially a plugin that controls permissions for your site.  I cannot stress this enough.  This is a powerful plugin that allows you to make direct changes to roles and capabilities in the database.

You'll need to stop by my [support forums](http://themehybrid.com/board/topics) to see if we can get your site fixed if you managed to lock yourself out.  I know that this can be a bit can be a bit scary, but it's not that tough to fix with a little custom code.

== Screenshots ==

1. Role management screen
2. Edit role screen
3. Content permissions meta box (edit post/page screen)
4. Plugin settings screen
5. Select multiple roles per user (edit user screen)

== Changelog ==

The change log is located in the `changelog.md` file in the plugin folder.  You may also [view the change log](https://github.com/justintadlock/members/blob/master/changelog.md) online.