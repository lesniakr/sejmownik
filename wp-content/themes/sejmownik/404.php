<?php
/**
 * The template for displaying 404 pages (not found)
 */

get_header();
?>

<div>
    <div>
        <h1>Błąd 404</h1>
        <h2>Strony nie znaleziono</h2>
        <div>
            <p>Strona, której szukasz, mogła zostać usunięta, zmieniono jej nazwę lub jest tymczasowo niedostępna.</p>
            
            <div>
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    Wróć do strony głównej
                </a>
            </div>
            
        </div>
    </div>
</div>

<?php get_footer(); ?>
