<?php
/**
 * API Handler for Sejm API
 */

class MP_API_Handler {
    const API_BASE_URL = 'https://api.sejm.gov.pl';
    
    /**
     * Fetch all MPs directly from the API
     */
    public static function fetch_all_mps() {
        // Get current term
        $term = self::get_current_term();
        if (!$term) {
            return new WP_Error('api_error', 'Could not get current term');
        }
        
        // Endpoint for the list of all MPs
        $api_url = self::API_BASE_URL . '/sejm/term' . $term . '/MP';
        
        error_log('MP Plugin: Fetching all MPs from: ' . $api_url);
        $response = wp_remote_get($api_url, array(
            'timeout' => 60, // Increased timeout for large request
            'headers' => array(
                'Accept' => 'application/json',
            ),
        ));
        
        if (is_wp_error($response)) {
            error_log('MP Plugin API Error (all MPs): ' . $response->get_error_message());
            return $response;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            error_log('MP Plugin API Error (all MPs): Unexpected status code ' . $status_code);
            return new WP_Error('api_error', 'API returned status code: ' . $status_code);
        }
        
        $body = wp_remote_retrieve_body($response);
        $mps = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('MP Plugin JSON Error (all MPs): ' . json_last_error_msg());
            return new WP_Error('json_error', 'Failed to parse API response: ' . json_last_error_msg());
        }
        
        if (empty($mps)) {
            return new WP_Error('api_error', 'No MPs found');
        }
        
        error_log('MP Plugin: Successfully retrieved ' . count($mps) . ' MPs');
        return $mps;
    }
    
    /**
     * Get current term number
     */
    public static function get_current_term() {
        $api_url = self::API_BASE_URL . '/sejm/term';
        
        error_log('MP Plugin: Fetching terms from: ' . $api_url);
        $response = wp_remote_get($api_url, array(
            'timeout' => 30,
            'headers' => array(
                'Accept' => 'application/json',
            ),
        ));
        
        if (is_wp_error($response)) {
            error_log('MP Plugin API Error (term): ' . $response->get_error_message());
            return false;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            error_log('MP Plugin API Error (term): Unexpected status code ' . $status_code);
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        $terms = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE || empty($terms)) {
            error_log('MP Plugin API Error: Invalid or empty terms response');
            return false;
        }
        
        // Find term marked as current: true
        foreach ($terms as $term) {
            if (isset($term['current']) && $term['current'] === true) {
                error_log('MP Plugin: Found current term: ' . $term['num']);
                return $term['num'];
            }
        }
        
        // If not found, return the last term
        $last_term = end($terms);
        if (isset($last_term['num'])) {
            error_log('MP Plugin: No term marked as current, using last term: ' . $last_term['num']);
            return $last_term['num'];
        }
        
        // Last resort - use term 10
        error_log('MP Plugin: Could not determine current term, using fallback value of 10');
        return 10;
    }
    
    /**
     * Get MP details by ID
     */
    public static function get_mp_details($mp_id) {
        // Get current term
        $term = self::get_current_term();
        if (!$term) {
            return new WP_Error('api_error', 'Could not get current term');
        }
        
        // Fetch MP details using the correct URL format
        $api_url = self::API_BASE_URL . '/sejm/term' . $term . '/MP/' . $mp_id;
        
        error_log('MP Plugin: Fetching MP details from: ' . $api_url);
        $response = wp_remote_get($api_url, array(
            'timeout' => 30,
            'headers' => array(
                'Accept' => 'application/json',
            ),
        ));
        
        if (is_wp_error($response)) {
            error_log('MP Plugin API Error (MP details): ' . $response->get_error_message());
            return $response;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            error_log('MP Plugin API Error (MP details): Unexpected status code ' . $status_code . ' for MP ID ' . $mp_id);
            return new WP_Error('api_error', 'API returned status code: ' . $status_code);
        }
        
        $body = wp_remote_retrieve_body($response);
        $mp_details = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('MP Plugin JSON Error (MP details): ' . json_last_error_msg());
            return new WP_Error('json_error', 'Failed to parse API response: ' . json_last_error_msg());
        }
        
        if (empty($mp_details)) {
            return new WP_Error('api_error', 'No MP details found for ID ' . $mp_id);
        }
        
        return $mp_details;
    }
    
    /**
     * Import all MPs into WordPress
     */
    public static function import_mps() {
        $mps = self::fetch_mps();
        
        if (is_wp_error($mps)) {
            return $mps;
        }
        
        $imported = 0;
        $errors = array();
        
        foreach ($mps as $mp) {
            // MP is already a complete object with all details
            $result = self::import_single_mp_from_details($mp);
            
            if (is_wp_error($result)) {
                $errors[] = $result->get_error_message();
            } else {
                $imported++;
            }
        }
        
        return array(
            'imported' => $imported,
            'errors' => $errors
        );
    }
    
    /**
     * Import a single MP by ID
     */
    public static function import_single_mp($mp_id) {
        $mp_details = self::get_mp_details($mp_id);
        
        if (is_wp_error($mp_details)) {
            return $mp_details;
        }
        
        return self::import_single_mp_from_details($mp_details);
    }
    
    /**
     * Import all MPs into WordPress with progress tracking
     */
    public static function import_mps_with_progress() {
        // Check stop marker file
        $marker_file = WP_CONTENT_DIR . '/uploads/mp_stop_import';
        if (file_exists($marker_file)) {
            error_log('MP Plugin: Import stopped by emergency marker file');
            self::reset_import();
            return array(
                'status' => 'stopped',
                'imported' => 0,
                'total' => 0,
            );
        }
        
        // Check if import is in progress and should be stopped
        if (get_option('mp_import_stop', false)) {
            error_log('MP Plugin: Import stopped by stop flag');
            self::reset_import();
            return array(
                'status' => 'stopped',
                'imported' => get_option('mp_import_completed', 0),
                'total' => get_option('mp_import_total', 0),
            );
        }
        
        // Get all MPs if we don't have them in options yet
        $all_mps = get_option('mp_all_mps', null);
        
        if ($all_mps === null) {
            error_log('MP Plugin: Fetching all MPs for import');
            $all_mps = self::fetch_all_mps();
            
            if (is_wp_error($all_mps)) {
                error_log('MP Plugin: Error fetching MPs: ' . $all_mps->get_error_message());
                return $all_mps;
            }
            
            update_option('mp_all_mps', $all_mps);
            update_option('mp_import_total', count($all_mps));
            update_option('mp_import_progress', 'running');
            update_option('mp_import_completed', 0);
            update_option('mp_import_current', 0);
            update_option('mp_import_errors', array());
            
            error_log('MP Plugin: Started new import with ' . count($all_mps) . ' MPs');
        }
        
        // Get progress information
        $total_mps = count($all_mps);
        $batch_size = 10;
        $current_index = get_option('mp_import_current', 0);
        $completed = get_option('mp_import_completed', 0);
        $errors = get_option('mp_import_errors', array());
        
        // Define range for current batch
        $end_index = min($current_index + $batch_size, $total_mps);
        
        error_log('MP Plugin: Processing batch from ' . $current_index . ' to ' . $end_index . ' of ' . $total_mps);
        
        $imported_in_batch = 0;
        
        // Import batch of MPs
        for ($i = $current_index; $i < $end_index; $i++) {
            // Check for stop
            if (file_exists($marker_file) || get_option('mp_import_stop', false)) {
                error_log('MP Plugin: Import stopped during batch');
                break;
            }
            
            if (isset($all_mps[$i])) {
                $mp = $all_mps[$i];
                
                // Get additional details if necessary
                if (!isset($mp['firstName']) || !isset($mp['lastName'])) {
                    $mp_details = self::get_mp_details($mp['id']);
                    if (!is_wp_error($mp_details)) {
                        $mp = array_merge($mp, $mp_details);
                    }
                }
                
                // Import the MP
                $result = self::import_single_mp_from_details($mp);
                
                if (is_wp_error($result)) {
                    error_log('MP Plugin: Error importing MP with ID ' . $mp['id'] . ': ' . $result->get_error_message());
                    $errors[] = array(
                        'id' => $mp['id'],
                        'error' => $result->get_error_message()
                    );
                } else {
                    $imported_in_batch++;
                    $completed++;
                    
                    // Add progress log
                    error_log('MP Plugin: Import progress - imported ' . $completed . ' of ' . $total_mps . ' MPs');
                }
            }
        }
        
        // Update progress
        $next_index = $end_index;
        update_option('mp_import_current', $next_index);
        update_option('mp_import_completed', $completed);
        update_option('mp_import_errors', $errors);
        
        // Check if import is completed
        if ($next_index >= $total_mps || file_exists($marker_file) || get_option('mp_import_stop', false)) {
            // Import completed or stopped
            error_log('MP Plugin: Import completed or stopped');
            
            // Remove temporary data
            delete_option('mp_all_mps');
            delete_option('mp_import_progress');
            delete_option('mp_import_current');
            delete_option('mp_import_lock');
            delete_option('mp_import_stop');
            
            $status = ($next_index >= $total_mps) ? 'completed' : 'stopped';
            
            return array(
                'status' => $status,
                'imported' => $completed,
                'total' => $total_mps,
                'errors' => $errors
            );
        } else {
            // Import still in progress
            update_option('mp_import_progress', 'in_progress');
            
            return array(
                'status' => 'in_progress',
                'imported' => $completed,
                'total' => $total_mps,
                'current' => $next_index,
                'imported_in_batch' => $imported_in_batch,
                'errors' => $errors
            );
        }
    }
    
    /**
     * Import a single MP from details
     */
    private static function import_single_mp_from_details($mp_details) {
        // Check if mp_id is valid
        if (empty($mp_details['id'])) {
            return new WP_Error('invalid_mp_id', 'Brak prawidłowego ID posła');
        }
        
        // First check if MP already exists using the mp_id key
        $existing_posts = get_posts(array(
            'post_type' => 'mp',
            'meta_query' => array(
                array(
                    'key' => 'mp_id',
                    'value' => $mp_details['id'],
                    'compare' => '='
                ),
            ),
            'posts_per_page' => -1, // Get all matching posts
            'fields' => 'ids',      // Get only IDs for efficiency
            'suppress_filters' => false, 
            'cache_results' => false // Disable cache to always get current data
        ));
        
        $post_id = 0;
        
        // If more than one post is found with the same mp_id, remove duplicates
        if (count($existing_posts) > 1) {
            error_log('MP Plugin: Znaleziono ' . count($existing_posts) . ' duplikatów dla MP ID ' . $mp_details['id']);
            
            // Keep the first post, delete the rest
            $post_id = $existing_posts[0];
            
            for ($i = 1; $i < count($existing_posts); $i++) {
                error_log('MP Plugin: Usuwanie duplikatu ID ' . $existing_posts[$i] . ' dla MP ID ' . $mp_details['id']);
                wp_delete_post($existing_posts[$i], true);
            }
            
            // Update the kept post
            wp_update_post(array(
                'ID' => $post_id,
                'post_title' => $mp_details['firstLastName'],
            ));
        }
        // If exactly one post is found, update it
        elseif (count($existing_posts) === 1) {
            $post_id = $existing_posts[0];
            wp_update_post(array(
                'ID' => $post_id,
                'post_title' => $mp_details['firstLastName'],
            ));
        }
        // If no posts are found, create a new one
        else {
            // Additionally check if an MP with identical name exists
            $title_query = new WP_Query(array(
                'post_type' => 'mp',
                'title' => $mp_details['firstLastName'],
                'posts_per_page' => 1,
                'fields' => 'ids',
            ));
            
            if ($title_query->have_posts()) {
                // MP with same name found, update this post
                $post_id = $title_query->posts[0];
                wp_update_post(array(
                    'ID' => $post_id,
                    'post_title' => $mp_details['firstLastName'],
                ));
                
                // Make sure mp_id is saved
                update_field('mp_id', $mp_details['id'], $post_id);
            } else {
                // Create a new post
                $post_id = wp_insert_post(array(
                    'post_title' => $mp_details['firstLastName'],
                    'post_status' => 'publish',
                    'post_type' => 'mp',
                ));
            }
        }
        
        if (is_wp_error($post_id)) {
            return $post_id;
        }
        
        // Update ACF fields
        update_field('mp_id', $mp_details['id'], $post_id);
        update_field('first_name', $mp_details['firstName'], $post_id);
        update_field('last_name', $mp_details['lastName'], $post_id);
        
        // Handle club field, which can be a string
        if (isset($mp_details['club'])) {
            if (is_array($mp_details['club']) && isset($mp_details['club']['name'])) {
                update_field('club', $mp_details['club']['name'], $post_id);
            } else {
                update_field('club', $mp_details['club'], $post_id);
            }
        }
        
        // District data
        if (isset($mp_details['districtName'])) {
            update_field('district', $mp_details['districtName'], $post_id);
        }
        
        if (isset($mp_details['districtNum'])) {
            update_field('district_number', $mp_details['districtNum'], $post_id);
        }
        
        if (isset($mp_details['email'])) {
            update_field('email', $mp_details['email'], $post_id);
        }
        
        if (isset($mp_details['voivodeship'])) {
            update_field('voivodeship', $mp_details['voivodeship'], $post_id);
        }
        
        return $post_id;
    }
    
    /**
     * Get MP photo URL
     * 
     * @param int $mp_id MP ID from Sejm API
     * @return string Photo URL
     */
    public static function get_mp_photo_url($mp_id) {
        // Get current term
        $term = self::get_current_term();
        if (!$term) {
            return '';
        }
        
        return self::API_BASE_URL . '/sejm/term' . $term . '/MP/' . $mp_id . '/photo';
    }
    
    /**
     * Stop the import process
     */
    public static function stop_import() {
        update_option('mp_import_stop', true);
        return true;
    }
    
    /**
     * Check import status
     */
    public static function get_import_status() {
        $progress = get_option('mp_import_progress', false);
        
        if (!$progress) {
            return array(
                'status' => 'not_running',
                'imported' => 0,
                'total' => 0,
                'current' => 0 // Add default current value
            );
        }
        
        return array(
            'status' => $progress,
            'imported' => get_option('mp_import_completed', 0),
            'total' => get_option('mp_import_total', 0),
            'current' => get_option('mp_import_current', 0),
        );
    }
    
    /**
     * Reset import status
     */
    public static function reset_import() {
        delete_option('mp_import_progress');
        delete_option('mp_import_current');
        delete_option('mp_import_total');
        delete_option('mp_import_completed');
        delete_option('mp_import_errors');
        delete_option('mp_import_stop');
        
        return true;
    }
}
