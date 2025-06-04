<?php
/**
 * The template for displaying the MPs archive
 */

get_header();
?>

<div>
    <h1>Członkowie Parlamentu</h1>
    
    <div>
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
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
        <?php the_posts_pagination(array(
            'mid_size' => 2,
            'prev_text' => '&laquo; Poprzednia',
            'next_text' => 'Następna &raquo;',
        )); ?>
    </div>
</div>

<?php get_footer(); ?>
