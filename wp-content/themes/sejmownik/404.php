<?php
/**
 * The template for displaying 404 pages (not found)
 */

get_header();
?>

<div class="container mx-auto px-4 pb-12 pt-6">
    <div class="bg-white rounded-lg shadow-md p-8 mx-auto text-center">
        <div class="text-9xl font-bold text-parlament-blue mb-6">404</div>
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Strony nie znaleziono</h1>
        <div class="text-gray-600 mb-8">
            <p class="mb-4">Strona, której szukasz, mogła zostać usunięta, zmieniono jej nazwę lub jest tymczasowo niedostępna.</p>
            <div class="mt-8">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-flex items-center bg-parlament-red text-white py-3 px-6 rounded-md hover:bg-red-800 transition">
                    <i class="fas fa-home mr-2"></i> Wróć do strony głównej
                </a>
            </div>
        </div>
        
        <div class="mt-8 pt-8 border-t border-gray-200">
            <p class="text-gray-500 text-sm">Możesz również spróbować przejść do <a href="<?php echo esc_url(get_post_type_archive_link('mp')); ?>" class="text-parlament-blue hover:underline">listy posłów</a> lub skorzystać z wyszukiwarki w górnej części strony.</p>
        </div>
    </div>
</div>

<?php get_footer(); ?>
