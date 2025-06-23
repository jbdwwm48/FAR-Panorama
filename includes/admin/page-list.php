<?php

/**
 * Affiche la page admin listant les panoramas de l'utilisateur connecté (ou tous pour les admins),
 * avec les colonnes : titre, auteur (login), compteur de vues, shortcode, actions.
 * Gère aussi l'affichage des messages de succès.
 */
function far_panorama_list_page()
{
    // Bloque l'accès si utilisateur non connecté
    if (!is_user_logged_in()) {
        wp_die('Tu dois être connecté pour voir cette page.');
    }

    // Récupère l'ID utilisateur courant et si c'est un admin
    $current_user_id = get_current_user_id();
    $is_admin = current_user_can('administrator');

    // Prépare les arguments de récupération des panoramas
    $args = [
        'post_type'   => 'panorama',
        'numberposts' => -1,
    ];

    // Si pas admin, limite la requête aux panoramas de l'utilisateur courant
    if (!$is_admin) {
        $args['author'] = $current_user_id;
    }

    // Récupère les panoramas selon les droits
    $panoramas = get_posts($args);

    // Début de l'affichage HTML
    echo '<div class="wrap"><h1>Mes Panoramas</h1>';

    // Messages de succès après suppression ou mise à jour
    if (isset($_GET['deleted'])) {
        echo '<div class="notice notice-success"><p>Panorama supprimé avec succès.</p></div>';
    }
    if (isset($_GET['updated'])) {
        echo '<div class="notice notice-success"><p>Panorama mis à jour.</p></div>';
    }

    // Si aucun panorama trouvé, propose d'en ajouter
    if (!$panoramas) {
        echo '<p>Aucun panorama. <a href="' . admin_url('admin.php?page=far-panorama-upload') . '">Ajouter un panorama</a>.</p></div>';
        return;
    }

    // Table des panoramas avec colonnes titre, auteur, vues, shortcode et actions
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr>';
    echo '<th>Titre</th>';
    echo '<th>Auteur</th>';  // Colonne auteur
    echo '<th>Vues</th>';    // Nouvelle colonne compteur vues
    echo '<th>Shortcode</th>';
    echo '<th>Actions</th>';
    echo '</tr></thead><tbody>';

    // Parcours chaque panorama pour afficher ses infos
    foreach ($panoramas as $p) {
        // Récupère les infos utilisateur de l'auteur du panorama
        $author = get_userdata($p->post_author);
        // Si utilisateur trouvé, récupère le login, sinon 'Inconnu'
        $author_login = $author ? $author->user_login : 'Inconnu';

        // Récupère le compteur de vues stocké en post meta (défaut 0)
        $views_count = intval(get_post_meta($p->ID, 'panorama_views', true));

        echo '<tr>';
        echo '<td>' . esc_html($p->post_title) . '</td>';
        echo '<td>' . esc_html($author_login) . '</td>';  // Affiche login auteur
        echo '<td>' . $views_count . '</td>';            // Affiche compteur vues
        echo '<td><code>[panorama id="' . $p->ID . '"]</code></td>';  // Shortcode à copier
        echo '<td>';
        // Bouton modifier redirige vers la page d'upload avec l'ID du panorama
        echo '<a class="button" href="' . admin_url('admin.php?page=far-panorama-upload&update_id=' . $p->ID) . '">Modifier</a> ';
        // Bouton supprimer avec nonce de sécurité et confirmation JS
        echo '<a class="button button-danger" href="'
            . wp_nonce_url(admin_url('admin.php?page=far-panorama-list&delete_id=' . $p->ID), 'far_panorama_delete_' . $p->ID)
            . '" onclick="return confirm(\'Confirmer la suppression ?\')">Supprimer</a>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody></table></div>';
}
