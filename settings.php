<?php
/**
 * The components settings page is an admin page that allows users to select which 
 * components of the plugin they wish to use.
 *
 * @package Members
 */

/* Get the global $members object. */
global $members; ?>

<div class="wrap">

	<h2><?php _e('Select Components', 'members'); ?></h2>

	<?php do_action( 'members_pre_components_form' ); // Pre-form hook.  Useful for things like messages once a component has been activated. ?>

	<div id="poststuff">

		<form method="post" action="options.php">

			<?php settings_fields( 'members_plugin_settings' ); ?>
			<?php $options = get_option( 'members_settings' ); ?>

			<table id="all-plugins-table" class="widefat">

				<thead>
					<tr>
						<th class='check-column'><input type='checkbox' /></th>
						<th class='name-column'><?php _e('Component', 'members'); ?></th>
						<th><?php _e('Description', 'members'); ?></th>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<th class='check-column'><input type='checkbox' /></th>
						<th class='name-column'><?php _e('Component', 'members'); ?></th>
						<th><?php _e('Description', 'members'); ?></th>
					</tr>
				</tfoot>

				<?php if ( is_array( $members->registered_components ) ) { ?>

					<tbody class="plugins">

					<?php foreach ( $members->registered_components as $component ) { ?>

						<?php if ( !$component->name ) continue; ?>

						<tr valign="top" class="<?php if ( isset($options[$component->name]) ) echo 'active'; else echo 'inactive'; ?>">

							<th class="manage-column column-cb check-column">
								<input type="checkbox" name="members_settings[<?php echo $component->name; ?>]" id="<?php echo $component->name; ?>" value="1" <?php checked( '1', isset($options[$component->name]) ); ?> />
							</th><!-- manage-column .column-cb .check-column -->
	
							<td class="plugin-title">
								<label for="<?php echo $component->name; ?>"><strong><?php echo $component->label; ?></strong></label>
							</td><!-- .plugin-title -->

							<td class="desc">
								<p><?php echo $component->description; ?></p>
							</td><!-- .desc -->

						</tr><!-- .active/.inactive -->

					<?php } // End loop through registered components. ?>

					</tbody><!-- .plugins -->

				<?php } // End check for registered components ?>

			</table><!-- #all-plugins-table .widefat -->

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Activate', 'members') ?>" />
			</p><!-- .submit -->

		</form>

	</div><!-- #poststuff -->

</div><!-- .wrap -->