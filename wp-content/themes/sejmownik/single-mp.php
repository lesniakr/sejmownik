<?php
/**
 * The template for displaying single MP pages
 */

get_header();
?>

<div>
    <article>
        <div>
            <div>
                <div>
                    <?php mp_display_photo(null, 'large'); ?>
                </div>
            </div>
            
            <div>
                <h1><?php the_title(); ?></h1>
                
                <div>
                    <div>
                        <p><strong>Partia:</strong> <?php echo esc_html(get_field('club')); ?></p>
                        
                        <?php if (get_field('district')) : ?>
                            <p><strong>Okręg wyborczy:</strong> <?php echo esc_html(get_field('district')); ?> (nr <?php echo esc_html(get_field('district_number')); ?>)</p>
                        <?php endif; ?>
                        
                        <?php if (get_field('voivodeship')) : ?>
                            <p><strong>Województwo:</strong> <?php echo esc_html(get_field('voivodeship')); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <?php if (get_field('email')) : ?>
                            <p>
                                <strong>E-mail:</strong> 
                                <a href="mailto:<?php echo esc_attr(get_field('email')); ?>">
                                    <?php echo esc_html(get_field('email')); ?>
                                </a>
                            </p>
                        <?php endif; ?>
                        
                        <p><strong>ID posła:</strong> <?php echo esc_html(get_field('mp_id')); ?></p>
                    </div>
                </div>
                
                <?php if (get_the_content()) : ?>
                    <div>
                        <h2>Biografia</h2>
                        <?php the_content(); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (get_field('biography')) : ?>
                    <div>
                        <?php echo get_field('biography'); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div>
            <div>
                <a href="<?php echo get_post_type_archive_link('mp'); ?>">
                    Wróć do listy posłów
                </a>
            </div>
        </div>
    </article>
</div>

<?php get_footer(); ?>
