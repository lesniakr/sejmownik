<?php

/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 */
?>

</div><!-- #content -->

<footer class="bg-gray-800 text-white pb-8 pt-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <?php 
                $footer_logo = get_field('footer_logo', 'option');
                if ($footer_logo) : ?>
                    <div class="mb-4">
                        <a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr(get_bloginfo('name')); ?>">
                            <img src="<?php echo esc_url($footer_logo['url']); ?>" 
                                 alt="<?php echo esc_attr($footer_logo['alt'] ?: get_bloginfo('name')); ?>" 
                                 class="max-w-[250px]">
                        </a>
                    </div>
                <?php endif; 
                
                $footer_description = get_field('footer_description', 'option');
                if ($footer_description) : 
                    echo '<p>' . esc_html($footer_description) . '</p>';
                endif; 
                ?>
            </div>
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
                <?php
                if (has_nav_menu('footer-2')) {
                    // Get the menu object
                    $menu_obj = wp_get_nav_menu_object(get_nav_menu_locations()['footer-2']);
                    // Display menu name as heading if available
                    if ($menu_obj) {
                        echo '<h3 class="text-xl font-bold mt-4 mb-4">' . esc_html($menu_obj->name) . '</h3>';
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
                <?php if (get_field('footer_contact_heading', 'option')): ?>
                    <h3 class="text-xl font-bold mb-4"><?php echo esc_html(get_field('footer_contact_heading', 'option')); ?></h3>
                <?php endif; ?>
                
                <address class="not-italic">
                    <?php if (get_field('footer_address', 'option')): ?>
                    <div class="mb-2">
                        <i class="fas fa-map-marker-alt mr-2 text-parlament-gold"></i> <?php echo esc_html(get_field('footer_address', 'option')); ?>
                    </div>
                    <?php endif; ?>

                    <?php if (get_field('footer_phone', 'option')): ?>
                    <div class="mb-2">
                        <i class="fas fa-phone-alt mr-2 text-parlament-gold"></i> <?php echo esc_html(get_field('footer_phone', 'option')); ?>
                    </div>
                    <?php endif; ?>

                    <?php if (get_field('footer_email', 'option')): ?>
                    <div class="mb-2">
                        <i class="fas fa-envelope mr-2 text-parlament-gold"></i> 
                        <a href="mailto:<?php echo esc_attr(get_field('footer_email', 'option')); ?>" class="hover:text-parlament-gold transition">
                            <?php echo esc_html(get_field('footer_email', 'option')); ?>
                        </a>
                    </div>
                    <?php endif; ?>
                </address>
                
                <?php if (have_rows('footer_social_media', 'option')): ?>
                <div class="mt-4">
                    <?php if (get_field('footer_social_heading', 'option')): ?>
                        <h4 class="font-medium mb-2"><?php echo esc_html(get_field('footer_social_heading', 'option')); ?></h4>
                    <?php endif; ?>
                    <div class="flex space-x-4">
                        <?php while (have_rows('footer_social_media', 'option')): the_row(); ?>
                            <?php 
                            $icon = get_sub_field('icon');
                            $url = get_sub_field('url');
                            if ($icon && $url): 
                            ?>
                            <a href="<?php echo esc_url($url); ?>" class="text-2xl hover:text-parlament-gold transition" target="_blank" rel="noopener noreferrer">
                                <i class="<?php echo esc_attr($icon); ?>"></i>
                            </a>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. Wszystkie prawa zastrzeżone. Projekt i wykonanie: <a href="https://rafallesniak.com/" target="_blank" class="hover:text-parlament-gold transition">Rafał Leśniak</a></p>
            <p class="mt-2 text-sm">Serwis nie jest oficjalną stroną Sejmu RP. Dane pochodzą z oficjalnego API Sejmu.</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>

</body>
</html>