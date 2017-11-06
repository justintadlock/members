jQuery( document ).ready( function() {

	/* ====== Delete Role Link (on Roles and Edit Role screens) ====== */

	// When the delete role link is clicked, give a "AYS?" popup to confirm.
	jQuery( '.members-delete-role-link' ).click(
		function() {
			return window.confirm( members_i18n.ays_delete_role );
		}
	);

	/* ====== Role Name and Slug ====== */

	/**
	 * Takes the given text and copies it to the role slug `<span>` after sanitizing it
	 * as a role.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $slug
	 * @return void
	 */
	function members_print_role_slug( slug ) {

		// Sanitize the role.
		slug = slug.toLowerCase().trim().replace( /<.*?>/g, '' ).replace( /\s/g, '_' ).replace( /[^a-zA-Z0-9_]/g, '' );

		// Add the text.
		jQuery( '.role-slug' ).text( slug );
	}

	// Check the role name input box for key presses.
	jQuery( 'input[name="role_name"]' ).keyup(
		function() {

			// If there's no value stored in the role input box, print this input's
			// value in the role slug span.
			if ( ! jQuery( 'input[name="role"]' ).val() )
				members_print_role_slug( this.value );
		}
	); // .keyup

	// Hide the role input box and role OK button.
	jQuery( 'input[name="role"], .role-ok-button' ).hide();

	// When the role edit button is clicked.
	jQuery( document ).on( 'click', '.role-edit-button.closed',
		function() {

			// Toggle the button class and change the text.
			jQuery( this ).removeClass( 'closed' ).addClass( 'open' ).text( members_i18n.button_role_ok );

			// Show role input.
			jQuery( 'input[name="role"]' ).show();

			// Focus on the role input.
			jQuery( 'input[name="role"]' ).trigger( 'focus' );

			// Copy the role slug to the role input edit value.
			jQuery( 'input[name="role"]' ).attr( 'value', jQuery( '.role-slug' ).text() );
		}
	);

	// When the role OK button is pressed.
	jQuery( document ).on( 'click', '.role-edit-button.open',
		function() {

			// Toggle the button class and change the text.
			jQuery( this ).removeClass( 'open' ).addClass( 'closed' ).text( members_i18n.button_role_edit );

			// Hide role input.
			jQuery( 'input[name="role"]' ).hide();

			// Get the role input value.
			var role = jQuery( 'input[name="role"]' ).val();

			// If we have a value, print the slug.
			if ( role )
				members_print_role_slug( role );

			// Else, use the role name input value.
			else
				members_print_role_slug( jQuery( 'input[name="role_name"]' ).val() );
		}
	); // .click()

	// Simulate clicking the OK button if the user presses "Enter" in the role field.
	jQuery( 'input[name="role"]' ).keypress(
		function( e ) {

			// 13 is the key code for "Enter".
			if ( 13 === e.keyCode ) {

				// Click the edit role button and trigger a focus.
				jQuery( '.role-edit-button' ).click().trigger( 'focus' );

				// Prevent default behavior and return false.
				e.preventDefault();
				return false;
			}
		}
	); // .keypress()

	// Hide the add new role button if we don't at least have a role name.
	if ( ! jQuery( '.users_page_role-new input[name="role_name"]' ).val() )
		jQuery( '.users_page_role-new #publish' ).prop( 'disabled', true );

	// Look for changes to the role name input.
	jQuery( '.users_page_role-new input[name="role_name"]' ).on( 'input',
		function() {

			// If there's a role name, enable the add new role button.
			if ( jQuery( this ).val() )
				jQuery( '.users_page_role-new #publish' ).prop( 'disabled', false );

			// Else, disable the button.
			else
				jQuery( '.users_page_role-new #publish' ).prop( 'disabled', true );
		}
	);

	/* ====== Tab Sections and Controls ====== */

	// Create Underscore templates.
	var section_template = wp.template( 'members-cap-section' );
	var control_template = wp.template( 'members-cap-control' );

	// Check that the `members_sections` and `members_controls` variables were
	// passed in via `wp_localize_script()`.
	if ( typeof members_sections !== 'undefined' && typeof members_controls !== 'undefined' ) {

		// Loop through the sections and append the template for each.
		_.each( members_sections, function( data ) {
			jQuery( '.members-tab-wrap' ).append( section_template( data ) );
		} );

		// Loop through the controls and append the template for each.
		_.each( members_controls, function( data ) {
			jQuery( '#members-tab-' + data.section + ' tbody' ).append( control_template( data ) );
		} );
	}

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

	// When a cap button is clicked. Note that we're using `.on()` here because we're dealing
	// with dynamically-generated HTML.
	//
	// Note that we only need to trigger `change()` once for our functionality.
	jQuery( document ).on( 'click', '.editable-role .members-cap-checklist button',
		function() {

			// Get the button parent element.
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

	// Remove focus from button when hovering another button.
	jQuery( document ).on( 'hover', '.editable-role .members-cap-checklist button',
		function() {
			jQuery( '.members-cap-checklist button:focus' ).not( this ).blur();
		}
	);

	/* ====== Meta Boxes ====== */

	// Add the postbox toggle functionality.
	// Note: `pagenow` is a global variable set by WordPress.
	postboxes.add_postbox_toggles( pagenow );

	/* ====== New Cap Meta Box ====== */

	// Give the meta box toggle button a type of `button` so that it doesn't submit the form
	// when we hit the "Enter" key in our input or toggle open/close the meta box.
	jQuery( '#newcapdiv button.handlediv' ).attr( 'type', 'button' );

	// Disable the new cap button so that it's not clicked until there's a cap.
	jQuery( '#members-add-new-cap' ).prop( 'disabled', true );

	// When the user starts typing a new cap.
	jQuery( '#members-new-cap-field' ).on( 'input',
		function() {

			// If there's a value in the input, enable the add new button.
			//if ( 'do_not_allow' !== jQuery( this ).val() ) {
			if ( -1 === jQuery.inArray( jQuery( this ).val(), members_i18n.hidden_caps ) ) {

				jQuery( '#members-add-new-cap' ).prop( 'disabled', false );

			// If there's no value, disable the button.
			} else {
				jQuery( '#members-add-new-cap' ).prop( 'disabled', true );
			}
		}
	); // .on( 'input' )

	// Simulate clicking the add new cap button if the user presses "Enter" in the new cap field.
	jQuery( '#members-new-cap-field' ).keypress(
		function( e ) {

			// 13 is the key code for "Enter".
			if ( 13 === e.keyCode ) {
				jQuery( '#members-add-new-cap' ).click();
				e.preventDefault();
				return false;
			}
		}
	); // .keypress()

	// When the new cap button is clicked.
	jQuery( '#members-add-new-cap' ).click(
		function() {

			// Get the new cap value.
			var new_cap = jQuery( '#members-new-cap-field' ).val();

			// Sanitize the new cap.
			// Note that this will be sanitized on the PHP side as well before save.
			new_cap = new_cap.trim().replace( /<.*?>/g, '' ).replace( /\s/g, '_' ).replace( /[^a-zA-Z0-9_]/g, '' );

			// If there's a new cap value.
			if ( new_cap ) {

				// Don't allow the 'do_not_allow' cap.
				//if ( 'do_not_allow' === new_cap ) {
				if ( -1 !== jQuery.inArray( jQuery( this ).val(), members_i18n.hidden_caps ) ) {
					return;
				}

				// Trigger a click event on the "custom" tab in the edit caps box.
				jQuery( 'a[href="#members-tab-custom"]' ).trigger( 'click' );

				// Replace text placeholder with cap.
				members_i18n.label_grant_cap = members_i18n.label_grant_cap.replace( /%s/g, '<code>' + new_cap + '</code>' );
				members_i18n.label_deny_cap  = members_i18n.label_deny_cap.replace( /%s/g,  '<code>' + new_cap + '</code>' );

				// Set up some data to pass to our Underscore template.
				var data = {
					cap            : new_cap,
					readonly       : '',
					name           : { grant : 'grant-new-caps[]', deny : 'deny-new-caps[]' },
					is_granted_cap : true,
					is_denied_cap  : false,
					label          : { cap : new_cap, grant : members_i18n.label_grant_cap, deny : members_i18n.label_deny_cap }
				};

				// Prepend our template to the "custom" edit caps tab content.
				jQuery( '#members-tab-custom tbody' ).prepend( control_template( data ) );

				// Get the new cap table row.
				var parent = jQuery( '[data-grant-cap="' + new_cap + '"]' ).parents( '.members-cap-checklist' );

				// Add the highlight class.
				jQuery( parent ).addClass( 'members-highlight' );

				// Remove the class after a set time for a highlight effect.
				setTimeout( function() {
					jQuery( parent ).removeClass( 'members-highlight' );
				}, 500 );

				// Set the new cap input value to an empty string.
				jQuery( '#members-new-cap-field' ).val( '' );

				// Disable the add new cap button.
				jQuery( '#members-add-new-cap' ).prop( 'disabled', true );

				// Trigger a change on our new grant cap checkbox.
				jQuery( '.members-cap-checklist input[data-grant-cap="' + new_cap + '"]' ).trigger( 'change' );
			}
		}
	); // .click()

} ); // ready()
