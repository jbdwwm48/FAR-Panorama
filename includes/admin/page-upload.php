<?php

// -----------------------------------------------------------------------------
// Page d'ajout ou de mise à jour d'un panorama avec formulaire stylé dashboard
// -----------------------------------------------------------------------------
function far_panorama_upload_page()
{
    $uploaded_id = intval($_GET['new_id'] ?? $_GET['update_id'] ?? 0);
    $is_update = isset($_GET['update_id']);
    $has_success = isset($_GET['success']) && $uploaded_id > 0;

    $upload_dir = wp_upload_dir();
    $panorama_exists = is_dir($upload_dir['basedir'] . '/panoramas/' . $uploaded_id);
    $upload_url = $upload_dir['baseurl'] . '/panoramas/' . $uploaded_id . '/index.html';

    echo '<div class="wrap far-panorama-wrap" style="max-width: 800px; margin: 0 auto; text-align: center;">';

    if ($has_success && $panorama_exists) {
        echo '<div class="notice notice-success is-dismissible"><p><strong>Votre panorama a bien été ' . ($is_update ? 'mis à jour ✅' : 'ajouté ✅') . '.</strong></p></div>';
        echo '<div class="panorama-upload-container">';

        echo '<div class="panorama-info">';
        echo '<h3>Utilisez ce shortcode dans vos pages ou articles :</h3>';
?>
        <div class="shortcode-copy-wrapper" tabindex="0" role="button" aria-label="Copier le shortcode" style="cursor:pointer; user-select:none;">
            <code id="shortcode-to-copy" class="shortcode-text" style="user-select: all;">
                <?php echo esc_html('[panorama id="' . $uploaded_id . '"]'); ?>
            </code>
            <svg id="copy-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="cursor:pointer; color: green; margin-left: 6px;">
                <rect x="9" y="9" width="13" height="13" rx="2" ry="2" />
                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1" />
            </svg>
        </div>
        <div id="copy-message" class="copy-message" aria-live="polite" role="alert" style="margin-top:8px; height: 1.2em; color: green; opacity: 0; transition: opacity 0.3s ease;"></div>
    <?php
        echo '<h4>Comment l’utiliser ?</h4>';
        echo '<p>Cliquez sur le shortcode ci-dessus pour le copier automatiquement.<br> Ensuite, collez-le dans n’importe quelle page, article ou bloc HTML de votre site WordPress.</p>';
        echo '<p>Astuce : utilisez une mise en page pleine largeur pour une meilleure expérience immersive.</p>';
        echo '</div>';

        echo '<div class="panorama-preview" style="margin-top: 30px;">';
        echo '<iframe src="' . esc_url($upload_url) . '" allowfullscreen loading="lazy" referrerpolicy="no-referrer" style="width:100%; height:400px; border:none; border-radius:8px;"></iframe>';
        echo '</div>';

        echo '</div>'; // .panorama-upload-container
    } else {
        echo '<h1 style="margin-bottom: 25px;">' . ($is_update ? 'Mettre à jour votre panorama' : 'Téléverser un panorama') . '</h1>';

        echo '<form method="post" enctype="multipart/form-data" class="far-panorama-upload-form upload-form-wrapper" style="max-width:600px; margin:0 auto; display:flex; flex-direction:column; align-items:center;">';

        wp_nonce_field('far_panorama_upload', 'far_panorama_nonce');

        if ($is_update) {
            echo '<input type="hidden" name="update_id" value="' . esc_attr($uploaded_id) . '">';
        }

        // Champ titre
        echo '<input type="text" name="panorama_title" placeholder="Saisissez un nouveau titre" required class="far-panorama-text-input" style="width:100%; max-width:400px; padding:10px; margin-bottom:25px; border-radius:6px; border:1px solid #ccc; text-align:center;">';

        // Champ fichier ZIP stylé corrigé sans chevauchement
        echo '<div class="custom-file-upload-wrapper" style="margin-bottom:30px; position:relative; width:100%; max-width:400px;">';

        // On cache totalement l’input avec clip + taille 1px (meilleure accessibilité et pas de chevauchement)
        echo '<input type="file" name="panorama_zip" id="panorama_zip" accept=".zip" ' . ($is_update ? '' : 'required') . ' class="far-panorama-file-input" ';
        echo 'style="position:absolute; width:1px; height:1px; padding:0; margin:0; overflow:hidden; clip:rect(0,0,0,0); border:0; cursor:pointer;" />';

        // Label visible, avec cursor:pointer pour le clic
        echo '<label for="panorama_zip" class="custom-file-upload-label" style="display:block; padding:12px 20px; background:#2271b1; color:white; border-radius:6px; font-weight:600; text-align:center; cursor:pointer; user-select:none;">Parcourir un fichier ZIP...</label>';

        echo '<span class="file-selected-text" style="display:block; margin-top:8px; font-style:italic; color:#555;">Aucun fichier sélectionné</span>';

        echo '</div>';

        // Submit
        echo '<button type="submit" name="submit_panorama" class="button button-primary far-panorama-upload-btn" style="padding:12px 30px; font-size:16px; border-radius:6px;">';
        echo $is_update ? 'Mettre à jour' : 'Téléverser';
        echo '</button>';

        echo '</form>';
    }

    // === Bouton retour en bas de page ===
    echo '<div style="text-align: center; margin-top: 60px;">';
    echo '<a href="' . admin_url('admin.php?page=far-panorama-dashboard') . '" class="button" style="padding: 10px 24px; font-size: 14px;">← Retour au tableau de bord</a>';
    echo '</div>';

    // === JS COPIE SHORTCODE ===
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
                if (navigator.clipboard) {
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
                    document.execCommand('copy');
                    showMessage('Shortcode copié !', 'green');
                } catch {
                    showMessage('Erreur lors de la copie.', 'red');
                }
                document.body.removeChild(textarea);
            }

            copyIcon.addEventListener('click', () => copyText(shortcode.textContent.trim()));
            shortcode.addEventListener('click', () => copyText(shortcode.textContent.trim()));
        });
    </script>
<?php

    echo '</div>'; // .wrap
}
