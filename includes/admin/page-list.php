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

    // 1. Initialisation variables GET / valeurs par défaut
    $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $posts_per_page = isset($_GET['posts_per_page']) ? intval($_GET['posts_per_page']) : 10;

    // 2. Construction des arguments WP_Query
    $args = [
        'post_type'      => 'panorama',
        'posts_per_page' => $posts_per_page,
        'paged'          => $paged,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];

    if (!$is_admin) {
        $args['author'] = $current_user_id;
    }

    if ($search) {
        $args['s'] = $search;
    }

    // 3. WP_Query pour paginer + filtrer
    $query = new WP_Query($args);

    echo '<h1 class="far-panorama-title">Mes Panoramas</h1>';

    if (isset($_GET['deleted'])) {
        echo '<div class="notice notice-success"><p>Panorama supprimé avec succès.</p></div>';
    }
    if (isset($_GET['updated'])) {
        echo '<div class="notice notice-success"><p>Panorama mis à jour.</p></div>';
    }

    // 4. Formulaire filtre + pagination + dropdown en haut
    echo '<form method="get" class="far-panorama-search-form">';
    echo '<input type="hidden" name="page" value="far-panorama-list">';

    // Recherche + bouton Filtrer + Reset (gauche)
    echo '<div class="search-left">';
    echo '<input type="search" name="search" placeholder="Rechercher par titre ou auteur..." value="' . esc_attr($search) . '" autocomplete="off">';
    echo '<button type="submit" class="button">Filtrer</button>';
    if ($search) {
        echo '<a href="' . admin_url('admin.php?page=far-panorama-list') . '" class="button button-reset">Supprimer les filtres</a>';
    }
    echo '</div>';

    // Dropdown + pagination (droite)
    echo '<div class="search-right">';
    echo '<div class="pagination-wrapper">';
    echo '<label class="pagination-label" for="posts_per_page">Afficher :</label>';
    echo '<select id="posts_per_page" name="posts_per_page" onchange="this.form.submit()">';
    foreach ([10, 25, 50] as $num) {
        $selected = ($posts_per_page === $num) ? 'selected' : '';
        echo "<option value=\"$num\" $selected>$num</option>";
    }
    echo '</select>';
    echo '<span class="pagination-label">par page</span>';
    echo '</div>';

    // Pagination custom avec type array
    $pagination_links = paginate_links([
        'base'      => add_query_arg('paged', '%#%'),
        'format'    => '',
        'current'   => $paged,
        'total'     => $query->max_num_pages,
        'prev_text' => '‹ Précédent',
        'next_text' => 'Suivant ›',
        'type'      => 'array',
    ]);

    if (!empty($pagination_links) && is_array($pagination_links)) {
        echo '<nav class="far-panorama-pagination" aria-label="Pagination des panoramas">';
        echo '<ul class="far-panorama-pagination-list">';
        foreach ($pagination_links as $link) {
            $active_class = strpos($link, 'current') !== false ? 'active' : '';
            echo '<li class="far-panorama-pagination-item ' . $active_class . '">' . $link . '</li>';
        }
        echo '</ul>';
        echo '</nav>';
    }

    echo '</div>'; // fin search-right
    echo '</form>';

    // 5. Vérifie si y'a des posts paginés à afficher
    if (!$query->have_posts()) {
        echo '<p>Aucun panorama. <a href="' . admin_url('admin.php?page=far-panorama-upload') . '">Ajouter un panorama</a>.</p>';
        return;
    }

    echo '<div class="far-panorama-table-container">';
    echo '<table class="wp-list-table widefat fixed striped far-panorama-table">';
    echo '<thead><tr>
            <th>Titre</th>
            <th>Auteur</th>
            <th>Créé le</th>
            <th>Mis à jour le</th>
            <th>Shortcode</th>
            <th style="min-width: 130px;">Actions</th>
        </tr></thead><tbody>';

    // 6. Boucle WP_Query
    while ($query->have_posts()) {
        $query->the_post();
        global $post;

        $author = get_userdata($post->post_author);
        $author_login = $author ? $author->user_login : 'Inconnu';

        $upload_dir = wp_upload_dir();
        $iframe_url = trailingslashit($upload_dir['baseurl']) . 'panoramas/' . $post->ID . '/index.html';

        echo '<tr>';
        echo '<td>' . esc_html(get_the_title()) . '</td>';
        echo '<td>' . esc_html($author_login) . '</td>';
        echo '<td>' . esc_html(get_the_date('d/m/Y H:i')) . '</td>';
        echo '<td>' . esc_html(get_the_modified_date('d/m/Y H:i')) . '</td>';
        echo '<td><code class="copy-shortcode" data-id="' . $post->ID . '" title="Clique pour copier" style="cursor:pointer;">[panorama id="' . $post->ID . '"]</code></td>';

        echo '<td class="far-panorama-actions">';
        // Bouton Aperçu (œil fin)
        echo '<button type="button" class="button preview-button preview-panorama" aria-label="Aperçu" data-url="' . esc_url($iframe_url) . '">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" />
            <circle cx="12" cy="12" r="3" />
            </svg>
            </button> ';

        // Bouton Modifier (crayon fin)
        echo '<a class="button edit-button" href="' . admin_url('admin.php?page=far-panorama-upload&update_id=' . $post->ID) . '" aria-label="Modifier">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 20h9" />
            <path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4 12.5-12.5z" />
            </svg>
        </a> ';

        // Bouton Supprimer (poubelle fin)
        echo '<a class="button delete-button" href="'
            . wp_nonce_url(admin_url('admin.php?page=far-panorama-list&delete_id=' . $post->ID), 'far_panorama_delete_' . $post->ID)
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
    wp_reset_postdata();

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

        codes.forEach(code => {
            code.addEventListener('click', () => {
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(code.textContent.trim()).then(() => {
                        showFeedback(code);
                    }).catch(() => {
                        fallbackCopy(code.textContent.trim(), code);
                    });
                } else {
                    fallbackCopy(code.textContent.trim(), code);
                }
            });
        });

        // Modale preview
        const modal = document.getElementById('panoramaModal');
        const iframe = document.getElementById('panoramaIframe');
        const closeBtn = modal.querySelector('.close');
        document.querySelectorAll('.preview-panorama').forEach(button => {
            button.addEventListener('click', () => {
                const url = button.getAttribute('data-url');
                iframe.src = url;
                modal.style.display = 'block';
            });
        });
        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
            iframe.src = '';
        });
        window.addEventListener('click', e => {
            if (e.target === modal) {
                modal.style.display = 'none';
                iframe.src = '';
            }
        });
    });
    </script>
    ";
}
