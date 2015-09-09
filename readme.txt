=== Members ===

Contributors: greenshady
Donate link: http://themehybrid.com/donate
Tags: admin, role, roles, member, members, profile, shortcode, user, users, widget, widgets
Requires at least: 3.4
Stable tag: 0.2.5

A user, role, and content management plugin that makes WordPress a more powerful CMS.

== Description ==

Members is a plugin that extends your control over your blog.  It's a user, role, and content management plugin that was created to make WordPress a more powerful CMS.

The foundation of the plugin is its extensive role and capability management system.  This is the backbone of all the current features and planned future features.

### Plugin Features:

* **Role Manager:** Allows you to edit, create, and delete roles as well as capabilities for these roles.
* **Multiple User Roles:** Give one, two, or even more roles to any user.
* **Content Permissions:** Gives you control over which users (by role) have access to post content.
* **Shortcodes:** Shortcodes to control who has access to content.
* **Widgets:**  A login form widget and users widget to show in your theme's sidebars.
* **Private Site:** You can make your site and its feed completely private if you want.

### Professional Support

If you need professional plugin support from me, the plugin author, you can access the support forums at [Theme Hybrid](http://themehybrid.com/support), which is a professional WordPress help/support site where I handle support for all my plugins and themes for a community of 60,000+ users (and growing).

### Plugin Development

If you're a theme author, plugin author, or just a code hobbyist, you can follow the development of this plugin on it's [GitHub repository](https://github.com/justintadlock/members). 

### Donations

Yes, I do accept donations.  If you want to buy me a beer or whatever, you can do so from my [donations page](http://themehybrid.com/donate).  I appreciate all donations, no matter the size.  Further development of this plugin is not contingent on donations, but they are always a nice incentive.

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

You'll need to stop by my [support forums](http://themehybrid.com/board/topics) to see if we can get your site fixed if you managed to lock yourself out.

== Screenshots ==

1. Role management screen
2. Edit role screen
3. Content permissions meta box (edit post/page screen)
4. Plugin settings screen
5. Select multiple roles per user (edit user screen)

== Changelog ==

The change log is located in the `changelog.md` file in the plugin folder.  You may also [view the change log](https://github.com/justintadlock/members/blob/master/changelog.md) online.