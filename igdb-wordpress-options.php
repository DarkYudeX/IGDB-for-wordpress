<div class="wrap">
    <h2>IGDB for Wordpress Settings</h2>


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


</div>