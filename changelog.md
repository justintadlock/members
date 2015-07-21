# Change Log

## [0.2.5] - 2015-07-09

### Fixed

* Deprecated old widget constructor method in favor of using `__construct()` in preparation for WP 4.3.
* Removed old `/components` folder and files that's not used.

## [0.2.4]

* Fixed content permissions not saving for attachments. Note that this only protects **content** and not media files.
* No longer runs the upgrade script when `WP_INSTALLING` is `TRUE`.

## [0.2.3]

* Fixes the strict standards notice "Redefining already defined constructor for class Members_Load".
* No longer uses `&` for passing the role name by reference on plugin activation.
* Fixes the `[feed]` shortcode, which was using the wrong callback function.

## [0.2.2]

* No longer displays non-editable roles on the edit roles screen.

## [0.2.1]

* Fixes the " Creating default object from empty value" error.

## [0.2.0]

* Updated everything.  Nearly all the code was rewritten from the ground up to make for a better user experience.
* Plugin users should check their plugin settings.

## [0.1.1]

* Fixed a bug with the Content Permissions component that restricted access to everyone.
* Added missing internationalization function call: `load_plugin_textdomain()`.
* Added new `/languages` folder for holding translations.
* Added `members-en_EN.po`, `members-en_EN.mo`, and `members.pot` to the `/languages` folder.
* Updated some non-internationalized strings.

## [0.1.0]

* Plugin launch.  Everything's new!