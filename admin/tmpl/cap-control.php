<?php
/**
 * Underscore JS template for edit capabilities tab section controls.
 *
 * @package    Members
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2009 - 2015, Justin Tadlock
 * @link       http://themehybrid.com/plugins/members
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
?>
<tr class="members-cap-checklist">
	<td class="column-cap">
		<button type="button"><strong>{{ data.cap }}</strong></button>
		<i class="dashicons <?php echo is_rtl() ? 'dashicons-arrow-left' : 'dashicons-arrow-right'; ?>"></i>
	</td>

	<td class="column-grant">
		<span class="screen-reader-text">{{{ data.label.grant }}}</span>
		<input {{{ data.readonly }}} type="checkbox" name="{{ data.name.grant }}" data-grant-cap="{{ data.cap }}" value="{{ data.cap }}" <# if ( data.is_granted_cap ) { #>checked="checked"<# } #> />
	</td>

	<td class="column-deny">
		<span class="screen-reader-text">{{{ data.label.deny }}}</span>
		<input {{{ data.readonly }}} type="checkbox" name="{{ data.name.deny }}" data-deny-cap="{{ data.cap }}" value="{{ data.cap }}" <# if ( data.is_denied_cap ) { #>checked="checked"<# } #> />
	</td>
</tr>
