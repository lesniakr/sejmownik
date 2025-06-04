<?php

/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 */
?>

</div><!-- #content -->

<footer>
    <div>
        <div>
            <h3>O stronie</h3>
            <p>Ta strona korzysta z danych API Sejmu w celu wyświetlania informacji o posłach.</p>
        </div>

        <div>
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. Realizacja: <a href="https://rafallesniak.com/" target="_blank">Rafał Leśniak</a></p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>

</body>

</html>