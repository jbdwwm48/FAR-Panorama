<?php

add_shortcode('panorama', function ($atts) {
    $post_id = intval($atts['id'] ?? 0);
    if (!$post_id) return '<p>Panorama invalide.</p>';

    // Incrémenter compteur de vues (post meta 'panorama_views')
    $views = intval(get_post_meta($post_id, 'panorama_views', true));
    $views++;
    update_post_meta($post_id, 'panorama_views', $views);

    $upload_dir = wp_upload_dir();
    $url = trailingslashit($upload_dir['baseurl']) . 'panoramas/' . $post_id . '/index.html';

    return '<iframe src="' . esc_url($url) . '" width="100%" height="600" style="border:none;"></iframe>';
});
