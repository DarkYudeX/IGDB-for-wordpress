jQuery(function($){ 
		
	var gameidlist = [];
	
	if(igdb_data.array !== ""){
	var gameidliststr = igdb_data.array;
	gameidlist = gameidliststr.split(',');
	}
	
	
	$( "#gamelist" ).sortable({
		update: function() {
			gameidlist = $(this).sortable('toArray', {attribute: 'gameid'});
			$("#hiddengamelist").attr("value",gameidlist.toString());
		}
	});
	
	$( "#gamelist" ).on( "click", ".game-list-close", function() {
		var removeItem = $(this).parent('.game-list').attr('gameid');
		gameidlist = jQuery.grep(gameidlist, function(value) {
		  return value != removeItem;
		});
		$(this).parent('.game-list').remove();
		$("#hiddengamelist").attr("value",gameidlist.toString());
		
	});

	$("#autocomplete").autocomplete({

		source: function (request, response) {
				
			$.ajax({
				url: 'https://igdbcom-internet-game-database-v1.p.mashape.com/games/?fields=name,url,summary,popularity,aggregated_rating,developers,publishers,release_dates,cover,esrb,pegi&limit=10&offset=0&search='+request.term,
				type: 'GET', // The HTTP Method
				beforeSend: function(xhr) {
				xhr.setRequestHeader("X-Mashape-Key", igdb_data.api); // Enter here your Mashape key
				},
				datatype: 'json',
				success: function(data) {
					response(data);
				}
			});
		
		},
		minLength: 2,
		select: function( event, ui ) {
			
			$.ajax({
				type : "post",
				url : igdb_data.admin_ajax,
				data : {action: "igdb_add_game", game : ui.item, addGameNonce : igdb_data.addGameNonce},
				success: function(response) {
					$("#gamelist").append("<li class='game-list ui-sortable-handle' gameid='"+ response +"'>"+"<div class='game-list-name'>"+ ui.item.name +"</div><img src='"+igdb_data.close_button_url+"'class='game-list-close'/></li>"); 
					gameidlist.push(response);
					$("#hiddengamelist").attr("value",gameidlist.toString());
				}
			 });  
			
			
			

		},
		open: function() {
			$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
		},
		close: function() {
			$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
		}
	}).autocomplete( "instance" )._renderItem = function( ul, item ) {
		return $( "<li class='game-autocomplete'>" )
		  .append("<div class='game-list-item'><div class='game-list-name'>"+ item.name +"</br></div></div>" )
		  .appendTo( ul );
	};


});