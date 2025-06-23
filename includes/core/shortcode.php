<?php

add_shortcode('panorama', function ($atts) {
    $post_id = intval($atts['id'] ?? 0);
    if (!$post_id) {
        return '<p>Panorama invalide.</p>';
    }

    // Incrément du compteur de vues
    $views = intval(get_post_meta($post_id, 'panorama_views', true));
    update_post_meta($post_id, 'panorama_views', $views + 1);

    // Chemin vers l’index.html du panorama
    $upload_dir = wp_upload_dir();
    $iframe_url = trailingslashit($upload_dir['baseurl']) . 'panoramas/' . $post_id . '/index.html';

    // Affichage du panorama dans un wrapper
    return '<div class="far-panorama-wrapper">' .
        '<iframe src="' . esc_url($iframe_url) . '" class="far-panorama-iframe" allowfullscreen loading="lazy" scrolling="no"></iframe>' .
        '</div>';
});
