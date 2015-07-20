<?php require_once( members_plugin()->admin_dir . 'class-role-list-table.php' ); ?>

<div class="wrap">

	<h2>
		<?php esc_html_e( 'Roles', 'members' ); ?>

		<?php if ( current_user_can( 'create_roles' ) ) : ?>
			<a href="<?php echo members_get_new_role_url(); ?>" class="add-new-h2"><?php esc_html_e( 'Add New', 'members' ); ?></a>
		<?php endif; ?>
	</h2>

	<?php do_action( 'members_pre_edit_roles_form' ); // Available action hook for displaying messages. ?>

	<div id="poststuff">

		<form id="roles" action="<?php echo members_get_edit_roles_url(); ?>" method="post">

			<?php $table = new Members_Role_List_Table(); ?>
			<?php $table->prepare_items(); ?>
			<?php $table->display(); ?>

		</form><!-- #roles -->

		<script type="text/javascript">
			jQuery( '.members-delete-role-link' ).click( function() {
				return window.confirm( '<?php esc_html_e( 'Are you sure you want to delete this role? This is a permanent action and cannot be undone.', 'members' ); ?>' );
			} );
		</script>

	</div><!-- #poststuff -->

</div><!-- .wrap -->
