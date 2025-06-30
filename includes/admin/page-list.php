<?php

// -----------------------------------------------------------------------------
// Page d'affichage de la liste des panoramas pour l'utilisateur connecté
// -----------------------------------------------------------------------------
function far_panorama_list_page()
{
    if (!is_user_logged_in()) {
        wp_die('Tu dois être connecté pour voir cette page.');
    }

    $current_user_id = get_current_user_id();
    $is_admin = current_user_can('administrator');

    $args = [
        'post_type'   => 'panorama',
        'numberposts' => -1,
    ];

    if (!$is_admin) {
        $args['author'] = $current_user_id;
    }

    $panoramas = get_posts($args);

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

    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr>
            <th>Titre</th>
            <th>Auteur</th>
            <th>Créé le</th>
            <th>Mis à jour le</th>
            <th>Shortcode</th>
            <th style="min-width: 130px;">Actions</th>
        </tr></thead><tbody>';

    foreach ($panoramas as $p) {
        $author = get_userdata($p->post_author);
        $author_login = $author ? $author->user_login : 'Inconnu';

        $upload_dir = wp_upload_dir();
        $iframe_url = trailingslashit($upload_dir['baseurl']) . 'panoramas/' . $p->ID . '/index.html';

        echo '<tr>';
        echo '<td>' . esc_html($p->post_title) . '</td>';
        echo '<td>' . esc_html($author_login) . '</td>';
        echo '<td>' . esc_html(get_the_date('d/m/Y H:i', $p)) . '</td>';
        echo '<td>' . esc_html(get_the_modified_date('d/m/Y H:i', $p)) . '</td>';
        echo '<td><code class="copy-shortcode" data-id="' . $p->ID . '" title="Clique pour copier" style="cursor:pointer;">[panorama id="' . $p->ID . '"]</code></td>';

        echo '<td class="far-panorama-actions">';
        // Bouton Aperçu (œil fin)
        echo '<button type="button" class="button preview-button preview-panorama" aria-label="Aperçu" data-url="' . esc_url($iframe_url) . '">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" />
            <circle cx="12" cy="12" r="3" />
            </svg>
            </button> ';

        // Bouton Modifier (crayon fin)
        echo '<a class="button edit-button" href="' . admin_url('admin.php?page=far-panorama-upload&update_id=' . $p->ID) . '" aria-label="Modifier">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 20h9" />
            <path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4 12.5-12.5z" />
            </svg>
        </a> ';

        // Bouton Supprimer (poubelle fin)
        echo '<a class="button delete-button" href="'
            . wp_nonce_url(admin_url('admin.php?page=far-panorama-list&delete_id=' . $p->ID), 'far_panorama_delete_' . $p->ID)
            . '" onclick="return confirm(\'Confirmer la suppression ?\')" aria-label="Supprimer">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="3 6 5 6 21 6" />
            <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" />
            <path d="M10 11v6" />
            <path d="M14 11v6" />
            <path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2" />
            </svg>
        </a>';

        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody></table></div>';

    // Modale pour aperçu iframe
    echo '
    <div id="panoramaModal" style="display:none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <iframe id="panoramaIframe" src="" width="100%" height="600" style="border:none;"></iframe>
        </div>
    </div>';

    // JS gestion copie shortcode
    echo "
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const codes = document.querySelectorAll('.copy-shortcode');

        function showFeedback(code) {
            code.style.background = '#cce5ff';
            code.style.transition = 'background 0.3s ease';
            setTimeout(() => {
                code.style.background = '';
            }, 1000);
        }

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
