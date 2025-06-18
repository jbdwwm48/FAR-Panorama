<?php

if (!defined('ABSPATH')) exit;

/**
 * Retourne l'URL publique du index.html d'un panorama donné.
 */
function far_panorama_get_panorama_url($post_id)
{
    $upload_dir = wp_upload_dir();
    return trailingslashit($upload_dir['baseurl']) . 'panoramas/' . $post_id . '/index.html';
}

/**
 * Affiche une notice admin WordPress.
 */
function far_panorama_display_notice($message, $type = 'success')
{
    echo '<div class="notice notice-' . esc_attr($type) . ' is-dismissible"><p>' . esc_html($message) . '</p></div>';
}
