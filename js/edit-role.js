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
	 * @param  object  $checkbox
	 * @return void
	 */
	function members_check_uncheck( checkbox ) {

		var type     = 'grant';
		var opposite = 'deny';

		// If this is a deny checkbox.
		if ( jQuery( checkbox ).attr( 'data-deny-cap' ) ) {

			type     = 'deny';
			opposite = 'grant';
		}

		// Get the capability for this checkbox.
		var cap = jQuery( checkbox ).attr( 'data-' + type + '-cap' );

		// If the checkbox is checked.
		if ( jQuery( checkbox ).prop( 'checked' ) ) {

			// Check any duplicate checkboxes.
			jQuery( 'input[data-' + type + '-cap="' + cap + '"]' ).not( checkbox ).prop( 'checked', true );

			// Uncheck any deny checkboxes with the same cap.
			jQuery( 'input[data-' + opposite + '-cap="' + cap + '"]' ).prop( 'checked', false );

		// If the checkbox is not checked.
		} else {

			// Uncheck any duplicate checkboxes.
			jQuery( 'input[data-' + type + '-cap="' + cap + '"]' ).not( checkbox ).prop( 'checked', false );
		}
	}

	// Count the granted and denied caps that are checked.
	members_count_caps();

	// When a change is triggered for any grant/deny checkbox. Note that we're using `.on()`
	// here because we're dealing with dynamically-generated HTML.
	jQuery( document ).on( 'change',
		'.members-cap-checklist input[data-grant-cap], .members-cap-checklist input[data-deny-cap]',
		function() {

			// Check/Uncheck boxes.
			members_check_uncheck( this );

			// Count the granted and denied caps that are checked.
			members_count_caps();
		}
	); // .on( 'change' )

	// When a cap label is clicked. Note that we're using `.on()` here because we're dealing
	// with dynamically-generated HTML.
	//
	// Note that we only need to trigger `change()` once for our functionality.
	jQuery( document ).on( 'click', '.editable-role .members-cap-checklist label',
		function() {

			// Get the label parent element.
			var parent = jQuery( this ).closest( '.members-cap-checklist' );

			// Find the grant and deny checkbox inputs.
			var grant = jQuery( parent ).find( 'input[data-grant-cap]' );
			var deny  = jQuery( parent ).find( 'input[data-deny-cap]' );

			// If the grant checkbox is checked.
			if ( jQuery( grant ).prop( 'checked' ) ) {

				jQuery( grant ).prop( 'checked', false );
				jQuery( deny ).prop( 'checked', true ).change();

			// If the deny checkbox is checked.
			} else if ( jQuery( deny ).prop( 'checked' ) ) {

				jQuery( grant ).prop( 'checked', false );
				jQuery( deny ).prop( 'checked', false ).change();

			// If neither checkbox is checked.
			} else {

				jQuery( grant ).prop( 'checked', true ).change();
			}
		}
	); // on()

	/* ====== New Cap Meta Box ====== */

	// Create a Underscore template.
	var new_cap_template = wp.template( 'members-new-cap-control' );

	// Disable the new cap button so that it's not clicked until there's a cap.
	jQuery( '#members-add-new-cap' ).prop( 'disabled', true );

	// When the user starts typing a new cap.
	jQuery( '#members-new-cap-field' ).on( 'input',
		function() {

			// If there's a value in the input, enable the add new button.
			if ( jQuery( this ).val() ) {

				jQuery( '#members-add-new-cap' ).prop( 'disabled', false );

			// If there's no value, disable the button.
			} else {
				jQuery( '#members-add-new-cap' ).prop( 'disabled', true );
			}
		}
	); // .on( 'input' )

	// When the new cap button is clicked.
	jQuery( '#members-add-new-cap' ).click(
		function() {

			// Get the new cap value.
			var new_cap = jQuery( '#members-new-cap-field' ).val();

			// Sanitize the new cap.
			// Note that this will be sanitized on the PHP side as well before save.
			new_cap = new_cap.replace( /<.*?>/g, '' ).replace( /\s/g, '_' ).replace( /[^a-zA-Z0-9_]/g, '' );

			// If there's a new cap value.
			if ( new_cap ) {

				// Trigger a click event on the "custom" tab in the edit caps box.
				jQuery( 'a[href="#members-tab-custom"]' ).trigger( 'click' );

				// Set up some data to pass to our Underscore template.
				var data = { cap : new_cap, is_granted_cap : true, is_denied_cap : false };

				// Prepend our template to the "custom" edit caps tab content.
				jQuery( '#members-tab-custom tbody' ).prepend( new_cap_template( data ) );

				// Set the new cap input value to an empty string.
				jQuery( '#members-new-cap-field' ).val( '' );

				// Disable the add new cap button.
				jQuery( '#members-add-new-cap' ).prop( 'disabled', true );

				// Trigger a change on our new grant cap checkbox.
				jQuery( '.members-cap-checklist input[data-grant-cap="' + new_cap + '"]' ).trigger( 'change' );
			}
		}
	);

} ); // ready()
