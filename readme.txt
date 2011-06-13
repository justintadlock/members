=== Members ===
Contributors: greenshady
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3687060
Tags: admin, role, roles, member, members, profile, shortcode, user, users, widget, widgets
Requires at least: 3.1
Tested up to: 3.2
Stable tag: 0.2

A user, role, and content management plugin that makes WordPress a more powerful CMS.

== Description ==

*Members* is a plugin that extends your control over your blog.  It's a user, role, and content management plugin that was created to make WordPress a more powerful CMS.

The foundation of the plugin is its extensive role and capability management system.  This is the backbone of all the current features and planned future features.

**Plugin Features:**

* Role Manager: Allows you to edit, create, and delete roles as well as capabilities for these roles.
* Content Permissions: Gives you control over which users (by role) have access to post content.
* Shortcodes: Shortcodes to control who has access to content.
* Widgets:  A login form widget and users widget to show in your theme's sidebars.
* Private Site: You can make your site and its feed completely private if you want.

**If updating from a previous version:**

If you've used a previous version of the Members plugin, please check your settings under Settings > Members and reset your Users and Login Form widgets (if you were using them).

== Installation ==

1. Upload `members` to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Go to <em>Settings > Members Components</em> to select which components you'd like to use.

More detailed instructions are included in the plugin's `readme.html` file.

== Frequently Asked Questions ==

= Why was this plugin created? =

I wasn't satisfied with the current user, role, and permissions plugins available.  Yes, some of them are good, but nothing fit what I had in mind perfectly.  Some offered few features.  Some worked completely outside of the WordPress APIs.  Others lacked the GPL license.

This plugin is still a long way away from my goals, but it'll get there eventually.

= How do I use it? =

Most things should be fairly straightforward, but I've included an in-depth guide in the plugin download.  It's a file called `readme.html` in the `/docs` folder.

You'll want to look over that.  It's probably the most in-depth plugin documentation you'll ever read. ;)

Now, open up the `/docs/readme.html` file included in the plugin download and read the documentation.

= I can't access the "Role Manager" features. =

When the plugin is first activated, it runs a script that sets specific capabilities to the "Administrator" role on your site that grants you access to this feature.  So, you must be logged in with the administrator account to access the role manager.

If, for some reason, you do have the administrator role and the role manager is still inaccesible to you, deactivate the plugin.  Then, reactivate it.

= Help! I've locked myself out of my site! =

Well, that's why you really need to read the documentation for the plugin before actually using it, especially a plugin that controls permissions for your site.

== Screenshots ==

1. Members plugin settings
2. Role management screen
3. Edit role screen
4. Members settings help tab
5. Content permissions on the edit post screen

== Changelog ==

**Version 0.2**

* Updated everything.  Nearly all the code was rewritten from the ground up to make for a better user experience.
* Plugin users should check their plugin settings.

**Version 0.1.1**

* Fixed a bug with the Content Permissions component that restricted access to everyone.
* Added missing internationalization function call: `load_plugin_textdomain()`.
* Added new `/languages` folder for holding translations.
* Added `members-en_EN.po`, `members-en_EN.mo`, and `members.pot` to the `/languages` folder.
* Updated some non-internationalized strings.

**Version 0.1**

* Plugin launch.  Everything's new!

== Upgrade Notice ==

= Version 0.2 =

Version 0.2 is a complete overhaul of the plugin.  It includes security fixes, bug fixes, and a few new features.  Please check your plugin settings and widget settings.