<?php

function far_panorama_upload_page()
{
    $uploaded_id = intval($_GET['new_id'] ?? $_GET['update_id'] ?? 0);
    $has_success = isset($_GET['success']) && $uploaded_id > 0;

    $upload_url = wp_upload_dir()['baseurl'] . '/panoramas/' . $uploaded_id . '/index.html';

    echo '<div class="wrap far-panorama-wrap">';

    $is_update = isset($_GET['update_id']);

    // On affiche soit la notice de succès, soit le titre, jamais les deux ensemble
    if ($has_success) {
        // Juste la notice
        echo '<div class="notice notice-success is-dismissible"><p><strong>✅ Votre panorama a bien été ' . ($is_update ? 'mis à jour' : 'ajouté') . '.</strong></p></div>';
    } else {
        // Juste le titre quand on n'a pas encore uploadé
        echo '<h1 class="far-panorama-title">' . ($is_update ? 'Mettez à jour votre panorama' : 'Téléverser un panorama') . '</h1>';
    }

    if ($has_success) {
        echo '<div class="panorama-upload-container">';

        echo '<div class="panorama-info">';
        echo '<h3>🎯 Utilisez ce shortcode dans vos pages ou articles :</h3>';

?>
        <div class="shortcode-copy-wrapper" tabindex="0" role="button" aria-label="Copier le shortcode">
            <code id="shortcode-to-copy" class="shortcode-text" style="user-select: all; cursor: pointer;">
                <?php echo esc_html('[panorama id="' . $uploaded_id . '"]'); ?>
            </code>

            <!-- 🧩 Ancien SVG restauré -->
            <svg id="copy-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="9" y="9" width="13" height="13" rx="2" ry="2" />
                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
            </svg>
        </div>

        <div id="copy-message" class="copy-message" aria-live="polite" role="alert"></div>

        <?php
        echo '<h4>📌 Comment l’utiliser ?</h4>';
        echo '<p>Cliquez sur le shortcode ci-dessus pour le copier automatiquement.<br> Ensuite, collez-le dans n’importe quelle page, article ou bloc HTML de votre site. Le panorama s’affichera automatiquement.</p>';
        echo '<p>⚠️ Assurez-vous que la page est en pleine largeur pour une meilleure expérience.</p>';

        echo '</div>'; // .panorama-info

        echo '<div class="panorama-preview">';
        echo '<iframe src="' . esc_url($upload_url) . '" allowfullscreen loading="lazy" referrerpolicy="no-referrer"></iframe>';
        echo '</div>'; // .panorama-preview

        echo '</div>'; // .panorama-upload-container
        ?>

        <script>
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
                            showMessage('✅ Shortcode copié !', 'green');
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
                        showMessage(successful ? '✅ Shortcode copié !' : '❌ Copie échouée.', successful ? 'green' : 'red');
                    } catch {
                        showMessage('❌ Erreur lors de la copie.', 'red');
                    }
                    document.body.removeChild(textarea);
                }

                // Listener sur le code aussi pour copier
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
        // Pas de message doublon ici, juste le formulaire
        echo '<form method="post" enctype="multipart/form-data" class="far-panorama-upload-form">';
        wp_nonce_field('far_panorama_upload', 'far_panorama_nonce');

        if ($is_update) {
            echo '<input type="hidden" name="update_id" value="' . $uploaded_id . '">';
        }

        echo '<input type="file" name="panorama_zip" accept=".zip" required class="far-panorama-file-input"><br>';
        echo '<button type="submit" name="submit_panorama" class="button button-primary far-panorama-upload-btn">';
        echo $is_update ? 'Mettre à jour le panorama' : 'Téléverser le panorama';
        echo '</button>';
        echo '</form>';
    }

    echo '</div>'; // .wrap
}
