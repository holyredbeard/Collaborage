var Capsule = {

    init: function () {

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
            if (($('#listObjectName').val()) != 'List object name') {

                var listObjectName = $('#listObjectName').val();

                if((listObjectName == null) || (listObjectName == '')) {
                    return false;
                }
                else {
                    var listObjectDesc = $('#newListObjectDesc').val();
                    $('#newListObjectDesc').val('');

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

                    $('<img>', {
                        href: '#',
                        src: 'http://cdn1.iconfinder.com/data/icons/cc_mono_icon_set/blacks/16x16/delete.png',
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

                $('#listObjectName').val('List object name');
                $('#newListObjectDesc').val('List object description');

                i += 1;

            }
            else {
                if ($('.addListObjectName').length == 0){
                    $('<span>', {
                        class: 'errorMessage addListObjectName',
                        text: 'You need to give the list object a name!'
                    }).appendTo('#errorMessages');        

                   $('<br>').appendTo('#errorMessages');                 
                }
            }

    		return false;
    		e.preventdefault();
    	});

        // error messages
        // 

		$('#submit').live('click', function(e){

            var validation = true;

            /*if ($('#listName').val() == 'List name') {

                if ($('.addListName').length == 0){
                    $('<span>', {
                        class: 'errorMessage addListName',
                        text: 'You need to provide a name for the list!'
                    }).appendTo('#errorMessages');        

                   $('<br>').appendTo('#errorMessages');
                }
                validation = false;
            }

            if($('.listObjectShow').length < 2) {

                if ($('.addObjects').length == 0){
                    $('<span>', {
                        class: 'errorMessage addObjects',
                        text: 'You need to add at least two list objects!'
                    }).appendTo('#errorMessages');

                    $('<br>').appendTo('#errorMessages');
                }
                validation = false;
            }

            if ($('input[name^="m_newListUser[]"]:checked').length < 2) {

                if ($('.addUsers').length == 0){
                    $('<span>', {
                        class: 'errorMessage addUsers',
                        text: 'You need to choose at least two users!'
                    }).appendTo('#errorMessages');        

                   $('<br>').appendTo('#errorMessages');
                }
                validation = false;
            }

            if (validation == false) {
                return false;
            }*/

			e.preventdefault();
		});

        $('#listObjectName').focus(function() {
            $('#errorMessages').text('');
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
	    	var defaultVal = $(this).attr('defaultValue');
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
    			var defaultVal = $(this).attr('defaultValue');
    			if ($(this).val() == defaultVal){
      				$(this).val('');
    			}
  			});
		});

		$('.tooltip').tipsy({trigger: 'focus', gravity: 'w'});

        if($('.fail').length) {
            $('.fail').effect("bounce", { times:8 }, 2200, function() {
                $(this).hide();
            });
        }

        if($('.success').length) {
            $('.success').effect("bounce", { times:8 }, 2200, function() {
                $(this).hide();
            });
        }
		

        ('slow', function() {
    // Animation complete.
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

                $('#newOrder').show();

		        var url = $('#newOrder').attr("url");

				url += "&listOrder=" + order;

		        $('#newOrder').attr('href',url);
			}
        });
        $("ul, li, #loginMenu").disableSelection();

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
	}
}

window.onload = Capsule.init;



