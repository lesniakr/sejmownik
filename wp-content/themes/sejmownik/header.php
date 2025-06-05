<?php
/**
 * The header for theme
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php wp_head(); ?>
</head>

<body <?php body_class('font-montserrat'); ?>>
<?php wp_body_open(); ?>

<header class="parlament-gradient shadow-lg">
    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="flex items-center mb-4 md:mb-0">
                <?php if (has_custom_logo()) : 
                    $custom_logo_id = get_theme_mod('custom_logo');
                    $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                ?>
                    <div class="bg-white p-2 rounded-full mr-4">
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                            <img src="<?php echo esc_url($logo[0]); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" class="w-12 h-12">
                        </a>
                    </div>
                <?php endif; ?>
                
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white"><?php bloginfo('name'); ?></h1>
                    <?php $description = get_bloginfo('description', 'display'); ?>
                    <?php if ($description) : ?>
                        <p class="text-white opacity-80"><?php echo $description; ?></p>
                    <?php else: ?>
                        <p class="text-white opacity-80">Lista posłów na Sejm RP</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="relative">
                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                    <input type="text" name="s" placeholder="Wyszukaj posła..." 
                           class="pl-10 pr-4 py-2 rounded-full w-full md:w-64 focus:outline-none focus:ring-2 focus:ring-parlament-gold">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </form>
            </div>
        </div>
    </div>
</header>

<div id="content" class="site-content">
