<?php

/**
 * Affiche la page admin listant les panoramas de l’utilisateur connecté (ou tous pour les admins).
 * Le traitement de suppression est délégué au handler pour garder la séparation des responsabilités.
 */
function far_panorama_list_page()
{
    if (!is_user_logged_in()) {
        wp_die('Tu dois être connecté pour voir cette page.');
    }

    $current_user_id = get_current_user_id();
    $is_admin = current_user_can('administrator');

    // Récupérer les panoramas, tous pour admin, uniquement les siens sinon
    $args = [
        'post_type'   => 'panorama',
        'numberposts' => -1,
    ];
    if (!$is_admin) {
        $args['author'] = $current_user_id;
    }

    $panoramas = get_posts($args);

    echo '<div class="wrap"><h1>Mes Panoramas</h1>';

    // Affichage message succès suppression
    if (isset($_GET['deleted'])) {
        echo '<div class="notice notice-success"><p>Panorama supprimé avec succès.</p></div>';
    }
    if (isset($_GET['updated'])) {
        echo '<div class="notice notice-success"><p>Panorama mis à jour.</p></div>';
    }

    if (!$panoramas) {
        echo '<p>Aucun panorama. <a href="' . admin_url('admin.php?page=far-panorama-upload') . '">Ajouter un panorama</a>.</p></div>';
        return;
    }

    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Titre</th><th>Shortcode</th><th>Actions</th></tr></thead><tbody>';

    foreach ($panoramas as $p) {
        echo '<tr>';
        echo '<td>' . esc_html($p->post_title) . '</td>';
        echo '<td><code>[panorama id="' . $p->ID . '"]</code></td>';

        echo '<td>';
        echo '<a class="button" href="' . admin_url('admin.php?page=far-panorama-upload&update_id=' . $p->ID) . '">Modifier</a> ';
        echo '<a class="button button-danger" href="'
            . wp_nonce_url(admin_url('admin.php?page=far-panorama-list&delete_id=' . $p->ID), 'far_panorama_delete_' . $p->ID)
            . '" onclick="return confirm(\'Confirmer la suppression ?\')">Supprimer</a>';
        echo '</td>';

        echo '</tr>';
    }

    echo '</tbody></table></div>';
}
