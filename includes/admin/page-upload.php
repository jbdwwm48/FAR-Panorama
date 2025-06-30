<?php

// -----------------------------------------------------------------------------
// Page d'ajout ou de mise à jour d'un panorama
// Cette page affiche soit la confirmation après upload,
// soit le formulaire d'upload avec gestion des mises à jour.
// -----------------------------------------------------------------------------
function far_panorama_upload_page()
{
    // Récupère l'ID du panorama uploadé ou mis à jour depuis l'URL (paramètres GET)
    $uploaded_id = intval($_GET['new_id'] ?? $_GET['update_id'] ?? 0);

    // Détermine si l'opération s'est bien passée et qu'on a un ID valide
    $has_success = isset($_GET['success']) && $uploaded_id > 0;

    // URL vers la page index.html du panorama pour la prévisualisation dans une iframe
    $upload_url = wp_upload_dir()['baseurl'] . '/panoramas/' . $uploaded_id . '/index.html';

    // Début du conteneur principal avec la classe WordPress "wrap"
    echo '<div class="wrap far-panorama-wrap">';

    // Indique si on est dans une mise à jour (update_id existe en GET)
    $is_update = isset($_GET['update_id']);

    // Affiche soit un message de succès, soit un titre selon le contexte
    if ($has_success) {
        // Message de succès dynamique selon ajout ou mise à jour
        echo '<div class="notice notice-success is-dismissible"><p><strong>Votre panorama a bien été ' . ($is_update ? 'mis à jour ✅' : 'ajouté ✅') . '.</strong></p></div>';
    } else {
        // Titre principal selon si c'est une mise à jour ou un ajout
        echo '<h1 class="far-panorama-title">' . ($is_update ? 'Mettez à jour votre panorama' : 'Téléverser un panorama') . '</h1>';
    }

    // Si upload réussi, on affiche la zone de prévisualisation + shortcode
    if ($has_success) {
        echo '<div class="panorama-upload-container">';

        // Section d'information sur le shortcode à utiliser
        echo '<div class="panorama-info">';
        echo '<h3>Utilisez ce shortcode dans vos pages ou articles :</h3>';
?>
        <!-- Zone cliquable pour copier le shortcode automatiquement -->
        <div class="shortcode-copy-wrapper" tabindex="0" role="button" aria-label="Copier le shortcode">
            <code id="shortcode-to-copy" class="shortcode-text" style="user-select: all; cursor: pointer;">
                <?php echo esc_html('[panorama id="' . $uploaded_id . '"]'); ?>
            </code>
            <!-- Icône SVG du bouton copier -->
            <svg id="copy-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="9" y="9" width="13" height="13" rx="2" ry="2" />
                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
            </svg>
        </div>

        <!-- Message dynamique accessible pour informer l'utilisateur -->
        <div id="copy-message" class="copy-message" aria-live="polite" role="alert"></div>

        <?php
        echo '<h4>Comment l’utiliser ?</h4>';
        echo '<p>Cliquez sur le shortcode ci-dessus pour le copier automatiquement.<br> Ensuite, collez-le dans n’importe quelle page, article ou bloc HTML de votre site. Le panorama s’affichera automatiquement.</p>';
        echo '<p>Assurez-vous que la page est en pleine largeur pour une meilleure expérience.</p>';

        echo '</div>'; // .panorama-info

        // Zone d’aperçu intégrée du panorama dans une iframe
        echo '<div class="panorama-preview">';
        echo '<iframe src="' . esc_url($upload_url) . '" allowfullscreen loading="lazy" referrerpolicy="no-referrer"></iframe>';
        echo '</div>'; // .panorama-preview

        echo '</div>'; // .panorama-upload-container
        ?>

        <script>
            // Script JS pour gérer la copie du shortcode au clic
            document.addEventListener('DOMContentLoaded', () => {
                const copyIcon = document.getElementById('copy-icon');
                const shortcode = document.getElementById('shortcode-to-copy');
                const message = document.getElementById('copy-message');
                if (!copyIcon || !shortcode || !message) return;

                function showMessage(text, color) {
                    message.textContent = text;
                    message.style.color = color;
                    message.style.opacity = '1';
                    setTimeout(() => {
                        message.style.opacity = '0';
                    }, 2500);
                }

                function copyText(text) {
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(text).then(() => {
                            showMessage('Shortcode copié !', 'green');
                        }).catch(() => fallbackCopy(text));
                    } else {
                        fallbackCopy(text);
                    }
                }

                function fallbackCopy(text) {
                    const textarea = document.createElement('textarea');
                    textarea.value = text;
                    textarea.style.position = 'fixed';
                    textarea.style.opacity = '0';
                    document.body.appendChild(textarea);
                    textarea.select();
                    try {
                        const successful = document.execCommand('copy');
                        showMessage(successful ? 'Shortcode copié !' : 'Copie échouée.', successful ? 'green' : 'red');
                    } catch {
                        showMessage('Erreur lors de la copie.', 'red');
                    }
                    document.body.removeChild(textarea);
                }

                shortcode.addEventListener('click', () => copyText(shortcode.textContent.trim()));
                copyIcon.addEventListener('click', () => copyText(shortcode.textContent.trim()));
                copyIcon.addEventListener('keydown', e => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        copyIcon.click();
                    }
                });
            });
        </script>

<?php
    } else {
        // Si pas de succès, affichage du formulaire d'upload simple

        echo '<form method="post" enctype="multipart/form-data" class="far-panorama-upload-form">';

        // Sécurité WordPress (nonce pour anti-CSRF)
        wp_nonce_field('far_panorama_upload', 'far_panorama_nonce');

        // Si mise à jour, ajout du champ caché avec l'ID à envoyer à panorama-handler.php
        if ($is_update) {
            echo '<input type="hidden" name="update_id" value="' . $uploaded_id . '">';
        }

        // Champ texte pour le titre personnalisé du panorama
        echo '<input type="text" name="panorama_title" placeholder="Titre du panorama" required class="far-panorama-title-input"><br>';

        // Champ fichier pour uploader une archive ZIP
        echo '<input type="file" name="panorama_zip" accept=".zip" required class="far-panorama-file-input"><br>';

        // Bouton de soumission avec texte dynamique selon ajout ou mise à jour
        echo '<button type="submit" name="submit_panorama" class="button button-primary far-panorama-upload-btn">';
        echo $is_update ? 'Mettre à jour le panorama' : 'Téléverser le panorama';
        echo '</button>';

        echo '</form>';
    }

    // Fin du conteneur principal
    echo '</div>'; // .wrap
}
