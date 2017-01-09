jQuery(function($){
    var API = igdb_sc_data.api;
    var adminajax = igdb_sc_data.admin_ajax;
    var ajaxnonce = igdb_sc_data.addGameNonce;
	tinymce.create('tinymce.plugins.IGDB_Shortcode', {
		init: function(ed, url) {
			ed.addButton('IGDB_Shortcode', {
				title: 'Add Game Info',
				image: url+'/../img/igdb_icon.png',
				cmd: 'IGDB_Shortcode_cmd'				
			});
 
			ed.addCommand('IGDB_Shortcode_cmd', function() {
				ed.windowManager.open(
					//	Window Properties
					{
						file: url + '/../admin/IGDB-shortcode-dialog.html',
						title: 'IGDB Shortcode',
						width: 600,
						height: 500,
						inline: 1
					},
					//	Windows Parameters/Arguments
					{
						giapi: API,
						admin_ajax: adminajax, 
						ajax_nonce: ajaxnonce,
						editor: ed,
						jquery: $ // PASS JQUERY
					}
				);
			});
		}
	});
	tinymce.PluginManager.add('IGDB_Shortcode', tinymce.plugins.IGDB_Shortcode);

});