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
