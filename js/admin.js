$j = jQuery.noConflict();

$j(document).ready(
	function() {


		if ( false === $j( '[name="members_settings[content_permissions]"]' ).prop( 'checked' ) ) {

			$j( '[name="members_settings[content_permissions]"]' ).parents( 'tr' ).next( 'tr' ).hide();
		}

		if ( false === $j( '[name="members_settings[private_blog]"]' ).prop( 'checked' ) ) {

			$j( '[name="members_settings[private_blog]"]' ).parents( 'tr' ).next( 'tr' ).hide();
		}

		if ( false === $j( '[name="members_settings[private_feed]"]' ).prop( 'checked' ) ) {

			$j( '[name="members_settings[private_feed]"]' ).parents( 'tr' ).next( 'tr' ).hide();
		}

		$j( '[name="members_settings[content_permissions]"], [name="members_settings[private_blog]"], [name="members_settings[private_feed]"]' ).on( 'change',
			function() {

				if ( $j( this ).prop( 'checked' ) ) {

					$j( this ).parents( 'tr' ).next( 'tr' ).show( 'slow' );
				} else {

					$j( this ).parents( 'tr' ).next( 'tr' ).hide( 'slow' );
				}
			}
		);


		$j( '.hide-if-no-js' ).show();

		$j( '#members-add-new-cap' ).click(
			function() {
				$j( 'p.new-cap-holder' ).append( '<input type="text" class="new-cap" name="new-cap[]" value="" size="20" />' );
			}
		);

		$j( 'div.members-role-checkbox input[type="checkbox"]' ).click(
			function() {
				if ( $j( this ).is( ':checked' ) ) {
					$j( this ).next( 'label' ).addClass( 'has-cap' );
				}
				else {
					$j( this ).next( 'label' ).removeClass( 'has-cap' );
				}
			}
		);
	}
);