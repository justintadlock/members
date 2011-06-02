$j = jQuery.noConflict();

$j(document).ready(
	function() {

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