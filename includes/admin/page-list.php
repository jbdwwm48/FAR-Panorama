<?php

// -----------------------------------------------------------------------------
// Page d'affichage de la liste des panoramas pour l'utilisateur connecté
// -----------------------------------------------------------------------------
function far_panorama_list_page()
{
    // Sécurité : l'utilisateur doit être connecté pour accéder à cette page
    if (!is_user_logged_in()) {
        wp_die('Tu dois être connecté pour voir cette page.');
    }

    // Récupère l'ID de l'utilisateur courant
    $current_user_id = get_current_user_id();

    // Vérifie si l'utilisateur est administrateur (droits élargis)
    $is_admin = current_user_can('administrator');

    // Prépare les arguments pour récupérer les panoramas
    $args = [
        'post_type'   => 'panorama', // On cible le CPT 'panorama'
        'numberposts' => -1,          // Sans limite (récupérer tous)
    ];

    // Si pas admin, on filtre pour ne récupérer que les panoramas de l'utilisateur courant
    if (!$is_admin) {
        $args['author'] = $current_user_id;
    }

    // Récupère les panoramas selon les arguments
    $panoramas = get_posts($args);

    // Début de la zone principale avec la classe WordPress "wrap"
    echo '<div class="wrap"><h1>Mes Panoramas</h1>';

    // Affiche un message de succès après suppression si paramètre 'deleted' présent dans l'URL
    if (isset($_GET['deleted'])) {
        echo '<div class="notice notice-success"><p>Panorama supprimé avec succès.</p></div>';
    }
    // Affiche un message de succès après mise à jour si paramètre 'updated' présent dans l'URL
    if (isset($_GET['updated'])) {
        echo '<div class="notice notice-success"><p>Panorama mis à jour.</p></div>';
    }

    // Si aucun panorama trouvé, on affiche un message invitant à en ajouter
    if (!$panoramas) {
        echo '<p>Aucun panorama. <a href="' . admin_url('admin.php?page=far-panorama-upload') . '">Ajouter un panorama</a>.</p></div>';
        return; // On sort de la fonction, rien d'autre à afficher
    }

    // Construction du tableau listant les panoramas
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr>
            <th>Titre</th>
            <th>Auteur</th>
            <th>Vues</th>
            <th>Shortcode</th>
            <th>Actions</th>
        </tr></thead><tbody>';

    // Parcours chaque panorama récupéré pour créer une ligne dans le tableau
    foreach ($panoramas as $p) {
        // Récupère les infos de l'auteur du post
        $author = get_userdata($p->post_author);
        $author_login = $author ? $author->user_login : 'Inconnu';

        // Récupère le nombre de vues stocké en meta post 'panorama_views'
        $views_count = intval(get_post_meta($p->ID, 'panorama_views', true));

        // Prépare l'URL du fichier index.html du panorama dans les uploads pour iframe preview
        $upload_dir = wp_upload_dir();
        $iframe_url = trailingslashit($upload_dir['baseurl']) . 'panoramas/' . $p->ID . '/index.html';

        echo '<tr>';

        // Affiche le titre du panorama
        echo '<td>' . esc_html($p->post_title) . '</td>';

        // Affiche le login de l'auteur
        echo '<td>' . esc_html($author_login) . '</td>';

        // Affiche le nombre de vues
        echo '<td>' . $views_count . '</td>';

        // Affiche le shortcode copiable avec attribut data-id pour script JS
        echo '<td><code class="copy-shortcode" data-id="' . $p->ID . '" title="Clique pour copier" style="cursor:pointer;">[panorama id="' . $p->ID . '"]</code></td>';

        // Colonne Actions avec boutons
        echo '<td class="far-panorama-actions">';
        // Bouton "Aperçu" qui déclenche une modale avec iframe du panorama
        echo '<button type="button" class="button preview-button preview-panorama" data-url="' . esc_url($iframe_url) . '">Aperçu</button> ';
        // Lien "Modifier" redirige vers la page upload avec param update_id pour modifier le panorama
        echo '<a class="button edit-button" href="' . admin_url('admin.php?page=far-panorama-upload&update_id=' . $p->ID) . '">Modifier</a> ';
        // Lien "Supprimer" avec nonce WordPress pour sécurité, et confirmation JS avant suppression
        echo '<a class="button delete-button" href="'
            . wp_nonce_url(admin_url('admin.php?page=far-panorama-list&delete_id=' . $p->ID), 'far_panorama_delete_' . $p->ID)
            . '" onclick="return confirm(\'Confirmer la suppression ?\')">Supprimer</a>';
        echo '</td>';

        echo '</tr>';
    }

    echo '</tbody></table></div>';

    // --------------------------
    //    Callbacks HTML et JS
    // --------------------------


    // HTML de la modale cachée qui affichera l'aperçu iframe du panorama
    echo '
    <div id="panoramaModal" style="display:none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <iframe id="panoramaIframe" src="" width="100%" height="600" style="border:none;"></iframe>
        </div>
    </div>';

    // Script JavaScript intégré pour gérer la copie du shortcode au clic
    echo "
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Récupère tous les éléments contenant le shortcode à copier
        const codes = document.querySelectorAll('.copy-shortcode');

        // Fonction visuelle pour indiquer que la copie a réussi (fond bleu temporaire)
        function showFeedback(code) {
            code.style.background = '#cce5ff';
            code.style.transition = 'background 0.3s ease';
            setTimeout(() => {
                code.style.background = '';
            }, 1000);
        }

        // Fonction fallback de copie (pour navigateurs sans Clipboard API)
        function fallbackCopy(text, code) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();

            try {
                const successful = document.execCommand('copy');
                if (successful) showFeedback(code);
                else alert('Erreur lors de la copie, copie manuelle nécessaire.');
            } catch {
                alert('Erreur lors de la copie, copie manuelle nécessaire.');
            }

            document.body.removeChild(textarea);
        }

        // Ajoute un événement clic sur chaque shortcode pour lancer la copie
        codes.forEach(function(code) {
            code.addEventListener('click', function () {
                const text = code.textContent.trim();
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(() => {
                        showFeedback(code);
                    }).catch(() => {
                        fallbackCopy(text, code);
                    });
                } else {
                    fallbackCopy(text, code);
                }
            });
        });
    });
    </script>
    ";
}
