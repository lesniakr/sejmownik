<?php
/**
 * The template for displaying all single posts and pages
 */

get_header();
?>

<div>
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <?php if (has_post_thumbnail() && !is_page()) : ?>
                <div>
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>
            
            <div>
                <header>
                    <h1><?php the_title(); ?></h1>
                    
                    <?php if (!is_page()) : ?>
                        <div>
                            <?php echo get_the_date(); ?> | <?php the_author(); ?>
                            <?php if (has_category()) : ?>
                                | Kategorie: <?php the_category(', '); ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </header>

                <div>
                    <?php the_content(); ?>
                </div>
                
                <?php if (has_tag() && !is_page()) : ?>
                    <footer>
                        <div>
                            <span>Tagi:</span> <?php the_tags('', ', ', ''); ?>
                        </div>
                    </footer>
                <?php endif; ?>
            </div>
            
            <?php if (!is_page()) : ?>
                <div>
                    <div>
                        <div>
                            <?php previous_post_link('%link', '&laquo; %title'); ?>
                        </div>
                        <div>
                            <?php next_post_link('%link', '%title &raquo;'); ?>
                        </div>
                    </div>
                </div>
                
                <?php if (comments_open() || get_comments_number()) : ?>
                    <div>
                        <?php comments_template(); ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </article>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
