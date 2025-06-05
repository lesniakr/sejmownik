<?php
/**
 * The template for displaying search results
 */

get_header();

// Check if we're searching for MPs (the header form includes post_type=mp)
$is_mp_search = isset($_GET['post_type']) && $_GET['post_type'] === 'mp';
$search_query = get_search_query();
?>

<div class="container mx-auto px-4 pb-8 pt-4">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-center md:text-left mb-4 md:mb-0">
            <?php if ($is_mp_search): ?>
                Wyniki wyszukiwania posłów: <span class="text-parlament-blue"><?php echo esc_html($search_query); ?></span>
            <?php else: ?>
                Wyniki wyszukiwania: <span class="text-parlament-blue"><?php echo esc_html($search_query); ?></span>
            <?php endif; ?>
        </h1>
    </div>
    
    <?php if ($is_mp_search): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); 
                    // Get MP data
                    $club = get_field('club');
                    $district = get_field('district');
                ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow flex">
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
                <?php endwhile; ?>
            <?php else : ?>
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-600">Nie znaleziono posłów spełniających kryteria wyszukiwania "<?php echo esc_html($search_query); ?>".</p>
                    <p class="mt-4">
                        <a href="<?php echo get_post_type_archive_link('mp'); ?>" class="inline-block px-6 py-3 bg-parlament-blue text-white font-medium rounded-md hover:bg-blue-800 transition">
                            Zobacz wszystkich posłów
                        </a>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow-md p-6">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <article class="mb-6 pb-6 border-b border-gray-200 last:border-0 last:mb-0 last:pb-0">
                        <h2 class="text-xl font-bold mb-2">
                            <a href="<?php the_permalink(); ?>" class="text-parlament-blue hover:underline"><?php the_title(); ?></a>
                        </h2>
                        <div class="text-gray-600 mb-3">
                            <?php echo get_the_date(); ?> | <?php the_author(); ?>
                        </div>
                        <div class="text-gray-700">
                            <?php the_excerpt(); ?>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="inline-block mt-3 text-parlament-blue hover:underline">Czytaj więcej</a>
                    </article>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="text-center py-8">
                    <p class="text-gray-600">Nie znaleziono wyników dla "<?php echo esc_html($search_query); ?>".</p>
                    <p class="mt-4">
                        <a href="<?php echo home_url(); ?>" class="inline-block px-6 py-3 bg-parlament-blue text-white font-medium rounded-md hover:bg-blue-800 transition">
                            Wróć do strony głównej
                        </a>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <div class="pt-8">
        <?php
        $pagination = paginate_links(array(
            'mid_size' => 2,
            'prev_text' => '&laquo; Poprzednia',
            'next_text' => 'Następna &raquo;',
            'type' => 'array',
        ));
        
        if ($pagination) : ?>
            <nav aria-label="Paginacja" class="flex justify-center">
                <ul class="inline-flex items-center space-x-2">
                    <?php foreach ($pagination as $key => $page_link) : 
                        $active = strpos($page_link, 'current') !== false ? 'bg-parlament-blue text-white' : 'bg-white text-gray-500 hover:bg-gray-100 hover:text-parlament-blue';
                    ?>
                        <li>
                            <?php 
                            // Clean up the page link classes and add Tailwind classes
                            $page_link = preg_replace('/class="[^"]*"/', '', $page_link);
                            
                            if (strpos($page_link, 'prev') !== false) {
                                echo str_replace('<a', '<a class="block px-3 py-2 leading-tight border border-gray-300 rounded-md ' . $active . '"', $page_link);
                            } elseif (strpos($page_link, 'next') !== false) {
                                echo str_replace('<a', '<a class="block px-3 py-2 leading-tight border border-gray-300 rounded-md ' . $active . '"', $page_link);
                            } elseif (strpos($page_link, 'current') !== false) {
                                echo str_replace(['<a', '<span'], ['<a class="z-10 px-3 py-2 leading-tight border border-parlament-blue rounded-md ' . $active . '"', '<span class="z-10 px-3 py-2 leading-tight border border-parlament-blue rounded-md ' . $active . '"'], $page_link);
                            } else {
                                echo str_replace('<a', '<a class="px-3 py-2 leading-tight border border-gray-300 rounded-md ' . $active . '"', $page_link);
                            }
                            ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
