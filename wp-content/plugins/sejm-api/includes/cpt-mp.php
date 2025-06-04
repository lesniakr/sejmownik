<?php
/**
 * Custom Post Type for Members of Parliament (Posłowie)
 */

class MP_CPT {
    /**
     * Register the custom post type
     */
    public static function register() {
        $labels = array(
            'name'               => 'Posłowie',
            'singular_name'      => 'Poseł',
            'menu_name'          => 'Posłowie',
            'add_new'            => 'Dodaj nowego',
            'add_new_item'       => 'Dodaj nowego posła',
            'edit_item'          => 'Edytuj posła',
            'new_item'           => 'Nowy poseł',
            'view_item'          => 'Zobacz posła',
            'search_items'       => 'Szukaj posłów',
            'not_found'          => 'Nie znaleziono posłów',
            'not_found_in_trash' => 'Nie znaleziono posłów w koszu',
        );

        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'posel'),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-groups',
            'supports'            => array('title', 'editor', 'thumbnail', 'excerpt'),
        );

        register_post_type('mp', $args);
    }
}
