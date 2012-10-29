var Capsule = {

    init: function () {

    	$(function() {
		    $("#listElements").sortable({

		      revert: true
		    });
		    $("ul, li").disableSelection();
		  });

    	if($('#loginForm').length) {
    		$('body').addClass('index');
    	}
    	else {
    		$('body').removeClass('index').addClass('index2');
    	}

    	$('#cancelList').click(function() {
    		alert('fixa denna bild och så att listan försvinner om man trycker här');
    	});

		$('#loginMenu').live('click', function () {
			$('#loginForm').toggle();
     	});

     	if($('#notLoggedIn').length) {
     		$('#notLoggedIn').effect("bounce", {
     			times: 4,
     			direction: 'right'
     		}, 2000, function() {
    			$('#notLoggedIn').fadeOut("slow");
  			});
     		$('#loginForm').show();
     	}

    	// Lägg till listobjekt
    	var i = 0;

    	$('#addListObjectSubmit').click(function (e) {

    		var listObjectName = $('#listObjectName').val();

    		$('#listObjectName').val('');

    		if((listObjectName == null) || (listObjectName == '')) {
    			return false;
    		}
    		else {
    			var listObjectDesc = $('#m_newListObjectDesc').val();
    			$('#m_newListObjectDesc').val('');

    			var listDiv = 'listDiv' + i;

    			$('<div>', {
    				class: 'listObjectShow',
    				id: listDiv
    			}).appendTo('#listOfListObjects');

    			$('<span>', {
    				class: 'listObjectShowText',
    				id: 'listObjectName' + i,
    				text: listObjectName
    			}).appendTo('#' + listDiv);

    			$('<br>', {
    				class: 'listObjectShowDesc',
    				id: 'listObjectDesc' + i,
    				text: listObjectDesc
    			}).appendTo('#' + listDiv);

    			$('<span>', {
    				class: 'listObjectShowDesc',
    				id: 'listObjectDesc' + i,
    				text: listObjectDesc
    			}).appendTo('#' + listDiv);

    			$('<span>', {
    				href: '#',
    				class: 'listObjectRemove',
    				id: 'removeListObject' + i,
    				text: 'X'
    			}).appendTo('#' + listDiv);

    			$('<input>', {
    				type: 'hidden',
    				class: 'removeListObject' + i,
    				name: 'newListObject[]',
    				id: 'listObjectName' + i,
    				value: listObjectName
    			}).appendTo('#listOfListObjects');

    			$('<input>', {
    				type: 'hidden',
    				class: 'removeListObject' + i,
    				name: 'newListObjectDesc[]',
    				id: 'listObjectDesc' + i,
    				value: listObjectDesc
    			}).appendTo('#listOfListObjects');
    		}

    		i += 1;

    		return false;
    		e.preventdefault();
    	});

		$('#submit').live('click', function(){
			e.preventdefault();
		});

		$('.listObjectRemove').live('click',function(){
			var id = $(this).attr('id');
			var output = id.substring(16);
			var inputToRemove = '.' + 'removeListObject' + (output);

			var listToRemoveId = 'listDiv' + output;
			$('#' + listToRemoveId).remove();

			//$("[id^=jander]");98ı
                  
			$(inputToRemove).remove();
		});



		$('.default').each(function(){
	    	var defaultVal = $(this).attr('title');
	    	$(this).focus(function(){
	      		if ($(this).val() == defaultVal){
	        		$(this).removeClass('active').val('');
	      		}
	    	})
	    	.blur(function(){
	      		if ($(this).val() == ''){
	        		$(this).addClass('active').val(defaultVal);
	      		}
	    	})
	    	.blur().addClass('active');
	  	});
	  	$('form').submit(function(){
  			$('.default').each(function(){
    			var defaultVal = $(this).attr('title');
    			if ($(this).val() == defaultVal){
      				$(this).val('');
    			}
  			});
		});





		$('#listElements').sortable({
			opacity: 0.5,
			axis: 'y',
			revert: true,
			update: function(event, ui) {

				var order = '';
				var i = 0;
		        $('.notSorted').each(function (e) {
		        	if (i != 0) {
		        		order += '.';
		        	}
		            order += $(this).attr('id');//.push($(this).attr('id'));
		            i +=1;
		        });

		        var url = $('#newOrder').attr("url");

				url += "&listOrder=" + order;

		        $('#newOrder').attr('href',url);
			}
        });
        $("ul, li, #loginMenu").disableSelection();
   	
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



