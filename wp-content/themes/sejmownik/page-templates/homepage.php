<?php
/**
 * Template Name: Strona główna z listą posłów
 * Template Post Type: page
 * 
 * This template can be assigned to a page to make it display a list of MPs
 */

get_header();
?>

<div>
    <h1><?php the_title(); ?></h1>
    
    <?php 
    // Display the page content (if any)
    while (have_posts()) : the_post();
        the_content();
    endwhile;
    ?>
    
    <div>
        <?php
        // Query for MPs
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $args = array(
            'post_type' => 'mp',
            'posts_per_page' => 20,
            'paged' => $paged
        );
        
        $mp_query = new WP_Query($args);
        
        if ($mp_query->have_posts()) : ?>
            <?php while ($mp_query->have_posts()) : $mp_query->the_post(); ?>
                <a href="<?php the_permalink(); ?>">
                    <div>
                        <div>
                            <?php mp_display_photo(null, 'medium'); ?>
                        </div>
                        <div>
                            <h2><?php the_title(); ?></h2>
                            <p>
                                <?php echo esc_html(get_field('club')); ?>
                            </p>
                            <?php if (get_field('district')) : ?>
                                <p>
                                    Okręg: <?php echo esc_html(get_field('district')); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endwhile; ?>
        <?php else : ?>
            <div>
                <p>Nie znaleziono żadnych członków parlamentu.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <div>
        <?php
        echo paginate_links(array(
            'total' => $mp_query->max_num_pages,
            'current' => $paged,
            'prev_text' => '&laquo; Poprzednia',
            'next_text' => 'Następna &raquo;',
        ));
        wp_reset_postdata();
        ?>
    </div>
</div>

<?php get_footer(); ?>
