<?php
/**
 * The template for displaying single MP pages
 */

get_header();

// Get complete MP data from the plugin
$mp_data = function_exists('get_mp_complete_data') ? get_mp_complete_data() : array();
?>

<div class="container mx-auto px-4 pb-8 pt-4">
    <article class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <div class="flex flex-col md:flex-row gap-8 mb-8">
                <div class="w-full md:w-1/4 lg:w-1/6">
                    <div class="bg-gray-100 rounded-lg overflow-hidden shadow-sm h-auto">
                        <?php mp_display_photo(null, 'full', 'w-full rounded-lg object-contain mp-single-photo border border-solid border-gray-200'); ?>
                    </div>
                </div>
                
                <div class="w-full md:w-3/4 lg:w-5/6">
                    <h1 class="text-3xl font-bold text-gray-800 mb-4"><?php the_title(); ?></h1>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                        <div>
                            <?php if (!empty($mp_data['first_name'])) : ?>
                                <p class="mb-2"><span class="font-medium">Imię:</span> 
                                    <?php echo esc_html($mp_data['first_name']); ?>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($mp_data['last_name'])) : ?>
                                <p class="mb-2"><span class="font-medium">Nazwisko:</span> 
                                    <?php echo esc_html($mp_data['last_name']); ?>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($mp_data['club'])) : ?>
                                <p class="mb-2"><span class="font-medium">Klub/Partia:</span> 
                                    <?php echo esc_html($mp_data['club']); ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($mp_data['district'])) : ?>
                                <p class="mb-2">
                                    <span class="font-medium">Okręg wyborczy:</span> 
                                    <?php echo esc_html($mp_data['district']); ?>
                                    <?php if (!empty($mp_data['district_number'])) : ?>
                                        (nr <?php echo esc_html($mp_data['district_number']); ?>)
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($mp_data['voivodeship'])) : ?>
                                <p class="mb-2"><span class="font-medium">Województwo:</span> <?php echo esc_html($mp_data['voivodeship']); ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($mp_data['profession'])) : ?>
                                <p class="mb-2"><span class="font-medium">Zawód:</span> <?php echo esc_html($mp_data['profession']); ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($mp_data['educationLevel'])) : ?>
                                <p class="mb-2"><span class="font-medium">Wykształcenie:</span> <?php echo esc_html($mp_data['educationLevel']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <?php if (!empty($mp_data['birthDate'])) : ?>
                                <p class="mb-2"><span class="font-medium">Data urodzenia:</span> <?php echo esc_html(date('d.m.Y', strtotime($mp_data['birthDate']))); ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($mp_data['birthLocation'])) : ?>
                                <p class="mb-2"><span class="font-medium">Miejsce urodzenia:</span> <?php echo esc_html($mp_data['birthLocation']); ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($mp_data['email'])) : ?>
                                <p class="mb-2">
                                    <span class="font-medium">E-mail:</span> 
                                    <a href="mailto:<?php echo esc_attr($mp_data['email']); ?>" class="text-parlament-blue hover:underline">
                                        <?php echo esc_html($mp_data['email']); ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($mp_data['mp_id'])) : ?>
                                <p class="mb-2"><span class="font-medium">ID posła:</span> <?php echo esc_html($mp_data['mp_id']); ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($mp_data['numberOfVotes'])) : ?>
                                <p class="mb-2"><span class="font-medium">Liczba głosów:</span> <?php echo esc_html(number_format($mp_data['numberOfVotes'], 0, ',', ' ')); ?></p>
                            <?php endif; ?>
                            
                            <?php if (isset($mp_data['active'])) : 
                                // Convert to boolean for comparison
                                $is_active = (bool)$mp_data['active'];
                            ?>
                                <p class="mb-2">
                                    <span class="font-medium">Status:</span> 
                                    <span class="<?php echo $is_active ? 'text-green-600' : 'text-red-600'; ?>">
                                        <?php echo $is_active ? 'Aktywny' : 'Nieaktywny'; ?>
                                    </span>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-8 border-t border-gray-200 pt-8">
                <?php if (get_the_content() || !empty($mp_data['biography'])) : ?>
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Biografia</h2>
                    
                    <div class="prose max-w-none text-gray-700">
                        <?php 
                        // Display the main content if available
                        if (get_the_content()) : 
                            the_content(); 
                        endif; 
                        
                        // Display additional biography field if available
                        if (!empty($mp_data['biography'])) : 
                            echo $mp_data['biography'];
                        endif; 
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="flex justify-between items-center">
                <a href="<?php echo get_post_type_archive_link('mp'); ?>" class="inline-flex items-center text-parlament-blue hover:underline">
                    <i class="fas fa-arrow-left mr-2"></i> Wróć do listy posłów
                </a>
                
                <div class="flex items-center space-x-3">
                    <span class="text-gray-600 text-sm">Udostępnij profil 
                    <?php if (!empty($mp_data['accusativeName'])) : ?>
                        <?php echo esc_html($mp_data['accusativeName']); ?></p>
                    <?php endif; ?>    
                    </span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-800">
                        <i class="fab fa-facebook-f"></i>
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
