=== Members ===
Contributors: greenshady, ptahdunbar
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3687060
Tags: admin, cms, community, profile, shortcode, user, users
Requires at least: 2.8
Tested up to: 2.8.4
Stable tag: 0.1.1

A user, role, and content management plugin that makes WordPress a more powerful CMS.

== Description ==

*Members* is a plugin that extends your control over your blog.  It's a user, role, and content management plugin that was created to make WordPress a more powerful CMS.

The plugin is created with a components-based system &mdash; you only have to use the features you want.

The foundation of the plugin is its extensive role and capability management system.  This is the backbone of all the current features and planned future features.

**Components (i.e., features):**

* Edit Roles: Edit (and delete) specific roles and each role's capabilities.
* New Roles: Create new roles for use on your site.
* Content Permissions: Control what roles have access to specific posts and pages.
* Shortcodes: Use [shortcodes] to restrict/allow access to content.
* Template Tags: Functions to be used within your WordPress theme.
* Widgets: A login form and user list widget for use in any widget areas.
* Private Blog: Force visitors to log in before viewing your site.

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

Most things should be fairly straightforward, but I've included an in-depth guide in the plugin download.  It's a file called `readme.html`.  

You'll want to look over that.  It's probably the most in-depth plugin documentation you'll ever read. ;)

== Changelog ==

**Version 0.1.1**

* Fixed a bug with the Content Permissions component that restricted access to everyone.
* Added missing localization call: `load_plugin_textdomain()`.
* Added new `/languages` folder for holding translations.
* Added `members-en_EN.po`, `members-en_EN.mo`, and `members.pot` to the `/languages` folder.
* Updated some unlocalized strings.

**Version 0.1**

* Plugin launch.  Everything's new!