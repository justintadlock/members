jQuery( document ).ready( function() {
	var wp_inline_edit  = inlineEditPost.edit;
	
	inlineEditPost.edit = function( id ) {
		
		wp_inline_edit.apply( this, arguments );
		
		var post_id = 0;
		
		if ( typeof( id ) == "object" ) {
			post_id = parseInt( this.getId( id ) );
		}

		if ( post_id > 0 ) {
			// define the edit row
			var edit_row 						= jQuery( "#edit-" + post_id );
			var post_row 						= jQuery( "#post-" + post_id );
			var content_permissions = jQuery( '.column-content_permissions_column input:hidden', post_row ).map( function(){ return jQuery( this ).val(); } ).get();
			
			jQuery.each( content_permissions, function( key, value ){ jQuery( ':input[name^=' + value + ']', edit_row ).filter('[value="' + value + '"]').prop('checked', true); } );
		}
	};
});
