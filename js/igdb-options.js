jQuery(function($){ 
    String.prototype.stripSlashes = function(){
        return this.replace(/\\(.)/mg, "$1");
    }
    

    $( "#tabs" ).tabs();
    
    
    
    $( ".refresh-click" ).click(function(e) {
        e.stopImmediatePropagation();
		loadGames();
    });
    
    

    
    
    
});