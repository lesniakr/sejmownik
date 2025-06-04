<?php
/**
 * WP-CLI commands for importing MPs in bulk
 */

if (!defined('WP_CLI') || !WP_CLI) {
    return;
}

/**
 * Manage Members of Parliament import.
 */
class MP_CLI_Command {
    
    /**
     * Import all MPs from Sejm API.
     *
     * ## OPTIONS
     *
     * [--skip-images]
     * : Skip importing images to speed up the process.
     *
     * [--batch-size=<number>]
     * : Number of MPs to process in a single batch. Default: 50
     *
     * ## EXAMPLES
     *
     *     # Import all MPs with default settings
     *     $ wp mp import
     *
     *     # Import all MPs without images and 100 MPs in a batch
     *     $ wp mp import --skip-images --batch-size=100
     *
     * @param array $args Command arguments.
     * @param array $assoc_args Command options.
     */
    public function import($args, $assoc_args) {
        // Parse options
        $import_images = !isset($assoc_args['skip-images']);
        $batch_size = isset($assoc_args['batch-size']) ? intval($assoc_args['batch-size']) : 50;
        
        WP_CLI::log('Starting MP import...');
        
        // Fetch MPs
        WP_CLI::log('Fetching all MPs from API...');
        $all_mps = MP_API_Handler::fetch_all_mps();
        
        if (is_wp_error($all_mps)) {
            WP_CLI::error('Failed to fetch MPs: ' . $all_mps->get_error_message());
            return;
        }
        
        $total = count($all_mps);
        WP_CLI::log("Found $total MPs to import.");
        
        // Use custom Progress Bar
        $progress = \WP_CLI\Utils\make_progress_bar('Importing MPs', $total);
        
        $imported = 0;
        $errors = array();
        
        // Use database transactions for better performance
        global $wpdb;
        $wpdb->query('START TRANSACTION');
        
        // Defer term counting for better performance
        wp_defer_term_counting(true);
        
        // Process in batches
        for ($i = 0; $i < $total; $i++) {
            try {
                $mp = $all_mps[$i];
                
                // Fetch additional details if needed
                if (empty($mp['firstName']) || empty($mp['lastName'])) {
                    $mp_details = MP_API_Handler::get_mp_details($mp['id']);
                    if (!is_wp_error($mp_details)) {
                        $mp = array_merge($mp, $mp_details);
                    }
                }
                
                $result = MP_API_Handler::import_single_mp_from_details($mp, $import_images);
                
                if (is_wp_error($result)) {
                    $errors[] = array(
                        'id' => $mp['id'],
                        'error' => $result->get_error_message()
                    );
                    WP_CLI::warning("Error importing MP ID {$mp['id']}: " . $result->get_error_message());
                } else {
                    $imported++;
                }
                
                $progress->tick();
                
                // Commit transaction and start a new one every batch
                if (($i + 1) % $batch_size === 0) {
                    $wpdb->query('COMMIT');
                    $wpdb->query('START TRANSACTION');
                    WP_CLI::log("Imported $imported MPs so far...");
                }
            } catch (Exception $e) {
                WP_CLI::warning("Exception during import: " . $e->getMessage());
            }
        }
        
        // Commit final transaction
        $wpdb->query('COMMIT');
        
        // Re-enable term counting
        wp_defer_term_counting(false);
        
        $progress->finish();
        
        WP_CLI::success("Import completed. Successfully imported $imported MPs.");
        
        if (count($errors) > 0) {
            WP_CLI::warning("There were " . count($errors) . " errors during import.");
        }
    }
    
    /**
     * Import images for MPs that are missing them.
     *
     * ## OPTIONS
     *
     * [--limit=<number>]
     * : Maximum number of images to import. Default: all
     *
     * ## EXAMPLES
     *
     *     # Import all missing images
     *     $ wp mp import-images
     *
     *     # Import up to 50 missing images
     *     $ wp mp import-images --limit=50
     *
     * @param array $args Command arguments.
     * @param array $assoc_args Command options.
     */
    public function import_images($args, $assoc_args) {
        $limit = isset($assoc_args['limit']) ? intval($assoc_args['limit']) : 0;
        
        WP_CLI::log('Finding MPs with missing images...');
        
        // Find MPs without featured images
        $query = new WP_Query(array(
            'post_type' => 'mp',
            'posts_per_page' => $limit > 0 ? $limit : -1,
            'meta_query' => array(
                array(
                    'key' => 'photo_url',
                    'compare' => 'EXISTS',
                ),
            ),
            'fields' => 'ids',
        ));
        
        $mp_ids = $query->posts;
        $total = count($mp_ids);
        
        if ($total === 0) {
            WP_CLI::success('No MPs found that need images.');
            return;
        }
        
        WP_CLI::log("Found $total MPs that need images.");
        $progress = \WP_CLI\Utils\make_progress_bar('Importing images', $total);
        
        $imported = 0;
        
        foreach ($mp_ids as $post_id) {
            $photo_url = get_field('photo_url', $post_id);
            
            if (!empty($photo_url) && !has_post_thumbnail($post_id)) {
                $result = MP_API_Handler::set_featured_image($post_id, $photo_url);
                
                if ($result) {
                    $imported++;
                }
            }
            
            $progress->tick();
        }
        
        $progress->finish();
        WP_CLI::success("Imported $imported images for MPs.");
    }
}

WP_CLI::add_command('mp', 'MP_CLI_Command');
