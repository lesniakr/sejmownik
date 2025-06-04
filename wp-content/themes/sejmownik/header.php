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

<header>
    <div>
        <div>
            <div>
                <?php if (has_custom_logo()) : ?>
                    <div>
                        <?php
                        $custom_logo_id = get_theme_mod('custom_logo');
                        if ($custom_logo_id) {
                            $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                            if ($logo) {
                                echo '<a href="' . esc_url(home_url('/')) . '" rel="home">';
                                echo '<img src="' . esc_url($logo[0]) . '" width="250" alt="' . esc_attr(get_bloginfo('name')) . '">';
                                echo '</a>';
                            }
                        }
                        ?>
                    </div>
                <?php endif; ?>
                <div>
                    <?php $description = get_bloginfo('description', 'display'); ?>
                    <?php if ($description) : ?>
                        <p><?php echo $description; ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</header>

<div id="content" class="site-content">
