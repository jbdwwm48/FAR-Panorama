<?php

function far_panorama_upload_page()
{
    $uploaded_id = intval($_GET['new_id'] ?? 0);
    $has_success = isset($_GET['success']) && $uploaded_id > 0;

    $upload_url = wp_upload_dir()['baseurl'] . '/panoramas/' . $uploaded_id . '/index.html';

    echo '<div class="wrap far-panorama-wrap">';

    echo '<h1 class="far-panorama-title">' . ($has_success ? 'Voici votre panorama prêt à être utilisé' : 'Téléverser un panorama') . '</h1>';

    if ($has_success) {
        echo '<div class="panorama-upload-container">';

        echo '<div class="panorama-info">';

        echo '<h3>🎯 Utilisez ce shortcode dans vos pages ou articles :</h3>';

?>
        <div class="shortcode-copy-wrapper" tabindex="0" role="button" aria-label="Copier le shortcode">
            <code id="shortcode-to-copy" class="shortcode-text" style="user-select: all; cursor: pointer;">
                <?php echo esc_html('[panorama id="' . $uploaded_id . '"]'); ?>
            </code>

            <svg id="copy-icon" class="copy-icon" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 115.77 122.88" width="14" height="14" aria-label="Copier le shortcode" role="button" tabindex="0" style="flex-shrink:0;">
                <style type="text/css">
                    .st0 {
                        fill-rule: evenodd;
                        clip-rule: evenodd;
                    }
                </style>
                <g>
                    <path class="st0" d="M89.62,13.96v7.73h12.19h0.01v0.02c3.85,0.01,7.34,1.57,9.86,4.1c2.5,2.51,4.06,5.98,4.07,9.82h0.02v0.02
                        v73.27v0.01h-0.02c-0.01,3.84-1.57,7.33-4.1,9.86c-2.51,2.5-5.98,4.06-9.82,4.07v0.02h-0.02h-61.7H40.1v-0.02
                        c-3.84-0.01-7.34-1.57-9.86-4.1c-2.5-2.51-4.06-5.98-4.07-9.82h-0.02v-0.02V92.51H13.96h-0.01v-0.02c-3.84-0.01-7.34-1.57-9.86-4.1
                        c-2.5-2.51-4.06-5.98-4.07-9.82H0v-0.02V13.96v-0.01h0.02c0.01-3.85,1.58-7.34,4.1-9.86c2.51-2.5,5.98-4.06,9.82-4.07V0h0.02h61.7
                        h0.01v0.02c3.85,0.01,7.34,1.57,9.86,4.1c2.5,2.51,4.06,5.98,4.07,9.82h0.02V13.96L89.62,13.96z M79.04,21.69v-7.73v-0.02h0.02
                        c0-0.91-0.39-1.75-1.01-2.37c-0.61-0.61-1.46-1-2.37-1v0.02h-0.01h-61.7h-0.02v-0.02c-0.91,0-1.75,0.39-2.37,1.01
                        c-0.61,0.61-1,1.46-1,2.37h0.02v0.01v64.59v0.02h-0.02c0,0.91,0.39,1.75,1.01,2.37c0.61,0.61,1.46,1,2.37,1v-0.02h0.01h12.19V35.65
                        v-0.01h0.02c0.01-3.85,1.58-7.34,4.1-9.86c2.51-2.5,5.98-4.06,9.82-4.07v-0.02h0.02H79.04L79.04,21.69z M105.18,108.92V35.65v-0.02
                        h0.02c0-0.91-0.39-1.75-1.01-2.37c-0.61-0.61-1.46-1-2.37-1v0.02h-0.01h-61.7h-0.02v-0.02c-0.91,0-1.75,0.39-2.37,1.01
                        c-0.61,0.61-1,1.46-1,2.37h0.02v0.01v73.27v0.02h-0.02c0,0.91,0.39,1.75,1.01,2.37c0.61,0.61,1.46,1,2.37,1v-0.02h0.01h61.7h0.02
                        v0.02c0.91,0,1.75-0.39,2.37-1.01c0.61-0.61,1-1.46,1-2.37h-0.02V108.92L105.18,108.92z" />
                </g>
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
                        }).catch(() => {
                            fallbackCopy(text);
                        });
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
                        if (successful) {
                            showMessage('✅ Shortcode copié !', 'green');
                        } else {
                            showMessage('❌ Erreur lors de la copie, copie manuelle requise.', 'red');
                        }
                    } catch {
                        showMessage('❌ Erreur lors de la copie, copie manuelle requise.', 'red');
                    }

                    document.body.removeChild(textarea);
                }

                shortcode.addEventListener('click', () => copyText(shortcode.textContent.trim()));
                copyIcon.addEventListener('click', () => copyText(shortcode.textContent.trim()));

                copyIcon.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        copyIcon.click();
                    }
                });
            });
        </script>

<?php
    } else {
        // Formulaire upload simple
        echo '<form method="post" enctype="multipart/form-data" class="far-panorama-upload-form">';
        wp_nonce_field('far_panorama_upload', 'far_panorama_nonce');
        echo '<input type="file" name="panorama_zip" accept=".zip" required class="far-panorama-file-input"><br>';
        echo '<button type="submit" class="button button-primary far-panorama-upload-btn">Téléverser le panorama</button>';
        echo '</form>';
    }

    echo '</div>'; // .wrap
}
