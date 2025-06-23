<?php

/**
 * Affiche la page admin listant les panoramas de l'utilisateur connecté (ou tous pour les admins),
 * avec les colonnes : titre, auteur (login), compteur de vues, shortcode, actions (aperçu/modifier/supprimer).
 * Gère aussi les messages de succès après action, et la modale d’aperçu.
 */
function far_panorama_list_page()
{
    // Sécurité : accès uniquement aux utilisateurs connectés
    if (!is_user_logged_in()) {
        wp_die('Tu dois être connecté pour voir cette page.');
    }

    // Récupère infos utilisateur courant
    $current_user_id = get_current_user_id();
    $is_admin = current_user_can('administrator');

    // Prépare la requête pour récupérer les panoramas
    $args = [
        'post_type'   => 'panorama',
        'numberposts' => -1,
    ];

    // Si pas admin, on ne récupère que les panoramas de l'utilisateur courant
    if (!$is_admin) {
        $args['author'] = $current_user_id;
    }

    $panoramas = get_posts($args);

    // Affiche l'en-tête de la page et les messages de statut
    echo '<div class="wrap"><h1>Mes Panoramas</h1>';

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

    // Génère le tableau HTML avec colonnes personnalisées
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr>
            <th>Titre</th>
            <th>Auteur</th>
            <th>Vues</th>
            <th>Shortcode</th>
            <th>Actions</th>
        </tr></thead><tbody>';

    foreach ($panoramas as $p) {
        // Récupère le login de l’auteur
        $author = get_userdata($p->post_author);
        $author_login = $author ? $author->user_login : 'Inconnu';

        // Récupère le compteur de vues stocké en post meta
        $views_count = intval(get_post_meta($p->ID, 'panorama_views', true));

        // Prépare l'URL vers le fichier index.html du panorama
        $upload_dir = wp_upload_dir();
        $iframe_url = trailingslashit($upload_dir['baseurl']) . 'panoramas/' . $p->ID . '/index.html';

        echo '<tr>';
        echo '<td>' . esc_html($p->post_title) . '</td>';
        echo '<td>' . esc_html($author_login) . '</td>';
        echo '<td>' . $views_count . '</td>';
        echo '<td><code>[panorama id="' . $p->ID . '"]</code></td>';

        // Boutons d'action : Aperçu / Modifier / Supprimer
        echo '<td class="far-panorama-actions">';
        // Aperçu
        echo '<button type="button" class="button preview-button preview-panorama" data-url="' . esc_url($iframe_url) . '">Aperçu</button> ';
        // Modifier
        echo '<a class="button edit-button" href="' . admin_url('admin.php?page=far-panorama-upload&update_id=' . $p->ID) . '">Modifier</a> ';
        // Supprimer avec nonce de sécurité
        echo '<a class="button delete-button" href="'
            . wp_nonce_url(admin_url('admin.php?page=far-panorama-list&delete_id=' . $p->ID), 'far_panorama_delete_' . $p->ID)
            . '" onclick="return confirm(\'Confirmer la suppression ?\')">Supprimer</a>';
        echo '</td>';

        echo '</tr>';
    }

    echo '</tbody></table></div>';

    // Structure HTML de la modale d’aperçu (invisible au départ)
    echo '
    <div id="panoramaModal" style="display:none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <iframe id="panoramaIframe" src="" width="100%" height="600" style="border:none;"></iframe>
        </div>
    </div>';
}
