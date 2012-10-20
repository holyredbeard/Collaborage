$(document).ready(function() {

	// Funktion som körs om användaren klickar på submit-knappen för formuläret för att ta bort användare.
	// Visar en confirm box för varje användare som användaren måste godkänna för att användaren ska tas bort.
	$('#form3').submit(function() {
	    var submit = true;
	    $('input[type=checkbox]').each(function () {
	        if( this.checked ) {
	            var username = $(this).attr('user');

	            var confirmBox = confirm('Do you really want to remove the user ' + username + ' ?');

	            // Om användaren klickar på Avbryt tas checken bort på användaren och denne tas därmed inte bort.
				if (!confirmBox) {
					$(this).attr('checked', false);
				}
	        }
	    });
	    return submit;
	});

});