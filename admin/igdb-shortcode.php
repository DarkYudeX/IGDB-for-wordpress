<?php



	function register_button( $buttons ) {
	   array_push( $buttons, "|", "IGDB_Shortcode" );
	   return $buttons;
	}
	function add_plugin( $plugin_array ) {
	   $plugin_array['IGDB_Shortcode'] =  plugins_url() .'/igdb-wordpress/js/igdb-shortcode.js';
	   return $plugin_array;
	}
	function IGDB_Shortcode_button() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );
		wp_enqueue_style( 'IGDBSC', plugins_url() .'/igdb-wordpress/css/igdb-shortcode.css' );
		wp_enqueue_script( 'IGDBSCplaceholder', plugins_url() . '/igdb-wordpress/js/placeholder.js', array(), '1.0.0', true );
		$script_data = array(
			'api' => get_option('igdb_API'),
			'admin_ajax' => admin_url( 'admin-ajax.php' ),
			'addGameNonce' => wp_create_nonce( 'myajax-add-game-nonce' ),
		);
		wp_localize_script(
			'IGDBSCplaceholder', 
			'igdb_sc_data',
			$script_data
		);
	   if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {
		  return;
	   }

	   if ( get_user_option('rich_editing') == 'true' ) {
		  add_filter( 'mce_external_plugins','add_plugin' );
		  add_filter( 'mce_buttons', 'register_button' );
	   }

	}
	add_action('init','IGDB_Shortcode_button');
?>