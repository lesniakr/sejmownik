<?php
/**
 * The main template file
 */

get_header();
?>

<div>
    <div>
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <div>
                    <?php if (has_post_thumbnail()) : ?>
                        <div>
                            <?php the_post_thumbnail('medium_large'); ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h2>
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h2>
                        <div>
                            <?php echo get_the_date(); ?> | <?php the_author(); ?>
                        </div>
                        <div>
                            <?php the_excerpt(); ?>
                        </div>
                        <a href="<?php the_permalink(); ?>">Read more</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <div>
                <p>No posts found.</p>
            </div>
        <?php endif; ?>
    </div>

    <div>
        <?php the_posts_pagination(array(
            'mid_size' => 2,
            'prev_text' => '&laquo; Previous',
            'next_text' => 'Next &raquo;',
        )); ?>
    </div>
</div>

<?php get_footer(); ?>
