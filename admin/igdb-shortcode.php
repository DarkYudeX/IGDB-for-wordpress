<?php
	function GISCfuction($atts, $content = null) {
		global $wpdb;
		global $games;

		wp_enqueue_style( 'IGDB-shortcode-style' );
		ob_start();
		$gametablename = $wpdb->prefix.'igdb_game';
		$games = $wpdb->get_row("SELECT * FROM $gametablename WHERE id = '".$content."'",ARRAY_A);

		if(isset($games)){

			?>
			<div class="IGDB_outerbox GISC_align">
				<?php include('igdb-shortcode-fragment.php'); ?>
			</div>
			<?php
		}

		return ob_get_clean();
	}
	function IGDB_cover(){
		global $games;
		echo '<img class="IGDB_gameimage" src="https://images.igdb.com/igdb/image/upload/t_cover_big/'. $games['cover'] .'.jpg"/>' ;
		
	}
	function IGDB_title(){
		global $games;
		echo '<a href="'.$games['url'].'" >'. stripslashes ( $games['name'] ).'</a>';
	}
	function IGDB_publishers(){
		global $games;
		global $wpdb;
		$publishers = $wpdb->get_results( "
                                            SELECT wp_igdb_company.name
                                            FROM wp_igdb_company
                                            INNER JOIN wp_igdb_game_company
                                            ON wp_igdb_company.id = wp_igdb_game_company.company
                                            WHERE wp_igdb_game_company.game = ". $games["id"] ." AND wp_igdb_game_company.companytype = 1 ;" , OBJECT);
        $output = array();
        foreach($publishers as $publisher){
            echo '<div class="IGDB_item">'.$publisher->name.'</div>';
        }
	}
	
	function IGDB_developers(){
		global $games;
		global $wpdb;
		$developers = $wpdb->get_results( "
                                            SELECT wp_igdb_company.name
                                            FROM wp_igdb_company
                                            INNER JOIN wp_igdb_game_company
                                            ON wp_igdb_company.id = wp_igdb_game_company.company
                                            WHERE wp_igdb_game_company.game = ". $games["id"] ." AND wp_igdb_game_company.companytype = 2 ;" , OBJECT);
        $output = array();
        foreach($developers as $developer){
            echo '<div class="IGDB_item">'.$developer->name.'</div>';
        }

	}
	
	function IGDB_platforms(){
		global $games;
		global $wpdb;
		$gamereleases = $wpdb->get_results( "
                                            SELECT wp_igdb_platform.name, wp_igdb_releases.release_date
                                            FROM wp_igdb_platform
                                            INNER JOIN wp_igdb_releases
                                            ON wp_igdb_platform.id = wp_igdb_releases.platform
                                            WHERE wp_igdb_releases.game = ". $games["id"] .";" , OBJECT);
        $output = array();
        foreach($gamereleases as $platform){
            if(!in_array($platform->name,$output )){
                 echo '<div class="IGDB_item">'.$platform->name.'</div>';
            }
           
        }
	}
	
	function IGDB_releasedate(){
		global $games;
		global $wpdb;
		$gamereleases = $wpdb->get_results( "
                                            SELECT wp_igdb_platform.name, wp_igdb_releases.release_date
                                            FROM wp_igdb_platform
                                            INNER JOIN wp_igdb_releases
                                            ON wp_igdb_platform.id = wp_igdb_releases.platform
                                            WHERE wp_igdb_releases.game = ". $games["id"] .";" , OBJECT);
        echo isset($gamereleases[0]) ? date("F j, Y", strtotime($gamereleases[0]->release_date)) : "-";
	}
	add_shortcode( 'game', 'GISCfuction' );


	
	
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