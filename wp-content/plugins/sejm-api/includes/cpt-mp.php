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
            'name'                  => 'Posłowie',
            'singular_name'         => 'Poseł',
            'menu_name'             => 'Posłowie',
            'name_admin_bar'        => 'Poseł',
            'archives'              => 'Archiwum posłów',
            'attributes'            => 'Atrybuty posła',
            'parent_item_colon'     => 'Rodzic:',
            'all_items'             => 'Wszyscy posłowie',
            'add_new_item'          => 'Dodaj nowego posła',
            'add_new'               => 'Dodaj nowego',
            'new_item'              => 'Nowy poseł',
            'edit_item'             => 'Edytuj posła',
            'update_item'           => 'Aktualizuj posła',
            'view_item'             => 'Zobacz posła',
            'view_items'            => 'Zobacz posłów',
            'search_items'          => 'Szukaj posła',
            'not_found'             => 'Nie znaleziono',
            'not_found_in_trash'    => 'Nie znaleziono w koszu',
            'featured_image'        => 'Zdjęcie posła',
            'set_featured_image'    => 'Ustaw zdjęcie posła',
            'remove_featured_image' => 'Usuń zdjęcie posła',
            'use_featured_image'    => 'Użyj jako zdjęcie posła',
            'insert_into_item'      => 'Wstaw do posła',
            'uploaded_to_this_item' => 'Wgrane dla tego posła',
            'items_list'            => 'Lista posłów',
            'items_list_navigation' => 'Nawigacja listy posłów',
            'filter_items_list'     => 'Filtruj listę posłów',
        );
        
        $args = array(
            'label'                 => 'Poseł',
            'description'           => 'Członek Sejmu Rzeczypospolitej Polskiej',
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail', 'excerpt'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-groups',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => 'poslowie',
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
            'rewrite'               => array(
                'slug' => 'posel',
                'with_front' => false
            ),
        );
        
        register_post_type('mp', $args);
    }
}
