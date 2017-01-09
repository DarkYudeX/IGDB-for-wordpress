jQuery(function($){
    $('.game').hover(
        function() {
            $(this).find('div').show();
        },
        function(){
           $(this).find('div').hide();
        }
    );
    
    $(document).on('click', '.searchbutton', function(e){ 
        
        if($( "#igdb_sort_game option:selected" ).val() == 'created_at DESC'){
            window.location.href = igdb_data.admin_url +  "&search=" + $("#igdb_search_game").val();
        }else{
            window.location.href = igdb_data.admin_url +  "&search=" +$("#igdb_search_game").val() + "&sort="+ $( "#igdb_sort_game option:selected" ).val();
        }
        
        
        
         
    });
    
    $(document).on('click', '.sortbutton', function(e){ 
        if($.trim($('#igdb_search_game').val()) == ''){
            window.location.href = igdb_data.admin_url + "&sort=" + $( "#igdb_sort_game option:selected" ).val();
        }else{
            window.location.href = igdb_data.admin_url +  "&search=" +$("#igdb_search_game").val() + "&sort="+ $( "#igdb_sort_game option:selected" ).val();
        }
        
        
        
        
         
    });
    
    $(document).on('click', '.refresh-game', function(e){ 
        e.preventDefault();
        
        var gameid = $(this).parent().parent().parent().children('.game-id').val();
        var igdbid = $(this).parent().parent().parent().children('.igdb-id').val();
        
        $.ajax({
			url: 'https://igdbcom-internet-game-database-v1.p.mashape.com/games/'+ igdbid +'/?fields=*',
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
    				}
                });  
			}
		});
    });
    
    $(document).on('click', '.delete-game', function(e){ 
        e.preventDefault();
        
        if (confirm('Are you sure you want to delete this game? (Cannot be undone)')) {
            var gameid = $(this).parent().parent().parent().children('.game-id').val();

            $.ajax({
    			type : "post",
    			url : igdb_data.admin_ajax,
    			data : {action: "igdb_delete_game", game_id: gameid, deleteGameNonce : igdb_data.deleteGamesNonce},
    			success: function(response) {
    			    alert(response);
    			}
            }); 
        }
        
        
    });
    
    

});