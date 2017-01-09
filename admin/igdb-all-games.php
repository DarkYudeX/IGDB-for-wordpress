<?php
    global $wpdb;
    
    
    
    
    $customPagHTML  = "";
    
    if(isset($_GET['search'])){
        $query          = "SELECT * FROM wp_igdb_game WHERE name LIKE '%". $_GET['search'] ."%'";
    }else{
        $query          = "SELECT * FROM wp_igdb_game";
    }
    
    $total_query    = "SELECT COUNT(1) FROM (${query}) AS combined_table";
    $total          = $wpdb->get_var( $total_query );
    $items_per_page = 10;
    $page           = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
    $offset         = ( $page * $items_per_page ) - $items_per_page;
    $result         = $wpdb->get_results( $query . " ORDER BY ". (isset($_GET['sort']) ? $_GET['sort'] : "created_at DESC") ." LIMIT ${offset}, ${items_per_page}" , OBJECT);
    $totalPage      = ceil($total / $items_per_page);
 
    if($totalPage > 1){
        $customPagHTML  =  '<div><span>Page '.$page.' of '.$totalPage.'</span>'.paginate_links( array(
            'base' => add_query_arg( 'cpage', '%#%' ),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => $totalPage,
            'current' => $page
        )).'</div>';
    }


 
//Now we'll display the list of records
?>

<div class="wrap">
    <?php
        if(!isset($_GET['search'])){
            echo "<h2> All Games </h2>";
        }else{
            echo "<h2>Search: ". $_GET['search'] ."</h2> <a href='". admin_url('admin.php?page=igdballgames') ."'><< return to all games</a>";
        }
    ?>
    
    <div class="tablenav">
        <div class="alignleft searchgame" style="width: 250px;">
            
            <input id="igdb_search_game" name="igdb_search_game" type="text" maxlength="255" placeholder="Ex. Super Mario World" value = "<?php echo isset($_GET['search']) ? $_GET['search'] : "" ?>"/>
            <button class="searchbutton" >Search</button>
        </div>
        
        <div class="alignleft sortgame" style="width: 300px;">
            <select id="igdb_sort_game" name="igdb_sort_game">
                <option value="created_at DESC" <?php echo (isset($_GET['sort']) && $_GET['sort'] == "created_at DESC" ? "selected" : "");?> >Date Created Descending</option>
                <option value="created_at ASC" <?php echo (isset($_GET['sort']) && $_GET['sort'] == "created_at ASC" ? "selected" : "");?> >Date Created Ascending</option>
                <option value="name DESC" <?php echo (isset($_GET['sort']) && $_GET['sort'] == "name DESC" ? "selected" : "");?> >Game Name Descending</option>
                <option value="name ASC" <?php echo (isset($_GET['sort']) && $_GET['sort'] == "name ASC" ? "selected" : "");?> >Game Name Ascending</option>
            </select>
            <button class="sortbutton" >Sort</button>
        </div>
        <div class='tablenav-pages'>
            <?php echo $customPagHTML; ?>
        </div>
    </div>
    
     
    <table class="widefat striped">
        <thead>
            <tr>
                <th>Cover Art</th>
                <th>Game ID</th>
                <th>Game Name</th>          
                <th>Publisher(s)</th>
                <th>Developer(s)</th>
                <th>Recent Release</th>
                <th>Platforms</th>
                <th>Last Updated</th>
            </tr>
        </thead>
        <tbody>
        
        <?php
            foreach($result as $game){
                $publishers = $wpdb->get_results( "
                                                    SELECT wp_igdb_company.name
                                                    FROM wp_igdb_company
                                                    INNER JOIN wp_igdb_game_company
                                                    ON wp_igdb_company.id = wp_igdb_game_company.company
                                                    WHERE wp_igdb_game_company.game = ". $game->id ." AND wp_igdb_game_company.companytype = 1 ;" , OBJECT);
                $output = array();
                foreach($publishers as $publisher){
                    $output[] = $publisher->name;
                }
                $publisherlist = join(', ', $output);
                
                $developers = $wpdb->get_results( "
                                                    SELECT wp_igdb_company.name
                                                    FROM wp_igdb_company
                                                    INNER JOIN wp_igdb_game_company
                                                    ON wp_igdb_company.id = wp_igdb_game_company.company
                                                    WHERE wp_igdb_game_company.game = ". $game->id ." AND wp_igdb_game_company.companytype = 2 ;" , OBJECT);
                $output = array();
                foreach($developers as $developer){
                    $output[] = $developer->name;
                }
                $developerslist = join(', ', $output);
                
                $gamereleases = $wpdb->get_results( "
                                                    SELECT wp_igdb_platform.name, wp_igdb_releases.release_date
                                                    FROM wp_igdb_platform
                                                    INNER JOIN wp_igdb_releases
                                                    ON wp_igdb_platform.id = wp_igdb_releases.platform
                                                    WHERE wp_igdb_releases.game = ". $game->id .";" , OBJECT);
                $output = array();
                foreach($gamereleases as $platform){
                    if(!in_array($platform->name,$output )){
                         $output[] = $platform->name;
                    }
                   
                }
                $platformlist = join(', ', $output);
            ?>
                <tr class="game">
                    <td><img src="https://images.igdb.com/igdb/image/upload/t_thumb/<?php echo $game->cover ?>.jpg" width="50"/></td>
                    <td><?php echo $game->id; ?></td>
                    <td>
                        <?php echo stripslashes($game->name) ?>
                        <div style="display: none;">
                            <a href="" class="refresh-game">Refresh</a>
                            -
                            <a href="" class="delete-game" style="color:red;" >Delete</a>
                        </div>
                    </td>
                    <td><?php echo $publisherlist;?></td>
                    <td><?php echo $developerslist;?></td>
                    <td><?php echo isset($gamereleases[0]) ? date("F j, Y", strtotime($gamereleases[0]->release_date)) : "-"?></td>
                    <td><?php echo $platformlist;?></td>
                    <td><?php echo isset($gamereleases[0]) ? date("F j, Y", strtotime($game->updated_at)) : "-"?></td>
                    <input type='hidden' class='game-id' value='<?php echo $game->id; ?>'><input type='hidden' class='igdb-id' value='<?php echo $game->igdb_id ;?>'>
                </tr>   
            <?php
            }
        ?>
            
        </tbody>
    </table>
</div>