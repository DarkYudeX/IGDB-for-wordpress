<div class="wrap">
    <h2>IGDB for Wordpress Settings</h2>
    <div id="tabs">
        <ul>
            <li><a href="#general-options">General</a></li>
            <li><a href="#refresh-games" class="refresh-click">Refresh Games</a></li>
            <li><a href="#shortcode">Shortcode</a></li>
        </ul>
        <div id="general-options">
            <form method="post" action="options.php">
                <?php wp_nonce_field('update-options') ?>
                <p>
                    <strong>IGDB API:</strong><br />
                    <input type="text" name="IGDB_API" size="45" value="<?php echo get_option('igdb_api'); ?>" />
                </p>
                <p>
                    <input type="submit" name="Submit" value="Store Options" />
                </p>
                <input type="hidden" name="action" value="update" />
                <input type="hidden" name="page_options" value="IGDB_API" />
            </form>
        </div>
        <div id="refresh-games">

                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th>Game Name</th>
                            <th>Options</th>          
                        </tr>
                    </thead>
                    <tbody class="game-list">
                        
                        
                    </tbody>
                </table>
        </div>
        <div id="shortcode">
            <p>Mauris eleifend est et turpis. Duis id erat. Suspendisse potenti. Aliquam vulputate, pede vel vehicula accumsan, mi neque rutrum erat, eu congue orci lorem eget lorem. Vestibulum non ante. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Fusce sodales. Quisque eu urna vel enim commodo pellentesque. Praesent eu risus hendrerit ligula tempus pretium. Curabitur lorem enim, pretium nec, feugiat nec, luctus a, lacus.</p>
            <p>Duis cursus. Maecenas ligula eros, blandit nec, pharetra at, semper at, magna. Nullam ac lacus. Nulla facilisi. Praesent viverra justo vitae neque. Praesent blandit adipiscing velit. Suspendisse potenti. Donec mattis, pede vel pharetra blandit, magna ligula faucibus eros, id euismod lacus dolor eget odio. Nam scelerisque. Donec non libero sed nulla mattis commodo. Ut sagittis. Donec nisi lectus, feugiat porttitor, tempor ac, tempor vitae, pede. Aenean vehicula velit eu tellus interdum rutrum. Maecenas commodo. Pellentesque nec elit. Fusce in lacus. Vivamus a libero vitae lectus hendrerit hendrerit.</p>
        </div>
    </div>

</div>