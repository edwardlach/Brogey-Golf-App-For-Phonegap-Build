


/* Function for push menu */

$('#menu_button').on( 'click', function() {
	if ( $('#brogey_menu').css( 'left' ) == '-10px' ) {

		$('#brogey_menu').css( 'left', '-300px' );
		$('#wrapper').css( 'left', '0' );
		
	}
	else {

		$('#brogey_menu').css( 'left', '-10px' );
		$('#wrapper').css( 'left', '250px' );
		
	}
});
	
	
	

	

