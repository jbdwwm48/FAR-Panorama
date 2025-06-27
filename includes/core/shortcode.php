<?php

// =============================================================================
// SHORTCODE [panorama id="X"] → affiche le panorama dans une iframe responsive
// =============================================================================

// Déclare le shortcode [panorama id="X"]
add_shortcode('panorama', function ($atts) {

    // Récupère et sécurise l’ID passé dans le shortcode
    $post_id = intval($atts['id'] ?? 0);

    // Si aucun ID n’est fourni, on affiche un message d’erreur
    if (!$post_id) return '<p>Panorama invalide.</p>';

    // Incrémente un compteur de vues (métadonnée personnalisée 'panorama_views')
    $views = intval(get_post_meta($post_id, 'panorama_views', true));
    update_post_meta($post_id, 'panorama_views', $views + 1);

    // Génère l’URL vers le fichier index.html du panorama
    $upload_dir = wp_upload_dir();
    $iframe_url = trailingslashit($upload_dir['baseurl']) . 'panoramas/' . $post_id . '/index.html';

    // Retourne le HTML : une iframe responsive avec wrapper
    return '
    <div class="far-panorama-wrapper">
        <iframe src="' . esc_url($iframe_url) . '" 
                allowfullscreen 
                loading="lazy"
                referrerpolicy="no-referrer"
                class="far-panorama-iframe">
        </iframe>
    </div>';
});
