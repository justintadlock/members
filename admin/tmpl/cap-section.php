<?php
/**
 * Underscore JS template for edit capabilities tab sections.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
?>
<div id="members-tab-{{ data.id }}" class="{{ data.class }}">

	<table class="wp-list-table widefat fixed members-roles-select">

		<thead>
			<tr>
				<th class="column-cap">{{ data.label.cap }}</th>
				<th class="column-grant">{{ data.label.grant }}</th>
				<th class="column-deny">{{ data.label.deny }}</th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<th class="column-cap">{{ data.label.cap }}</th>
				<th class="column-grant">{{ data.label.grant }}</th>
				<th class="column-deny">{{ data.label.deny }}</th>
			</tr>
		</tfoot>

		<tbody></tbody>
	</table>
</div>
