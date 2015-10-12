# Change Log

## [1.1.0] - 2015-10-12

### Added

* `Text Domain` plugin header added.
* `Domain Path` plugin header added.
* `members_role_updated` action hook for when a role is edited/updated.
* `members_role_added` action hook for when a new role is created.
* `members_manage_roles_column_{$column_name}` filter hook for handling the output of custom manage roles screen columns.
* `members_cp_meta_box_before` action hook for hooking in before the Content Permissions meta box.
* `members_cp_meta_box_after` action hook for hooking in after the Content Permissions meta box.
* Added the `Members_Role_Factory::remove_role()` method to remove a stored role.

### Changed

* Edit/New role forms just check the nonce instead of checking for form fields + nonce to see if the form was submitted (fields can be legitimately empty).

### Fixed

* Correct "undefined index" notices with widgets.
* Make sure custom caps (post types, taxonomies, etc.) that aren't stored get saved when editing a role.
* Make sure the "All" capability group actually lists all caps from all groups.
* Use the `$user` variable instead of `$author` variable in `members_list_users()`.
* "Custom" cap group should always be added last.
* Make sure roles edited with no caps get processed. Previously, we bailed if no caps were set.
* Remove deleted roles from the manage roles screen list without having to refresh the page.

### Security

* Use `wp_strip_all_tags()` over `strip_tags()` for sanitizing the role name.
* Use `esc_url_raw()` to escape the redirect URL after creating a new role.

## [1.0.2] - 2015-09-15

### Fixed

* Make sure `$attr` is set by `shortcode_atts()` in the `[members_access]` shortcode.
* Use `members_current_user_has_role()` in the `[members_access]` shortcode.
* Use `! empty()` to validate the checkboxes when settings are saved.

## [1.0.1] - 2015-09-14

### Fixed

* Only load `edit-role.js` on the role management screens.

## [1.0.0] - 2015-09-13

### Added

* Ability to clone existing roles.
* Add multiple roles per user.
* Ability to explicitly deny capabilities to roles.
* Capability groups to find related caps when editing a role.
* Role groups to group related roles together.
* Content Permissions handles HTML, shortcodes, and auto-embeds.
* Disabled capabilities (via `wp-config.php` settings) no longer show cap lists.
* Additional role and capability functions to extend WP's existing APIs.
* Role translation filters so that plugin authors can make role names translatable.
* Uneditable roles are now shown in the UI. They can be viewed but not edited.
* `Members_Role` object and API to extend WP's existing Roles API.
* Plugin authors can now add meta boxes to the edit role screen.
* Underscore JS based capabilities editing box for roles.

### Changed

* Complete UI overhaul for all user-facing components in the plugin.
* Overhaul of all text strings in the plugin.
* Shortcodes are now prefixed with `members_`. Old shortcodes will continue to work.
* Overhauled the widgets code with new text strings and general code cleanup.
* Using the newer WP help tab functionality with custom tabs.

### Deprecated

* `members_get_active_roles()`. Replaced by `members_get_active_role_names()`.
* `members_get_inactive_roles()`. Replaced by `members_get_inactive_role_names()`.
* `members_count_roles()`. Replaced by `members_get_role_count()`.
* `members_get_default_capabilities()`. Replaced by `members_get_wp_capabilities()`.
* `members_get_additional_capabilities()`. Replaced by `members_get_plugin_capabilities()`.

### Removed

* Upgrade routine.
* All old translation files since the text strings were overhauled.
* Plugin constants in favor of `Members_Plugin` properties.
* `$members` global in favor of `Members_Plugin` properties.

### Fixed

* Content Permissions meta box now accepts HTML on the edit post screen.
* User levels are now hidden from the role capability count.
* Uses the proper path for translation files in case plugin folder name changes.
* An "Are you sure?" (AYS) popup prevents users from inadvertently deleting roles.
* Added missing `export` and `delete_themes` caps to the core caps backup function.
* Removed CSS code that was hiding the "collapse menu" button for the admin menu.

### Security

* Added the `members_sanitize_role()` function.
* Added the `members_sanitize_cap()` function.
* Security hardening by making sure all URLs are escaped.
* Security hardening by making sure all dynamic data is escaped.
* Security hardening by escaping translated text.

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