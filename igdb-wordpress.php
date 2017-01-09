<?php 
    /*
    Plugin Name: IGDB.com for Wordpress
    Plugin URI: http://www.drzgamer.com
    Description: Display game information from IGDB.com(Internet Games Database) for your blog. 
    Author: Randy Grullon
    Version: 1.0
    Author URI: http://www.drzgamer.com
    */
?>
<?php 
	include( plugin_dir_path( __FILE__ ) . 'admin/igdb-shortcode.php');

    /**
    * Creates tables
    */
    register_activation_hook( __FILE__, 'igdb_create_db' );
    function igdb_create_db() {

        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $game_table_name = $wpdb->prefix . 'igdb_game';
        $company_table_name = $wpdb->prefix . 'igdb_company';
        $platform_table_name = $wpdb->prefix . 'igdb_platform';
        $game_company_table_name = $wpdb->prefix . 'igdb_game_company';
        $releases_table_name = $wpdb->prefix . 'igdb_releases';
		
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        
    
        $sql = "CREATE TABLE $game_table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            igdb_id bigint(8) NOT NULL,
            name varchar(100) NOT NULL,
            url varchar(100),
            summary longtext,
            popularity float(5,4),
            aggregated_rating float(4,2),
			esrb int(1),
			pegi int(1),
            cover varchar(100)
        ) $charset_collate;";
        dbDelta( $sql );
        
        $sql = "CREATE TABLE $company_table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            igdb_id bigint(8) NOT NULL,
            name varchar(100) NOT NULL
        ) $charset_collate;";
        dbDelta( $sql );
        
        $sql = "CREATE TABLE $platform_table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            igdb_id bigint(8) NOT NULL,
            name varchar(100) NOT NULL
        ) $charset_collate;";
        dbDelta( $sql );
        
        $sql = "CREATE TABLE $game_company_table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            game bigint(20) UNSIGNED NOT NULL,
            company bigint(20) UNSIGNED NOT NULL,
			companytype TINYINT(1) UNSIGNED NOT NULL
        ) $charset_collate;";
        dbDelta( $sql );
    
        $sql = "CREATE TABLE $releases_table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            game bigint(20)  UNSIGNED NOT NULL,
            platform bigint(20) UNSIGNED NOT NULL,
            release_date date,
			region int(2)
        ) $charset_collate;";
        dbDelta( $sql );
    }
	
	
	
	/**
    * Drops tables on uninstall
    */
	register_deactivation_hook( __FILE__, 'igdb_drop_db' );
	function igdb_drop_db() {
		
		global $wpdb;
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		$game_table_name = $wpdb->prefix . 'igdb_game';
        $company_table_name = $wpdb->prefix . 'igdb_company';
        $platform_table_name = $wpdb->prefix . 'igdb_platform';
        $game_company_table_name = $wpdb->prefix . 'igdb_game_company';
        $releases_table_name = $wpdb->prefix . 'igdb_releases';
		
		$sql = "DROP TABLE IF EXISTS $game_table_name,$company_table_name,$platform_table_name,$game_company_table_name,$releases_table_name ;";
		
		$wpdb->query($sql);
		
	}
	
    
    /**
    * Adds top level admin page.
    */
    add_action( 'admin_menu', 'my_admin_menu' );
    
    function my_admin_menu() {
        add_menu_page( 'WPIGDB', 'WPIGDB','edit_posts', 'igdballgames', 'igdb_all_games', plugin_dir_url( __FILE__ ) . 'img/igdb_icon.png', 6  );
		add_submenu_page('igdballgames', 'Games', 'All Games', 'edit_posts', 'igdballgames' );
        add_submenu_page( 'igdballgames', 'Settings', 'Settings', 'edit_posts', 'igdbsettings', 'igdb_admin_settings' );
		

    }
    
    function igdb_all_games(){
    	wp_enqueue_script( 'igdboptions', plugins_url() .'/igdb-wordpress/js/igdb-all-games.js', array(), '4.0.0', true );
		$script_data = array(
			'api' => get_option('igdb_API'),
			'close_button_url' => plugins_url().'/igdb-wordpress/img/xbutton.png',
			'admin_ajax' => admin_url( 'admin-ajax.php' ),
			'updateGamesNonce' => wp_create_nonce( 'myajax-update-games-nonce' ),
			'deleteGamesNonce' => wp_create_nonce( 'myajax-delete-games-nonce' ),
			'admin_url' => admin_url('admin.php?page=igdballgames'),
	
		);
		wp_localize_script(
			'igdboptions', // the script handle enqueued above
			'igdb_data',
			$script_data
		);
    	wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_style( 'jqueryuicss', plugins_url() .'/igdb-wordpress/css/jquery-ui.min.css' );
		
    	include( plugin_dir_path( __FILE__ ) . 'admin/igdb-all-games.php');	

    }
	    
    function igdb_admin_settings(){
		wp_enqueue_script( 'igdboptions', plugins_url() .'/igdb-wordpress/js/igdb-options.js', array(), '2.0.0', true );
		$script_data = array(
			'api' => get_option('igdb_API'),
			'close_button_url' => plugins_url().'/igdb-wordpress/img/xbutton.png',
			'admin_ajax' => admin_url( 'admin-ajax.php' ),
			'updateGamesNonce' => wp_create_nonce( 'myajax-update-games-nonce' ),
			
	
		);
		wp_localize_script(
			'igdboptions', // the script handle enqueued above
			'igdb_data',
			$script_data
		);
    	
    	
    	wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_style( 'jqueryuicss', plugins_url() .'/igdb-wordpress/css/jquery-ui.min.css' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		
		wp_enqueue_style( 'IGDBoptionscss', plugins_url() .'/igdb-wordpress/css/igdb-option.css' );

        
        include('igdb-wordpress-options.php');
    }
	
	/**
	 * Adds game via ajax
	 */
	 
    function igdb_get_item($tablename,$item){
		global $wpdb;
		$temp = $wpdb->get_row("SELECT * FROM $tablename WHERE igdb_id = '".$item."'", ARRAY_N);
		return $temp;
		
	}
	
	function igdb_add_game(){
		
		$result = $_REQUEST['game'];
		$result = $result[0];
		
		global $wpdb;
		$gametablename = $wpdb->prefix.'igdb_game';
		$datum = igdb_get_item($gametablename,$result["id"]);
		
		if($datum == null) {
			$queryarray = array();
			$queryarraytype = array();
				
			$queryarray['name'] = $result['name'];
			array_push ( $queryarraytype , "%s");
			
			$queryarray['igdb_id'] = $result['id'];
			array_push ( $queryarraytype , "%d");
			
			
			if("" != trim($result['url'])){
				$queryarray['url'] = $result['url'];
				array_push ( $queryarraytype , "%s");
			}
			
			if("" != trim($result['summary'])){
				$queryarray['summary'] = sanitize_text_field($result['summary']);
				array_push ( $queryarraytype , "%s");
			}
			
			if("" != trim($result['popularity'])){
				$queryarray['popularity'] = $result['popularity'];
				array_push ( $queryarraytype , "%f");
			}
			
			if("" != trim($result['aggregated_rating'])){
				$queryarray['aggregated_rating'] = $result['aggregated_rating'];
				array_push ( $queryarraytype , "%f");
			}
			
			if("" != trim($result['esrb']['rating'])){
				$queryarray['esrb'] = $result['esrb']['rating'];
				array_push ( $queryarraytype , "%d");
			}
			
			if("" != trim($result['pegi']['rating'])){
				$queryarray['pegi'] = $result['pegi']['rating'];
				array_push ( $queryarraytype , "%d");
			}
			
			
			if("" != trim($result['cover']['cloudinary_id'])){
				$queryarray['cover'] = $result['cover']['cloudinary_id'];
				array_push ( $queryarraytype , "%s");
			}
			
			date_default_timezone_set('America/New_York');
			$date = date('Y-m-d h:i:s', time());
			
			$queryarray['created_at'] = $date;
			array_push ( $queryarraytype , "%s");
			
			$queryarray['updated_at'] = $date;
			array_push ( $queryarraytype , "%s");
			
			$wpdb->insert( $gametablename, $queryarray, $queryarraytype );
			
			$gameid = $wpdb->insert_id;
			
			$companytable = $wpdb->prefix . 'igdb_company';
			$companycombine = $wpdb->prefix . 'igdb_game_company';
			$platformtable = $wpdb->prefix . 'igdb_platform';
			$releasedatetable = $wpdb->prefix . 'igdb_releases';
				
			if(array_key_exists('developers', $result)){
				foreach($result['developers'] as $developer){
					$curl = curl_init("https://igdbcom-internet-game-database-v1.p.mashape.com/companies/". $developer ."?fields=name");
					curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Mashape-Authorization: '.get_option('igdb_API')));
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
					$response = json_decode(curl_exec ($curl), true);
					
					$datum = igdb_get_item($companytable,$response[0]['id']);
					if($datum == null) {
						$newdata = array(
						    'igdb_id' =>$response[0]['id'],
							'name'=>$response[0]['name']
							
						);
						$wpdb->insert( $companytable , $newdata );
						$devtemp = $wpdb->insert_id;
						
						$wpdb->insert($companycombine,array('game' => $gameid, 'company'=>$devtemp, 'companytype' => 1));
						
						
					}else {
						$devtemp = $datum;
						
						$wpdb->insert($companycombine,array('game' => $gameid, 'company'=>$devtemp[0], 'companytype' => 1));
					}
					
				}
			}
			
			if(array_key_exists('publishers', $result)){
				foreach($result['publishers'] as $publisher){
					$curl = curl_init("https://igdbcom-internet-game-database-v1.p.mashape.com/companies/". $publisher ."?fields=name");
					curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Mashape-Authorization: '.get_option('igdb_API')));
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
					$response = json_decode(curl_exec ($curl), true);
					
					$datum = igdb_get_item($companytable,$response[0]['id']);
					if($datum == null) {
						$newdata = array(
						    'igdb_id' =>$response[0]['id'],
							'name'=>$response[0]['name']
						);
						$wpdb->insert( $companytable , $newdata );
						$pubtemp = $wpdb->insert_id;
						
						$wpdb->insert($companycombine,array('game' => $gameid, 'company'=>$pubtemp, 'companytype' => 2));
						
						
					}else {
						$pubtemp = $datum;
						
						$wpdb->insert($companycombine,array('game' => $gameid, 'company'=>$pubtemp[0], 'companytype' => 2));
					}
					
				}
			}
			
			
			if(array_key_exists('release_dates', $result)){
				foreach($result['release_dates'] as $release_date){
					$curl = curl_init("https://igdbcom-internet-game-database-v1.p.mashape.com/platforms/". $release_date['platform'] ."?fields=name");
					curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Mashape-Authorization: '.get_option('igdb_API')));
					curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
					$response = json_decode(curl_exec ($curl), true);
					$date = date('Y-m-d', $release_date['date']/1000);

					
					$datum = igdb_get_item($platformtable,$response[0]['id']);
					if($datum == null) {
						$newdata = array(
						    'igdb_id' =>$response[0]['id'],
							'name'=>$response[0]['name']
						);
						$wpdb->insert( $platformtable , $newdata );
						$plattemp = $wpdb->insert_id;
						$wpdb->insert($releasedatetable,array('game' => $gameid, 'platform'=>$plattemp, 'region' => $release_date['region'], 'release_date' => $date));
						
						
					}else {
						$plattemp = $datum;
						$wpdb->insert($releasedatetable,array('game' => $gameid, 'platform'=>$plattemp[0], 'region' => (array_key_exists('region',$release_date) ? $release_date['region'] : '8') , 'release_date' => $date));
					}
					
				}
			}
			
			
			echo $gameid;
		}else{
			echo $datum[0];
		}
		exit();
	}
	add_action('wp_ajax_igdb_add_game', 'igdb_add_game');
	
	/**
    * Ajax: return list of games
    */
    function igdb_get_game_list(){
    	global $wpdb;
    	$game_table_name = $wpdb->prefix . 'igdb_game';
    	$data = $wpdb->get_results("SELECT * FROM $game_table_name ORDER BY name ASC LIMIT 20");

    	echo json_encode($data);
    	exit();
    }
    add_action('wp_ajax_igdb_get_game_list', 'igdb_get_game_list');
    
    
    /**
    * Ajax: Update Game
    */
    function igdb_update_game(){
		$result = $_REQUEST['igdb_game'];
		
		
		global $wpdb;
		$gametablename = $wpdb->prefix.'igdb_game';
		$companytable = $wpdb->prefix . 'igdb_company';
		$companycombine = $wpdb->prefix . 'igdb_game_company';
		$platformtable = $wpdb->prefix . 'igdb_platform';
		$releasedatetable = $wpdb->prefix . 'igdb_releases';

		

		$queryarray = array();
		$queryarraytype = array();
			
		$queryarray['name'] = $result['name'];
		array_push ( $queryarraytype , "%s");
		
		if("" != trim($result['url'])){
			$queryarray['url'] = $result['url'];
			array_push ( $queryarraytype , "%s");
		}
		
		if("" != trim($result['summary'])){
			$queryarray['summary'] = sanitize_text_field($result['summary']);
			array_push ( $queryarraytype , "%s");
		}
		
		if("" != trim($result['popularity'])){
			$queryarray['popularity'] = $result['popularity'];
			array_push ( $queryarraytype , "%f");
		}
		
		if("" != trim($result['aggregated_rating'])){
			$queryarray['aggregated_rating'] = $result['aggregated_rating'];
			array_push ( $queryarraytype , "%f");
		}
		
		if("" != trim($result['esrb']['rating'])){
			$queryarray['esrb'] = $result['esrb']['rating'];
			array_push ( $queryarraytype , "%d");
		}
		
		if("" != trim($result['pegi']['rating'])){
			$queryarray['pegi'] = $result['pegi']['rating'];
			array_push ( $queryarraytype , "%d");
		}
		
		
		if("" != trim($result['cover']['cloudinary_id'])){
			$queryarray['cover'] = $result['cover']['cloudinary_id'];
			array_push ( $queryarraytype , "%s");
		}
		
		date_default_timezone_set('America/New_York');
		$date = date('Y-m-d h:i:s', time());
		
		
		$queryarray['updated_at'] = $date;
		array_push ( $queryarraytype , "%s");
		
		$gameid = $_POST['game_id'];
		
		$wpdb->delete($companycombine,array( 'game' => $gameid));
		$wpdb->delete($releasedatetable,array( 'game' => $gameid));

		$wpdb->update( $gametablename, $queryarray, array( 'id' => $gameid  ), $queryarraytype, array( '%d' )  );
		


		if(array_key_exists('developers', $result)){
			foreach($result['developers'] as $developer){
				$curl = curl_init("https://igdbcom-internet-game-database-v1.p.mashape.com/companies/". $developer ."?fields=name");
				curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Mashape-Authorization: '.get_option('igdb_API')));
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
				$response = json_decode(curl_exec ($curl), true);
				
				$datum = igdb_get_item($companytable,$response[0]['id']);
				if($datum == null) {
					$newdata = array(
					    'igdb_id' =>$response[0]['id'],
						'name'=>$response[0]['name']
						
					);
					$wpdb->insert( $companytable , $newdata );
					$devtemp = $wpdb->insert_id;
					
					$wpdb->insert($companycombine,array('game' => $gameid, 'company'=>$devtemp, 'companytype' => 1));
					
					
				}else {
					$devtemp = $datum;
					
					$wpdb->insert($companycombine,array('game' => $gameid, 'company'=>$devtemp[0], 'companytype' => 1));
				}
				
			}
		}
			
		if(array_key_exists('publishers', $result)){
			foreach($result['publishers'] as $publisher){
				$curl = curl_init("https://igdbcom-internet-game-database-v1.p.mashape.com/companies/". $publisher ."?fields=name");
				curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Mashape-Authorization: '.get_option('igdb_API')));
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
				$response = json_decode(curl_exec ($curl), true);
				
				$datum = igdb_get_item($companytable,$response[0]['id']);
				if($datum == null) {
					$newdata = array(
					    'igdb_id' =>$response[0]['id'],
						'name'=>$response[0]['name']
					);
					$wpdb->insert( $companytable , $newdata );
					$pubtemp = $wpdb->insert_id;
					
					$wpdb->insert($companycombine,array('game' => $gameid, 'company'=>$pubtemp, 'companytype' => 2));
					
					
				}else {
					$pubtemp = $datum;
					
					$wpdb->insert($companycombine,array('game' => $gameid, 'company'=>$pubtemp[0], 'companytype' => 2));
				}
				
			}
		}
			
			
		if(array_key_exists('release_dates', $result)){
			foreach($result['release_dates'] as $release_date){
				$curl = curl_init("https://igdbcom-internet-game-database-v1.p.mashape.com/platforms/". $release_date['platform'] ."?fields=name");
				curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Mashape-Authorization: '.get_option('igdb_API')));
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
				$response = json_decode(curl_exec ($curl), true);
				$date = date('Y-m-d', $release_date['date']/1000);

				
				$datum = igdb_get_item($platformtable,$response[0]['id']);
				if($datum == null) {
					$newdata = array(
					    'igdb_id' =>$response[0]['id'],
						'name'=>$response[0]['name']
					);
					$wpdb->insert( $platformtable , $newdata );
					$plattemp = $wpdb->insert_id;
					$wpdb->insert($releasedatetable,array('game' => $gameid, 'platform'=>$plattemp, 'region' => $release_date['region'], 'release_date' => $date));
					
					
				}else {
					$plattemp = $datum;
					$wpdb->insert($releasedatetable,array('game' => $gameid, 'platform'=>$plattemp[0], 'region' => (array_key_exists('region',$release_date) ? $release_date['region'] : '8') , 'release_date' => $date));
				}
				
			}
		}
			

    	echo "Sucessfully updated game.";

    	exit();
    }
    add_action('wp_ajax_igdb_update_game', 'igdb_update_game');
	
	
	/**
    * Ajax: Deletes a givin game
    */
	function igdb_delete_game(){
		
		check_ajax_referer( 'myajax-delete-games-nonce', 'deleteGameNonce' );
		
		global $wpdb;
		$gametablename = $wpdb->prefix.'igdb_game';
		$companycombine = $wpdb->prefix . 'igdb_game_company';
		$releasedatetable = $wpdb->prefix . 'igdb_releases';

		
		$gameid = $_POST['game_id'];
		
		$wpdb->delete($companycombine,array( 'game' => $gameid));
		$wpdb->delete($releasedatetable,array( 'game' => $gameid));
		$wpdb->delete($gametablename, array( 'id' => $gameid ));
		
		echo "Sucessfully deleted game.";

    	exit();
    }
    add_action('wp_ajax_igdb_delete_game', 'igdb_delete_game');





?>