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
    <?php 
    // Display the page content (if any)
    while (have_posts()) : the_post();
        the_content();
    endwhile;
    ?>
    
    <!-- MP Grid Section -->
    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold mb-8 text-center"><?php the_title(); ?></h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php
                $args = array(
                    'post_type' => 'mp',
                    'posts_per_page' => 12,
                    'orderby' => 'rand'
                );
                $mp_query = new WP_Query($args);
                
                if ($mp_query->have_posts()) : 
                    while ($mp_query->have_posts()) : $mp_query->the_post();
                        // Get MP data
                        $club = get_field('club');
                        $district = get_field('district');
                ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden animate-scale hover:shadow-lg transition-shadow flex">
                        <div class="w-2/5 relative overflow-hidden bg-gray-200">
                            <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                                <?php mp_display_photo(null, 'medium', 'w-full h-full object-cover'); ?>
                            </a>
                        </div>
                        <div class="p-4 w-3/5">
                            <div class="mb-2">
                                <h3 class="text-lg font-bold text-gray-800">
                                    <a href="<?php the_permalink(); ?>" class="hover:text-parlament-blue"><?php the_title(); ?></a>
                                </h3>
                            </div>
                            
                            <?php if ($club) : ?>
                                <p class="text-gray-600 text-sm mb-2">
                                    <i class="fas fa-landmark mr-1 text-parlament-blue"></i> <?php echo esc_html($club); ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if ($district) : ?>
                                <p class="text-gray-600 text-sm mb-3">
                                    <i class="fas fa-map-marker-alt mr-1 text-parlament-blue"></i> Okręg: <?php echo esc_html($district); ?>
                                </p>
                            <?php endif; ?>
                            
                            <div>
                                <a href="<?php the_permalink(); ?>" class="text-sm text-parlament-red hover:underline font-bold">Zobacz profil</a>
                            </div>
                        </div>
                    </div>
                <?php
                    endwhile;
                    wp_reset_postdata();
                else : ?>
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-600">Nie znaleziono posłów.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="text-center mt-8">
                <a href="<?php echo get_post_type_archive_link('mp'); ?>" class="inline-block px-6 py-3 bg-parlament-blue text-white font-medium rounded-md hover:bg-blue-800 transition">
                    Zobacz wszystkich posłów
                </a>
            </div>
        </div>
    </section>
    
</div>

<?php get_footer(); ?>
