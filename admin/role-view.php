<?php
/**
 * This file handles the display of the edit role form and the updates submitted by the user for the role.
 *
 * @package Members
 * @subpackage Admin
 */

// Get the current role object to edit.
$role = get_role( members_sanitize_role( $_GET['role'] ) );

// If we don't have a real role, die.
if ( is_null( $role ) )
	wp_die( esc_html__( 'The requested role does not exist.', 'members' ) );

// Get all the capabilities.
$capabilities = members_get_capabilities(); ?>

<div class="wrap">

	<h2>
		<?php esc_html_e( 'View Role', 'members' ); ?>
		<?php if ( current_user_can( 'create_roles' ) ) printf( '<a class="add-new-h2" href="%s">%s</a>', members_get_new_role_url(), esc_html__( 'Add New', 'members' ) ); ?>
	</h2>

	<?php if ( ! members_is_role_editable( $role->name ) ) : ?>
		<div class="error">
			<p><?php esc_html_e( 'The current role is not editable. This means that it is most likely added via a third-party plugin that has a special use case for it or that you do not have permission to edit it.', 'members' ); ?></p>
		</div>
	<?php endif; ?>

	<div id="poststuff">

			<table class="form-table">

				<tr>
					<th>
						<?php esc_html_e( 'Role', 'members' ); ?>
					</th>
					<td>
						<input type="text" disabled="disabled" readonly="readonly" value="<?php echo esc_attr( $role->name ); ?>" />
					</td>
				</tr>

				<tr>
					<th>
						<?php esc_html_e( 'Role Name', 'members' ); ?>
					</th>
					<td>
						<input type="text" disabled="disabled" readonly="readonly" value="<?php echo esc_attr( members_get_role_name( $role->name ) ); ?>" />
					</td>
				</tr>

				<tr>
					<th>
						<?php esc_html_e( 'Capabilities', 'members' ); ?>
					</th>

					<td>
						<?php $i = -1; foreach ( $capabilities as $cap ) : ?>

							<div class="members-role-checkbox <?php if ( ++$i % 3 == 0 ) echo 'clear'; ?>">
								<?php $has_cap = $role->has_cap( $cap ) ? true : false; // Note: $role->has_cap() returns a string intead of TRUE. ?>
								<label class="<?php echo ( $has_cap ? 'has-cap' : 'has-cap-not' ); ?>">
									<input type="checkbox" disabled="disabled" readonly="readonly" name="<?php echo esc_attr( "role-caps[{$role->name}-{$cap}]" ); ?>" <?php checked( true, $has_cap ); ?> value="true" />
									<?php echo esc_html( $cap ); ?>
								</label>
							</div>

						<?php endforeach; ?>
					</td>
				</tr>

			</table><!-- .form-table -->

	</div><!-- #poststuff -->

</div><!-- .wrap -->
