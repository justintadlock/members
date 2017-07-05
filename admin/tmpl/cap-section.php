<?php
/**
 * Underscore JS template for edit capabilities tab sections.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justintadlock@gmail.com>
 * @copyright  Copyright (c) 2009 - 2017, Justin Tadlock
 * @link       https://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
?>
<div id="members-tab-{{ data.id }}" class="{{ data.class }}">

	<table class="wp-list-table widefat fixed members-roles-select">

		<thead>
			<tr>
				<th class="column-cap"><?php esc_html_e( 'Capability', 'members' ); ?></th>
				<th class="column-grant"><?php esc_html_e( 'Grant', 'members' ); ?></th>
				<th class="column-deny"><?php esc_html_e( 'Deny', 'members' ); ?></th>
			</tr>
		</thead>

		<tfoot>
			<tr>
				<th class="column-cap"><?php esc_html_e( 'Capability', 'members' ); ?></th>
				<th class="column-grant"><?php esc_html_e( 'Grant', 'members' ); ?></th>
				<th class="column-deny"><?php esc_html_e( 'Deny', 'members' ); ?></th>
			</tr>
		</tfoot>

		<tbody></tbody>
	</table>
</div>
