<?php
/**
 * Plugin Name: Posłowie Parlamentu
 * Description: Wyświetlanie członków polskiego parlamentu za pomocą danych z API Sejmu
 * Version: 1.0
 * Author: Rafał Leśniak
 * Author URI: https://rafallesniak.com/
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('MP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MP_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include files
require_once MP_PLUGIN_DIR . 'includes/cpt-mp.php';
require_once MP_PLUGIN_DIR . 'includes/acf-fields.php';
require_once MP_PLUGIN_DIR . 'includes/api-handler.php';
require_once MP_PLUGIN_DIR . 'includes/admin-page.php';

// Initialize plugin
function mp_init() {
    // Register ACF fields if ACF is active
    if (class_exists('ACF')) {
        MP_ACF::register_fields();
    }
    
    // Add admin page
    MP_Admin::init();
}
add_action('plugins_loaded', 'mp_init');

// Register custom post type
function mp_register_post_types() {
    // Register custom post type
    MP_CPT::register();
}
add_action('init', 'mp_register_post_types');

// Activation function
function mp_activate() {
    // Register CPT on activation to flush rewrite rules
    MP_CPT::register();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'mp_activate');

// Add custom styles
function mp_enqueue_styles() {
    wp_enqueue_style('mp-styles', MP_PLUGIN_URL . 'assets/css/mp-styles.css', array(), '1.0.0');
}
add_action('wp_enqueue_scripts', 'mp_enqueue_styles');

/**
 * Change the "Featured Image" text to "Zdjęcie posła" for MP post type
 */
function mp_change_featured_image_text($labels) {
    $labels->featured_image = 'Zdjęcie posła';
    $labels->set_featured_image = 'Ustaw zdjęcie posła';
    $labels->remove_featured_image = 'Usuń zdjęcie posła';
    $labels->use_featured_image = 'Użyj jako zdjęcie posła';
    
    return $labels;
}
add_filter('post_type_labels_mp', 'mp_change_featured_image_text');

/**
 * Generate a default MP ID for manually created MPs
 */
function mp_generate_default_id($post_id, $post, $update) {
    if ($post->post_type !== 'mp' || wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
        return;
    }
    
    // Check if this post already has an MP ID
    $existing_mp_id = get_field('mp_id', $post_id);
    
    // If no MP ID is set, generate one
    if (empty($existing_mp_id)) {
        // Generate ID with 'm' prefix + post ID
        $new_mp_id = 'm' . $post_id;
        
        // Update the field
        update_field('mp_id', $new_mp_id, $post_id);
        
        // Log for debugging
        error_log('MP Plugin: Generated manual MP ID: ' . $new_mp_id . ' for post ' . $post_id);
    }
}
add_action('save_post', 'mp_generate_default_id', 20, 3);

/**
 * Get complete MP data for display in templates
 * 
 * @param int $post_id Post ID (optional, defaults to current post)
 * @return array Complete MP data including API data
 */
function get_mp_complete_data($post_id = null) {
    if (null === $post_id) {
        $post_id = get_the_ID();
    }
    
    // Get basic MP data from post meta/ACF
    $mp_data = array(
        'title' => get_the_title($post_id),
        'mp_id' => get_field('mp_id', $post_id),
        'first_name' => get_field('first_name', $post_id),
        'last_name' => get_field('last_name', $post_id),
        'club' => get_field('club', $post_id),
        'district' => get_field('district', $post_id),
        'district_number' => get_field('district_number', $post_id),
        'email' => get_field('email', $post_id),
        'voivodeship' => get_field('voivodeship', $post_id),
        'biography' => get_field('biography', $post_id),
        'active' => get_field('active', $post_id),
        'birthDate' => get_field('birthDate', $post_id),
        'birthLocation' => get_field('birthLocation', $post_id),
        'educationLevel' => get_field('educationLevel', $post_id),
        'profession' => get_field('profession', $post_id),
        'numberOfVotes' => get_field('numberOfVotes', $post_id),
        'accusativeName' => get_field('accusativeName', $post_id),
        'genitiveName' => get_field('genitiveName', $post_id),
    );
    
    // Try to get additional data from API if mp_id exists
    if (!empty($mp_data['mp_id'])) {
        // Skip API call for manually created MPs (ID starts with 'm')
        $is_manual_mp = substr($mp_data['mp_id'], 0, 1) === 'm';
        
        if (!$is_manual_mp) {
            $api_data = false;
            
            // Check if the method exists before calling it
            if (method_exists('MP_API_Handler', 'get_mp_term_details')) {
                $api_data = MP_API_Handler::get_mp_term_details($mp_data['mp_id']);
            } else {
                error_log('MP Plugin: get_mp_term_details method not found in MP_API_Handler class');
                
                // Fallback to get_mp_details if available
                if (method_exists('MP_API_Handler', 'get_mp_details')) {
                    $api_data = MP_API_Handler::get_mp_details($mp_data['mp_id']);
                    error_log('MP Plugin: Using fallback get_mp_details method');
                }
            }
            
            if ($api_data && !is_wp_error($api_data)) {
                // Add API-specific fields to our data array
                $api_fields = array(
                    'birthDate', 'birthLocation', 'educationLevel',
                    'profession', 'secondName', 'numberOfVotes',
                    'accusativeName', 'genitiveName'
                );
                
                // Handle special case for boolean fields
                if (isset($api_data['active']) && !isset($mp_data['active'])) {
                    $mp_data['active'] = $api_data['active'];
                }
                
                foreach ($api_fields as $field) {
                    if (isset($api_data[$field]) && empty($mp_data[$field])) {
                        $mp_data[$field] = $api_data[$field];
                    }
                }
            }
        }
    }
    
    return $mp_data;
}
