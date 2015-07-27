<?php
global $wp_roles;

$new_cap_added = false;
$cap = '';
$roles = array();
$messages = array();

if ( current_user_can( 'edit_roles' ) && isset( $_POST['members_new_cap_nonce'] ) ) {

	// Verify the nonce.
	check_admin_referer( 'new_cap', 'members_new_cap_nonce' );

	if ( isset( $_POST['capability'] ) )
		$cap = members_sanitize_cap( $_POST['capability'] );

	if ( empty( $cap ) )
		$messages['no_cap'] = esc_html__( 'Please input a valid capability.', 'members' );

	else if ( in_array( $cap, members_get_capabilities() ) )
		$messages['cap_exists'] = sprintf( esc_html__( 'The %s capability already exists.', 'members' ), '<code>' . $cap . '</code>' );

	if ( isset( $_POST['roles'] ) && is_array( $_POST['roles'] ) )
		$roles = array_map( 'members_sanitize_role', $_POST['roles'] );

	if ( empty( $roles ) )
		$messages['no_roles'] = esc_html__( 'Please select at least one role.', 'members' );

	if ( $cap && ! empty( $roles ) ) {

		foreach ( $roles as $role ) {


			$role_obj = get_role( $role );

			$role_obj->add_cap( $cap );
		}

		$new_cap_added = true;

		$cap = '';
		$roles = array();
	}
}
?>

<div class="wrap">

	<h2><?php esc_html_e( 'New Capability', 'members' ); ?></h2>

	<?php if ( !empty( $messages ) ) : ?>

		<?php foreach ( $messages as $message ) : ?>

			<div class="error">
				<p><?php echo $message; ?></p>
			</div>

		<?php endforeach; ?>

	<?php endif; ?>

	<?php if ( $new_cap_added ) : ?>
		<div class="updated">
			<p><strong><?php printf( esc_html__( 'The %s capability has been created.', 'members' ), '<code>' . members_sanitize_cap( $_POST['capability'] ) . '</code>' ); ?></strong></p>
			<p><?php printf( '<a href="%s">%s</a>', members_get_edit_caps_url(), esc_html__( '&larr; Back to capabilities screen', 'members' ) ); ?>
		</div>
	<?php endif; ?>

	<?php do_action( 'members_pre_edit_caps_form' ); // Available action hook for displaying messages. ?>

	<div id="poststuff">

		<form id="roles" action="<?php echo members_get_new_cap_url(); ?>" method="post">

			<?php wp_nonce_field( 'new_cap', 'members_new_cap_nonce' ); ?>

			<table class="form-table">

				<tr>
					<th>
						<label for="capability"><?php esc_html_e( 'Capability', 'members' ); ?></label>
					</th>
					<td>
						<input type="text" id="capability" name="capability" value="<?php echo esc_attr( $cap ); ?>" size="30" />

						<p class="description">
							<?php esc_html_e( 'The capability should be unique and contain only alphanumeric characters and underscores.', 'members' ); ?>
						</p>
					</td>
				</tr>

				<tr>
					<th>
						<?php esc_html_e( 'Roles', 'members' ); ?>
					</th>
					<td>
						<p class="description">
							<?php esc_html_e( 'Select at least one role. Because of the way capabilities work in WordPress, they can only exist if assigned to a role.', 'members' ); ?>
						</p>

						<ul>
							<?php foreach ( $wp_roles->role_names as $role => $name ) : ?>

								<li>
									<label>
										<input type="checkbox" name="roles[<?php echo esc_attr( $role ); ?>]" value="<?php echo esc_attr( $role ); ?>" <?php checked( in_array( $role, $roles ) ); ?> />
										<?php echo esc_html( $name ); ?>
									</label>
								</li>

							<?php endforeach; ?>
						</ul>
					</td>
				</tr>

			</table><!-- .form-table -->

			<?php submit_button( esc_attr__( 'Add Capability', 'members' ) ); ?>

		</form><!-- #roles -->

	</div><!-- #poststuff -->

</div><!-- .wrap -->
