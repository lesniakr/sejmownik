<?php
/**
 * Members of Parliament theme functions and definitions
 */

if (!defined('_S_VERSION')) {
    define('_S_VERSION', '1.0.0');
}

if (!function_exists('sejmownik_setup')) :
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     */
    function sejmownik_setup() {
        // Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');

        // Let WordPress manage the document title.
        add_theme_support('title-tag');

        // Enable support for Post Thumbnails on posts and pages.
        add_theme_support('post-thumbnails');

        // Add support for core custom logo.
        add_theme_support('custom-logo', array(
            'height'      => 250,
            'width'       => 250,
            'flex-width'  => true,
            'flex-height' => true,
        ));

        // Register nav menus
        register_nav_menus(array(
            'primary' => esc_html__('Primary Menu', 'sejmownik'),
            'footer'  => esc_html__('Footer Menu', 'sejmownik'),
        ));
    }
endif;
add_action('after_setup_theme', 'sejmownik_setup');

/**
 * Enqueue scripts and styles.
 */
function sejmownik_scripts() {
    // Enqueue main CSS
    wp_enqueue_style('sejmownik-main', get_template_directory_uri() . '/assets/css/main.css', array(), wp_get_theme()->get('Version'));
    
    // Enqueue theme's main stylesheet
    wp_enqueue_style('sejmownik-style', get_stylesheet_uri(), array(), wp_get_theme()->get('Version'));

}
add_action('wp_enqueue_scripts', 'sejmownik_scripts');

/**
 * Include template functions
 */
require get_template_directory() . '/includes/template-functions.php';