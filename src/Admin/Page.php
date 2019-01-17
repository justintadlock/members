<?php
/**
 * Handles the new role screen.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2018, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Members\Admin;

interface Page {

	public function boot() {}

	// admin_menu
	public function add_admin_page() {}

	// load-$page
	public function load() {}

	public function add_help_tabs() {}

	public function enqueue() {}

	public function page() {}

}
