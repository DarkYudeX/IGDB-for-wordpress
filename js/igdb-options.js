jQuery(function($){ 
    String.prototype.stripSlashes = function(){
        return this.replace(/\\(.)/mg, "$1");
    }
    

    $( "#tabs" ).tabs();
    
    function loadGames(){
        $(".game-list").empty();
        $.ajax({
    		type : "post",
    		url : igdb_data.admin_ajax,
    		data : {action: "igdb_get_game_list"},
    		success: function(response) {
    		    var data = JSON.parse(response);
    		    $.each( data, function( key, value ) {
                    $(".game-list").append("<tr><td>"+ value.name.stripSlashes() +"</td><td><a class='refresh-game'>Refresh</a>&nbsp;|&nbsp;<a class='delete-game'>Delete</a></td><input type='hidden' class='game-id' value='"+ value.id +"'><input type='hidden' class='igdb-id' value='"+ value.igdb_id +"'></tr>");
                });
    		}
        });  
 
    }
    
    $(document).on('click', '.refresh-game', function(){ 
        var gameid = $(this).parent().parent().children('.game-id').val();
        var igdbid = $(this).parent().parent().children('.igdb-id').val();
        
        $.ajax({
			url: 'https://igdbcom-internet-game-database-v1.p.mashape.com/games/'+ igdbid +'/?fields=name,url,summary,popularity,aggregated_rating,developers,publishers,release_dates,cover,esrb,pegi',
			type: 'GET', // The HTTP Method
			beforeSend: function(xhr) {
			xhr.setRequestHeader("X-Mashape-Key", igdb_data.api); // Enter here your Mashape key
			},
			datatype: 'json',
			success: function(data) {
				$.ajax({
    				type : "post",
    				url : igdb_data.admin_ajax,
    				data : {action: "igdb_update_game", igdb_game : data[0], game_id: gameid, addGameNonce : igdb_data.updateGamesNonce},
    				success: function(response) {
    				    alert(response);
    				    loadGames();
    				}
                });  
			}
		});
    });
    
    
    $( ".refresh-click" ).click(function(e) {
        e.stopImmediatePropagation();
		loadGames();
    });
    
    

    
    
    
});