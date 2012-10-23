var Capsule = {

    init: function () {

    	var LI_POSITION = 'li_position';

    	$(function() {
		    $("#listElements").sortable({

		      revert: true
		    });
		    $("ul, li").disableSelection();
		  });


		$('#listElements').sortable({
			//revert: true,
			update: function(event, ui) {

				var order = '';
				var i = 0;
		        $('.listObject li').each(function (e) {
		        	if (i != 0) {
		        		order += '.';
		        	}
		            order += $(this).attr('id');//.push($(this).attr('id'));
		            i +=1;
		        });

		        var url = $('#newOrder').attr("href");
				url += "&listOrder=" + order;

				alert(url);

		        $('#newOrder').attr('href',url);
			}
        });
        $("ul, li").disableSelection();

    	
    	$(function() {
        	$('#datepicker').datepicker({ dateFormat: 'yy-mm-dd' });
    	});

		$('input[type=text]').focus(function() {
			$(this).val('');
	    });

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

		$('#newListIsPublic').change(function () {

	    	Capsule.showUserList(true);
	 	});
		
	},
	
	showUserList: function(isPublic){

		if ($('#addUsers').is(":visible")) {
				$('#addUsers').hide();
		}
		else {
			$('#addUsers').show();
		}
	}
}

window.onload = Capsule.init;



