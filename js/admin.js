jQuery( document ).ready(
	function() {

		/* ====== Plugin Settings ====== */

		// Hide content permissions message if disabled.
		if ( false === jQuery( '[name="members_settings[content_permissions]"]' ).prop( 'checked' ) ) {

			jQuery( '[name="members_settings[content_permissions]"]' ).parents( 'tr' ).next( 'tr' ).hide();
		}

		// Hide private feed if private blog disabled.
		if ( false === jQuery( '[name="members_settings[private_blog]"]' ).prop( 'checked' ) ) {

			jQuery( '[name="members_settings[private_blog]"]' ).parents( 'tr' ).next( 'tr' ).hide();
		}

		// Hide private feed message if private feed disabled.
		if ( false === jQuery( '[name="members_settings[private_feed]"]' ).prop( 'checked' ) ) {

			jQuery( '[name="members_settings[private_feed]"]' ).parents( 'tr' ).next( 'tr' ).hide();
		}

		// Show above hidden items if feature becomes enabled.
		jQuery( '[name="members_settings[content_permissions]"], [name="members_settings[private_blog]"], [name="members_settings[private_feed]"]' ).on( 'change',
			function() {

				if ( jQuery( this ).prop( 'checked' ) ) {

					jQuery( this ).parents( 'tr' ).next( 'tr' ).show( 'slow' );
				} else {

					jQuery( this ).parents( 'tr' ).next( 'tr' ).hide( 'slow' );
				}
			}
		);

		/* ====== Edit Role ====== */


		jQuery( '.members-cap-tabs .members-tab-content' ).hide();
		jQuery( '.members-cap-tabs .members-tab-content:first-child' ).show();
		jQuery( '.members-tab-nav :first-child' ).attr( 'aria-selected', 'true' );

		jQuery( '.members-tab-nav li a' ).click(
			function( j ) {
				j.preventDefault();

				var href = jQuery( this ).attr( 'href' );

				jQuery( this ).parents( '.members-cap-tabs' ).find( '.members-tab-content' ).hide();

				jQuery( this ).parents( '.members-cap-tabs' ).find( href ).show();

				jQuery( this ).parents( '.members-cap-tabs' ).find( '.members-tab-title' ).attr( 'aria-selected', 'false' );

				jQuery( this ).parent().attr( 'aria-selected', 'true' );
			}
		);

		// Show hidden stuff.
		jQuery( '.hide-if-no-js' ).show();

		/*jQuery( '.members-cap-checklist .members-grant-cb, .members-cap-checklist .members-deny-cb' ).change(

			function() {

				var parent = jQuery( this ).closest( '.members-cap-checklist' );

				if ( this.checked ) {
					jQuery( parent ).find( '.members-cap-name' ).addClass( 'has-cap' );
				} else {
					jQuery( parent ).find( '.members-cap-name' ).removeClass( 'has-cap' );
				}

			}
		);*/

		jQuery( '.members-cap-checklist label' ).click(
			function() {

				var parent = jQuery( this ).closest( '.members-cap-checklist' );

				var grant = jQuery( parent ).find( '.members-grant-cb' );
				var deny = jQuery( parent ).find( '.members-deny-cb' );

				if ( jQuery( grant ).prop( 'checked' ) ) {

					jQuery( grant ).prop( 'checked', false ).trigger( 'change' );
					jQuery( deny ).prop( 'checked', true ).trigger( 'change' );

				} else if ( jQuery( deny ).prop( 'checked' ) ) {

					jQuery( grant ).prop( 'checked', false ).trigger( 'change' );
					jQuery( deny ).prop( 'checked', false ).trigger( 'change' );

				} else {
					jQuery( grant ).prop( 'checked', true ).trigger( 'change' );
				}
			}
		);

		jQuery( '.members-cap-checklist .members-grant-cb' ).change(
			function() {

				var data_grant = jQuery( this ).attr( 'data-grant-cap' );

				if ( this.checked ) {

					//var parent = jQuery( this ).closest( '.members-cap-checklist' );

					//jQuery( parent ).find( '.members-deny-cb' ).prop( 'checked', false );

					jQuery( 'input[data-grant-cap="' + data_grant + '"]' ).not( this ).prop( 'checked', true );
					jQuery( 'input[data-deny-cap="' + data_grant + '"]' ).prop( 'checked', false );

				} else {

					jQuery( 'input[data-grant-cap="' + data_grant + '"]' ).not( this ).prop( 'checked', false );
				}

			}
		);

		jQuery( '.members-cap-checklist .members-deny-cb' ).change(
			function() {

				var data_deny = jQuery( this ).attr( 'data-deny-cap' );

				if ( this.checked ) {
					//var parent = jQuery( this ).closest( '.members-cap-checklist' );

					//jQuery( parent ).find( '.members-grant-cb' ).prop( 'checked', false );

					jQuery( 'input[data-deny-cap="' + data_deny + '"]' ).not( this ).prop( 'checked', true );
					jQuery( 'input[data-grant-cap="' + data_deny + '"]' ).prop( 'checked', false );

				} else {

					jQuery( 'input[data-deny-cap="' + data_deny + '"]' ).not( this ).prop( 'checked', false );
				}

			}
		);


		// New cap input box.
		jQuery( '#members-add-new-cap' ).click(
			function() {
				jQuery( 'p.new-cap-holder' ).append( '<input type="text" class="new-cap" name="new-cap[]" value="" size="20" />' );
			}
		);

		// Add `has-cap` class to caps the role has.
		jQuery( '.members-role-checkbox input[type="checkbox"]:checked' ).parent( 'label' ).addClass( 'has-cap' );

		// Toggle the `has-cap` class when checkbox is clicked.
		jQuery( 'div.members-role-checkbox input[type="checkbox"]' ).click(
			function() {
				jQuery( this ).parent( 'label' ).toggleClass( 'has-cap' );
			}
		);
	}
);