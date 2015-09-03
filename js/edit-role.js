jQuery( document ).ready( function() {

	/* ====== Tabs ====== */

	// Hides the tab content.
	jQuery( '.members-cap-tabs .members-tab-content' ).hide();

	// Shows the first tab's content.
	jQuery( '.members-cap-tabs .members-tab-content:first-child' ).show();

	// Makes the 'aria-selected' attribute true for the first tab nav item.
	jQuery( '.members-tab-nav :first-child' ).attr( 'aria-selected', 'true' );

	// Copies the current tab item title to the box header.
	jQuery( '.members-which-tab' ).text( jQuery( '.members-tab-nav :first-child a' ).text() );

	// When a tab nav item is clicked.
	jQuery( '.members-tab-nav li a' ).click(
		function( j ) {

			// Prevent the default browser action when a link is clicked.
			j.preventDefault();

			// Get the `href` attribute of the item.
			var href = jQuery( this ).attr( 'href' );

			// Hide all tab content.
			jQuery( this ).parents( '.members-cap-tabs' ).find( '.members-tab-content' ).hide();

			// Find the tab content that matches the tab nav item and show it.
			jQuery( this ).parents( '.members-cap-tabs' ).find( href ).show();

			// Set the `aria-selected` attribute to false for all tab nav items.
			jQuery( this ).parents( '.members-cap-tabs' ).find( '.members-tab-title' ).attr( 'aria-selected', 'false' );

			// Set the `aria-selected` attribute to true for this tab nav item.
			jQuery( this ).parent().attr( 'aria-selected', 'true' );

			// Copy the current tab item title to the box header.
			jQuery( '.members-which-tab' ).text( jQuery( this ).text() );
		}
	); // click()

	/* ====== Capability Checkboxes (inside tab content) ====== */

	/**
	 * Counts the number of granted and denied capabilities that are checked and updates
	 * the count in the submit role meta box.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	function members_count_caps() {

		// Count the granted and denied caps that are checked.
		var granted_count = jQuery( "#members-tab-all input[data-grant-cap]:checked" ).length;
		var denied_count  = jQuery( "#members-tab-all input[data-deny-cap]:checked" ).length;

		// Count the new (added from new cap meta box) granted and denied caps that are checked.
		var new_granted_count = jQuery( '#members-tab-custom input[name="grant-new-caps[]"]:checked' ).length;
		var new_denied_count  = jQuery( '#members-tab-custom input[name="deny-new-caps[]"]:checked' ).length;

		// Update the submit meta box cap count.
		jQuery( '#submitdiv .granted-count' ).text( granted_count + new_granted_count );
		jQuery( '#submitdiv .denied-count' ).text( denied_count + new_denied_count );
	}

	/**
	 * When a grant/deny checkbox has a change, this function makes sure that any duplicates
	 * also receive that change.  It also unchecks the grant/deny opposite checkbox if needed.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $type
	 * @param  string  $opposite
	 * @return void
	 */
	function members_check_uncheck( type, opposite ) {

		// Get the capability for this checkbox.
		var data_grant = jQuery( this ).attr( 'data-' + type + '-cap' );

		// If the checkbox is checked.
		if ( this.checked ) {

			// Check any duplicate checkboxes.
			jQuery( 'input[data-' + type + '-cap="' + data_grant + '"]' ).not( this ).prop( 'checked', true );

			// Uncheck any deny checkboxes with the same cap.
			jQuery( 'input[data-' + opposite + '-cap="' + data_grant + '"]' ).prop( 'checked', false );

		// If the checkbox is not checked.
		} else {

			// Uncheck any duplicate checkboxes.
			jQuery( 'input[data-' + type + '-cap="' + data_grant + '"]' ).not( this ).prop( 'checked', false );
		}
	}

	// Count the granted and denied caps that are checked.
	members_count_caps();

	// When a change is triggered for any grant checkbox. Note that we're using `.on()`
	// here because we're dealing with dynamically-generated HTML.
	jQuery( document ).on( 'change',
		'.members-cap-checklist input[data-grant-cap], .members-cap-checklist input[data-deny-cap]',
		function() {

			// If this is a grant checkbox.
			if ( jQuery( this ).attr( 'data-grant-cap' ) ) {

				members_check_uncheck( 'grant', 'deny' );

			// If this is a deny checkbox.
			} else if ( jQuery( this ).attr( 'data-deny-cap' ) ) {

				members_check_uncheck( 'deny', 'grant' );
			}

			// Count the granted and denied caps that are checked.
			members_count_caps();
		}
	); // .on( 'change' )

	// When a cap label is clicked. Note that we're using `.on()` here because we're dealing
	// with dynamically-generated HTML.
	jQuery( document ).on( 'click', '.editable-role .members-cap-checklist label',
		function() {

			// Get the label parent element.
			var parent = jQuery( this ).closest( '.members-cap-checklist' );

			// Find the grant and deny checkbox inputs.
			var grant = jQuery( parent ).find( 'input[data-grant-cap]' );
			var deny  = jQuery( parent ).find( 'input[data-deny-cap]' );

			// If the grant checkbox is checked.
			if ( jQuery( grant ).prop( 'checked' ) ) {

				jQuery( grant ).prop( 'checked', false ).trigger( 'change' );
				jQuery( deny ).prop( 'checked', true ).trigger( 'change' );

			// If the deny checkbox is checked.
			} else if ( jQuery( deny ).prop( 'checked' ) ) {

				jQuery( grant ).prop( 'checked', false ).trigger( 'change' );
				jQuery( deny ).prop( 'checked', false ).trigger( 'change' );

			// If neither checkbox is checked.
			} else {

				jQuery( grant ).prop( 'checked', true ).trigger( 'change' );
			}
		}
	); // on()

} ); // ready()
