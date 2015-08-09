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

		// Show hidden stuff.
		jQuery( '.hide-if-no-js' ).show();

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