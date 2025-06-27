<?php

// Empêche l'accès direct au fichier depuis l'URL
if (!defined('ABSPATH')) exit;

// =============================================================================
// Fonction utilitaire : URL publique d'un panorama
// =============================================================================

/**
 * Retourne l'URL absolue du fichier `index.html` pour un panorama donné,
 * en se basant sur l’ID du post.
 *
 * @param int $post_id L’ID du post de type panorama.
 * @return string L’URL publique du fichier index.html généré.
 */
function far_panorama_get_panorama_url($post_id)
{
    // On récupère les infos du dossier uploads de WordPress
    $upload_dir = wp_upload_dir();

    // On retourne l’URL complète vers le fichier index.html du panorama
    // Ex: https://monsite.com/wp-content/uploads/panoramas/123/index.html
    return trailingslashit($upload_dir['baseurl']) . 'panoramas/' . $post_id . '/index.html';
}

// =============================================================================
// Fonction utilitaire : afficher une notice dans l'admin WordPress
// =============================================================================

/**
 * Affiche un message dans le back-office de WordPress.
 *
 * @param string $message Le texte à afficher.
 * @param string $type Le type de message (success, error, warning, info).
 */
function far_panorama_display_notice($message, $type = 'success')
{
    // Génère une div admin WordPress avec le message
    // Elle est fermable automatiquement grâce à la classe "is-dismissible"
    echo '<div class="notice notice-' . esc_attr($type) . ' is-dismissible"><p>' . esc_html($message) . '</p></div>';
}
