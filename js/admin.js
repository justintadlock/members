(function($){

	$(document).ready( function() {

		$( '.hide-if-no-js' ).show();

		$( '#members-add-new-cap' ).click( function() {
			$( 'p.new-cap-holder' ).append( '<input type="text" class="new-cap" name="new-cap[]" value="" size="20" />' );
		});

		$( 'div.members-role-checkbox input[type="checkbox"]' ).click( function() {
			if ( $( this ).prop( 'checked' ) ) {
				$( this ).next( 'label' ).addClass( 'has-cap' );
			} else {
				$( this ).next( 'label' ).removeClass( 'has-cap' );
			}
		});

	});

})(jQuery);