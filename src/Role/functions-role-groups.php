<?php
/**
 * Role groups API. Offers a standardized method for creating role groups.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2018, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

namespace Members\Role;

use Members\App;

function groups() {

	return App::resolve( 'role/groups' );
}
