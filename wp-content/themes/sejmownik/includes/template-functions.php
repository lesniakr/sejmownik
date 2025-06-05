<?php
/**
 * Template functions for Members of Parliament
 */

/**
 * Display MP photo with fallbacks
 * 
 * Checks for featured image, then API photo, then placeholder
 * 
 * @param int $post_id Post ID (optional, defaults to current post)
 * @param string $size Featured image size (small, medium, large, etc.)
 * @param string|array $attr Additional image attributes or CSS class as string
 * @return void
 */
function mp_display_photo($post_id = null, $size = 'medium', $attr = array()) {
    if (null === $post_id) {
        $post_id = get_the_ID();
    }
    
    // Handle string attributes (convert string to class attribute)
    if (is_string($attr) && !empty($attr)) {
        $attr = array('class' => $attr);
    } elseif (!is_array($attr)) {
        $attr = array();
    }
    
    // Add loading="lazy" to default attributes
    if (!isset($attr['loading'])) {
        $attr['loading'] = 'lazy';
    }
    
    // Check for featured image
    if (has_post_thumbnail($post_id)) {
        echo get_the_post_thumbnail($post_id, $size, $attr);
        return;
    }
    
    // No featured image, try API photo (only for MPs imported from API)
    $mp_id = get_field('mp_id', $post_id);
    
    // Check if this is a manually created MP (ID starts with 'm')
    $is_manual_mp = $mp_id && substr($mp_id, 0, 1) === 'm';
    
    if ($mp_id && !$is_manual_mp) {
        $photo_url = MP_API_Handler::get_mp_photo_url($mp_id);
        if ($photo_url) {
            // Default image attributes
            $default_attr = array(
                'src' => esc_url($photo_url),
                'alt' => get_the_title($post_id),
                'class' => 'mp-photo api-photo',
                'loading' => 'lazy',
            );
            
            // If attr has a class, append it to the default class
            if (isset($attr['class'])) {
                $default_attr['class'] .= ' ' . $attr['class'];
                unset($attr['class']);
            }
            
            // Merge with custom attributes
            $img_attr = array_merge($default_attr, $attr);
            
            // Build attributes string
            $attr_str = '';
            foreach ($img_attr as $name => $value) {
                $attr_str .= ' ' . $name . '="' . esc_attr($value) . '"';
            }
            
            echo '<img' . $attr_str . '>';
            return;
        }
    }
    
    // No photo available, use placeholder
    $default_attr = array(
        'src' => 'https://placehold.co/110x137/EEE/31343C',
        'alt' => get_the_title($post_id),
        'class' => 'mp-photo placeholder',
    );
    
    // If attr has a class, append it to the default class
    if (isset($attr['class'])) {
        $default_attr['class'] .= ' ' . $attr['class'];
        unset($attr['class']);
    }
    
    // Merge with custom attributes
    $img_attr = array_merge($default_attr, $attr);
    
    // Build attributes string
    $attr_str = '';
    foreach ($img_attr as $name => $value) {
        $attr_str .= ' ' . $name . '="' . esc_attr($value) . '"';
    }
    
    echo '<img' . $attr_str . '>';
}
