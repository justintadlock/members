<?php
/**
 * This file is used to disable the comments and comments form from showing comments on posts that a user
 * doesn't have access to.  The use of this file assumes that the theme properly uses the comments_template()
 * function.  This is required because the plugin hooks into the 'comments_template' filter hook to load this
 * empty file.
 *
 * Theme authors can overwrite this with a `comments-no-access.php` template in their themes.
 */
