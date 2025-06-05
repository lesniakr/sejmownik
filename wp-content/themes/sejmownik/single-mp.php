<?php
/**
 * The template for displaying single MP pages
 */

get_header();
?>

<div class="container mx-auto px-4 pb-8 pt-4">
    <article class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <!-- MP Profile Header with Photo -->
            <div class="flex flex-col md:flex-row gap-8 mb-8">
                <!-- MP Photo -->
                <div class="w-full md:w-1/4 lg:w-1/6">
                    <div class="bg-gray-100 rounded-lg overflow-hidden shadow-sm h-auto">
                        <?php mp_display_photo(null, 'full', 'w-full rounded-lg object-contain mp-single-photo'); ?>
                    </div>
                </div>
                
                <!-- MP Basic Information -->
                <div class="w-full md:w-3/4 lg:w-5/6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-4"><?php the_title(); ?></h1>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                        <div>
                            <?php if (get_field('club')) : ?>
                                <p class="mb-2"><span class="font-medium">Klub/Partia:</span> 
                                    <span class="text-parlament-blue"><?php echo esc_html(get_field('club')); ?></span>
                                </p>
                            <?php endif; ?>
                            
                            <?php if (get_field('district')) : ?>
                                <p class="mb-2">
                                    <span class="font-medium">Okręg wyborczy:</span> 
                                    <?php echo esc_html(get_field('district')); ?>
                                    <?php if (get_field('district_number')) : ?>
                                        (nr <?php echo esc_html(get_field('district_number')); ?>)
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if (get_field('voivodeship')) : ?>
                                <p class="mb-2"><span class="font-medium">Województwo:</span> <?php echo esc_html(get_field('voivodeship')); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <?php if (get_field('email')) : ?>
                                <p class="mb-2">
                                    <span class="font-medium">E-mail:</span> 
                                    <a href="mailto:<?php echo esc_attr(get_field('email')); ?>" class="text-parlament-blue hover:underline">
                                        <?php echo esc_html(get_field('email')); ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                            
                            <?php if (get_field('mp_id')) : ?>
                                <p class="mb-2"><span class="font-medium">ID posła:</span> <?php echo esc_html(get_field('mp_id')); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- MP Biography -->
            <div class="mt-8 border-t border-gray-200 pt-8">
                <?php if (get_the_content() || get_field('biography')) : ?>
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Biografia</h2>
                    
                    <div class="prose max-w-none text-gray-700">
                        <?php 
                        // Display the main content if available
                        if (get_the_content()) : 
                            the_content(); 
                        endif; 
                        
                        // Display additional biography field if available
                        if (get_field('biography')) : 
                            echo get_field('biography');
                        endif; 
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Back to List Navigation -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="flex justify-between items-center">
                <a href="<?php echo get_post_type_archive_link('mp'); ?>" class="inline-flex items-center text-parlament-blue hover:underline">
                    <i class="fas fa-arrow-left mr-2"></i> Wróć do listy posłów
                </a>
                
                <!-- Social Share Links -->
                <div class="flex items-center space-x-3">
                    <span class="text-gray-600 text-sm">Udostępnij:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-800">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" rel="noopener noreferrer" class="text-blue-400 hover:text-blue-600">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="mailto:?subject=<?php echo urlencode(get_the_title()); ?>&body=<?php echo urlencode(get_permalink()); ?>" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-envelope"></i>
                    </a>
                </div>
            </div>
        </div>
    </article>
</div>

<?php get_footer(); ?>
