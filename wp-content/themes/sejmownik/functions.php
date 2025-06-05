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
    }
endif;
add_action('after_setup_theme', 'sejmownik_setup');

/**
 * Register navigation menus
 */
function sejmownik_register_menus() {
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'sejmownik'),
        'footer-1' => esc_html__('Footer Menu 1', 'sejmownik'),
        'footer-2' => esc_html__('Footer Menu 2', 'sejmownik'),
    ));
}
add_action('after_setup_theme', 'sejmownik_register_menus');

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

/**
 * Modify the number of MPs per page in archive and handle sorting
 */
function sejmownik_modify_mp_archive_query($query) {
    // Only modify the main query on frontend for MP archive
    if (!is_admin() && $query->is_main_query() && is_post_type_archive('mp')) {
        // Set posts per page
        $query->set('posts_per_page', 12);
        
        // Handle sorting
        $sort = isset($_GET['sort']) ? sanitize_text_field($_GET['sort']) : 'name_asc';
        
        switch ($sort) {
            case 'name_asc':
                $query->set('meta_key', 'last_name');
                $query->set('orderby', 'meta_value');
                $query->set('order', 'ASC');
                break;
                
            case 'name_desc':
                $query->set('meta_key', 'last_name');
                $query->set('orderby', 'meta_value');
                $query->set('order', 'DESC');
                break;
                
            case 'date_desc':
                $query->set('orderby', 'date');
                $query->set('order', 'DESC');
                break;
                
            case 'date_asc':
                $query->set('orderby', 'date');
                $query->set('order', 'ASC');
                break;
                
            case 'id_asc':
                $query->set('orderby', 'ID');
                $query->set('order', 'ASC');
                break;
                
            case 'id_desc':
                $query->set('orderby', 'ID');
                $query->set('order', 'DESC');
                break;
                
            default:
                $query->set('meta_key', 'last_name');
                $query->set('orderby', 'meta_value');
                $query->set('order', 'ASC');
                break;
        }
    }
}
add_action('pre_get_posts', 'sejmownik_modify_mp_archive_query');

/**
 * Register ACF Options Page
 */
if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title'    => 'Opcje motywu',
        'menu_title'    => 'Opcje motywu',
        'menu_slug'     => 'theme-options',
        'capability'    => 'edit_posts',
        'redirect'      => false,
        'icon_url'      => 'dashicons-admin-customizer',
        'position'      => 60
    ));
}

/**
 * Register ACF Fields
 */
if (function_exists('acf_add_local_field_group')) {
    // Footer Settings
    acf_add_local_field_group(array(
        'key' => 'group_footer_settings',
        'title' => 'Ustawienia stopki',
        'fields' => array(
            array(
                'key' => 'field_footer_contact_tab',
                'label' => 'Informacje kontaktowe',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_footer_contact_heading',
                'label' => 'Nagłówek sekcji kontakt',
                'name' => 'footer_contact_heading',
                'type' => 'text',
                'default_value' => 'Kontakt',
                'instructions' => 'Tekst wyświetlany jako nagłówek sekcji kontaktowej',
            ),
            array(
                'key' => 'field_footer_address',
                'label' => 'Adres',
                'name' => 'footer_address',
                'type' => 'textarea',
                'rows' => 2,
                'default_value' => 'ul. Wiejska 1, 00-902 Warszawa',
            ),
            array(
                'key' => 'field_footer_phone',
                'label' => 'Telefon',
                'name' => 'footer_phone',
                'type' => 'text',
                'default_value' => '+48 22 694 10 00',
            ),
            array(
                'key' => 'field_footer_email',
                'label' => 'Email',
                'name' => 'footer_email',
                'type' => 'email',
                'default_value' => 'info@sejm.gov.pl',
            ),
            array(
                'key' => 'field_footer_social_tab',
                'label' => 'Media społecznościowe',
                'name' => '',
                'type' => 'tab',
            ),
            array(
                'key' => 'field_footer_social_heading',
                'label' => 'Nagłówek sekcji',
                'name' => 'footer_social_heading',
                'type' => 'text',
                'default_value' => 'Śledź nas:',
                'instructions' => 'Tekst wyświetlany nad ikonami mediów społecznościowych',
            ),
            array(
                'key' => 'field_footer_social_media',
                'label' => 'Media społecznościowe',
                'name' => 'footer_social_media',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => 'Dodaj media społecznościowe',
                'sub_fields' => array(
                    array(
                        'key' => 'field_footer_social_media_icon',
                        'label' => 'Ikona (klasa FontAwesome)',
                        'name' => 'icon',
                        'type' => 'text',
                        'instructions' => 'Wprowadź klasę Font Awesome, np. fab fa-facebook',
                        'required' => 1,
                    ),
                    array(
                        'key' => 'field_footer_social_media_url',
                        'label' => 'URL',
                        'name' => 'url',
                        'type' => 'url',
                        'required' => 1,
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'options_page',
                    'operator' => '==',
                    'value' => 'theme-options',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => '',
    ));
}