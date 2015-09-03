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
		jQuery( '.members-which-tab' ).text( jQuery( '.members-tab-nav :first-child a' ).text() );

		jQuery( '.members-tab-nav li a' ).click(
			function( j ) {
				j.preventDefault();

				var href = jQuery( this ).attr( 'href' );

				jQuery( this ).parents( '.members-cap-tabs' ).find( '.members-tab-content' ).hide();

				jQuery( this ).parents( '.members-cap-tabs' ).find( href ).show();

				jQuery( this ).parents( '.members-cap-tabs' ).find( '.members-tab-title' ).attr( 'aria-selected', 'false' );

				jQuery( this ).parent().attr( 'aria-selected', 'true' );

				jQuery( '.members-which-tab' ).text( jQuery( this ).text() );
			}
		);

		// Show hidden stuff.
		jQuery( '.hide-if-no-js' ).show();

		jQuery( document ).on( 'click', '.editable-role .members-cap-checklist label',
			function() {

				var parent = jQuery( this ).closest( '.members-cap-checklist' );

				var grant = jQuery( parent ).find( 'input[data-grant-cap]' );
				var deny = jQuery( parent ).find( 'input[data-deny-cap]' );

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
				var granted_count = jQuery( "#members-tab-all input[data-grant-cap]:checked" ).length;

				var denied_count = jQuery( "#members-tab-all input[data-deny-cap]:checked" ).length;

				jQuery( '#submitdiv .granted-count' ).text( granted_count );
				jQuery( '#submitdiv .denied-count' ).text( denied_count );

		jQuery( document ).on( 'change', '.members-cap-checklist input[data-grant-cap]',
			function() {

				var data_grant = jQuery( this ).attr( 'data-grant-cap' );

				if ( this.checked ) {

					jQuery( 'input[data-grant-cap="' + data_grant + '"]' ).not( this ).prop( 'checked', true );
					jQuery( 'input[data-deny-cap="' + data_grant + '"]' ).prop( 'checked', false );

				} else {

					jQuery( 'input[data-grant-cap="' + data_grant + '"]' ).not( this ).prop( 'checked', false );
				}

				var granted_count = jQuery( "#members-tab-all input[data-grant-cap]:checked" ).length;
				var denied_count = jQuery( "#members-tab-all input[data-deny-cap]:checked" ).length;

				var new_granted_count = jQuery( '#members-tab-custom input[name="grant-new-caps[]"]:checked' ).length;
				var new_denied_count  = jQuery( '#members-tab-custom input[name="deny-new-caps[]"]:checked' ).length;

				jQuery( '#submitdiv .granted-count' ).text( granted_count + new_granted_count );
				jQuery( '#submitdiv .denied-count' ).text( denied_count + new_denied_count );

			}
		);

		jQuery( document ).on( 'change', '.members-cap-checklist input[data-deny-cap]',
			function() {

				var data_deny = jQuery( this ).attr( 'data-deny-cap' );

				if ( this.checked ) {

					jQuery( 'input[data-deny-cap="' + data_deny + '"]' ).not( this ).prop( 'checked', true );
					jQuery( 'input[data-grant-cap="' + data_deny + '"]' ).prop( 'checked', false );

				} else {

					jQuery( 'input[data-deny-cap="' + data_deny + '"]' ).not( this ).prop( 'checked', false );
				}

				var granted_count = jQuery( "#members-tab-all input[data-grant-cap]:checked" ).length;
				var denied_count  = jQuery( "#members-tab-all input[data-deny-cap]:checked" ).length;

				var new_granted_count = jQuery( '#members-tab-custom input[name="grant-new-caps[]"]:checked' ).length;
				var new_denied_count  = jQuery( '#members-tab-custom input[name="deny-new-caps[]"]:checked' ).length;

				jQuery( '#submitdiv .granted-count' ).text( granted_count + new_granted_count );
				jQuery( '#submitdiv .denied-count' ).text( denied_count + new_denied_count );

			}
		);
	}
);