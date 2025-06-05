<?php
/**
 * The template for displaying MP archives
 */

get_header();

// Get current sort parameter or set default
$current_sort = isset($_GET['sort']) ? sanitize_text_field($_GET['sort']) : 'lastname_asc';
?>

<div class="container mx-auto px-4 pb-8 pt-4">
    
    <div class="flex flex-col md:flex-row justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-center md:text-left mb-4 md:mb-0"><?php post_type_archive_title(); ?></h1>
        
        <div class="mp-sort-controls">
            <form method="get" action="<?php echo esc_url(get_post_type_archive_link('mp')); ?>" class="flex items-center">
                <label for="sort-select" class="mr-2 text-gray-700">Sortuj wg:</label>
                <select name="sort" id="sort-select" class="border border-gray-300 rounded py-2 px-3 focus:outline-none focus:ring-2 focus:ring-parlament-blue">
                    <option value="lastname_asc" <?php selected($current_sort, 'lastname_asc'); ?>>Nazwisko (A-Z)</option>
                    <option value="lastname_desc" <?php selected($current_sort, 'lastname_desc'); ?>>Nazwisko (Z-A)</option>
                    <option value="firstname_asc" <?php selected($current_sort, 'firstname_asc'); ?>>Imię (A-Z)</option>
                    <option value="firstname_desc" <?php selected($current_sort, 'firstname_desc'); ?>>Imię (Z-A)</option>
                    <option value="date_desc" <?php selected($current_sort, 'date_desc'); ?>>Najnowsze</option>
                    <option value="date_asc" <?php selected($current_sort, 'date_asc'); ?>>Najstarsze</option>
                    <option value="id_asc" <?php selected($current_sort, 'id_asc'); ?>>ID (rosnąco)</option>
                    <option value="id_desc" <?php selected($current_sort, 'id_desc'); ?>>ID (malejąco)</option>
                </select>
                <button type="submit" class="ml-2 bg-parlament-blue text-white py-2 px-3 rounded hover:bg-blue-800">
                    <i class="fas fa-sort mr-1"></i> Sortuj
                </button>
            </form>
        </div>
    </div>
    
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
