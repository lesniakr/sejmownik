<?php

/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 */
?>

</div><!-- #content -->

<footer class="bg-gray-800 text-white py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <?php
                if (has_nav_menu('footer-1')) {
                    // Get the menu object
                    $menu_obj = wp_get_nav_menu_object(get_nav_menu_locations()['footer-1']);
                    // Display menu name as heading if available
                    if ($menu_obj) {
                        echo '<h3 class="text-xl font-bold mb-4">' . esc_html($menu_obj->name) . '</h3>';
                    } else {
                        echo '<h3 class="text-xl font-bold mb-4">Informacje</h3>';
                    }
                    
                    wp_nav_menu(array(
                        'theme_location' => 'footer-1',
                        'container' => false,
                        'menu_class' => 'space-y-2',
                        'fallback_cb' => false,
                        'items_wrap' => '<ul class="%2$s">%3$s</ul>',
                        'link_before' => '<span class="hover:text-parlament-gold transition">',
                        'link_after' => '</span>',
                    ));
                }
                ?>
            </div>
            <div>
                <?php
                if (has_nav_menu('footer-2')) {
                    // Get the menu object
                    $menu_obj = wp_get_nav_menu_object(get_nav_menu_locations()['footer-2']);
                    // Display menu name as heading if available
                    if ($menu_obj) {
                        echo '<h3 class="text-xl font-bold mb-4">' . esc_html($menu_obj->name) . '</h3>';
                    } else {
                        echo '<h3 class="text-xl font-bold mb-4">Dla obywateli</h3>';
                    }
                    
                    wp_nav_menu(array(
                        'theme_location' => 'footer-2',
                        'container' => false,
                        'menu_class' => 'space-y-2',
                        'fallback_cb' => false,
                        'items_wrap' => '<ul class="%2$s">%3$s</ul>',
                        'link_before' => '<span class="hover:text-parlament-gold transition">',
                        'link_after' => '</span>',
                    ));
                }
                ?>
            </div>
            <div>
                <h3 class="text-xl font-bold mb-4">Kontakt</h3>
                <address class="not-italic">
                    <div class="mb-2">
                        <i class="fas fa-map-marker-alt mr-2 text-parlament-gold"></i> ul. Wiejska 1, 00-902 Warszawa
                    </div>
                    <div class="mb-2">
                        <i class="fas fa-phone-alt mr-2 text-parlament-gold"></i> +48 22 694 10 00
                    </div>
                    <div class="mb-2">
                        <i class="fas fa-envelope mr-2 text-parlament-gold"></i> info@sejm.gov.pl
                    </div>
                </address>
                <div class="mt-4">
                    <h4 class="font-medium mb-2">Śledź nas:</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-2xl hover:text-parlament-gold transition"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-2xl hover:text-parlament-gold transition"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-2xl hover:text-parlament-gold transition"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="text-2xl hover:text-parlament-gold transition"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. Wszelkie prawa zastrzeżone.</p>
            <p class="mt-2 text-sm">Realizacja: <a href="https://rafallesniak.com/" target="_blank" class="hover:text-parlament-gold transition">Rafał Leśniak</a></p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>

</body>
</html>