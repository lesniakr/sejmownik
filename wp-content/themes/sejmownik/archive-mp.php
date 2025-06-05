<?php
/**
 * The template for displaying MP archives
 */

get_header();
?>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8 text-center"><?php post_type_archive_title(); ?></h1>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); 
                // Get MP data
                $club = get_field('club');
                $district = get_field('district');
            ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="relative w-full h-48 overflow-hidden">
                        <?php mp_display_photo(null, 'medium', 'w-full h-full object-cover'); ?>
                    </div>
                    <div class="p-4">
                        <div class="mb-2">
                            <h3 class="text-lg font-bold text-gray-800"><?php the_title(); ?></h3>
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
                            <a href="<?php the_permalink(); ?>" class="text-sm text-parlament-blue hover:underline">Profil</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <div class="col-span-full text-center py-12">
                <p class="text-gray-600">Nie znaleziono posłów.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="mt-8">
        <?php the_posts_pagination(array(
            'mid_size' => 2,
            'prev_text' => '<i class="fas fa-chevron-left mr-2"></i>Poprzednia',
            'next_text' => 'Następna<i class="fas fa-chevron-right ml-2"></i>',
            'class' => 'flex justify-center',
        )); ?>
    </div>
</div>

<?php get_footer(); ?>
