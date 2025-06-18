<?php

function far_panorama_upload_page()
{
    $update_id = intval($_GET['update_id'] ?? 0);
    $is_update = $update_id > 0;

    $uploaded_id = $update_id ?: intval($_GET['new_id'] ?? 0);
    $has_success = isset($_GET['success']) && $uploaded_id > 0;

    $upload_url = wp_upload_dir()['baseurl'] . '/panoramas/' . $uploaded_id . '/index.html';

    echo '<div class="wrap far-panorama-wrap">';

    echo '<h1 class="far-panorama-title">' . ($has_success ? 'Voici votre panorama prêt à être utilisé' : 'Téléverser un panorama') . '</h1>';

    if ($has_success) {
        echo '<div class="panorama-upload-container">';

        echo '<div class="panorama-info">';

        echo '<h3>🎯 Utilisez ce shortcode dans vos pages ou articles :</h3>';
        echo '<code class="shortcode-copy" title="Cliquer pour copier" onclick="navigator.clipboard.writeText(this.textContent)">' . esc_html('[panorama id="' . $uploaded_id . '"]') . '</code>';

        echo '<h4>📌 Comment l’utiliser ?</h4>';
        echo '<p>Collez le shortcode ci-dessus dans n’importe quelle page, article ou bloc HTML de votre site. Le panorama s’affichera automatiquement.</p>';
        echo '<p>⚠️ Assurez-vous que la page est en pleine largeur pour une meilleure expérience.</p>';

        echo '</div>';

        echo '<div class="panorama-preview">';
        echo '<iframe src="' . esc_url($upload_url) . '" allowfullscreen loading="lazy" referrerpolicy="no-referrer"></iframe>';
        echo '</div>';

        echo '</div>'; // .panorama-upload-container
    } else {
        echo '<div class="far-panorama-tutorial">';
        echo '<h2>Comment ça marche ?</h2>';
        echo '<p>1. Préparez un fichier ZIP généré avec <strong>Marzipano Tool</strong> contenant votre scène 360°.</p>';
        echo '<p>2. Cliquez sur "Parcourir", sélectionnez votre ZIP.</p>';
        echo '<p>3. Cliquez sur "Téléverser" et laissez WordPress faire sa magie !</p>';
        echo '</div>';

        echo '<form method="post" enctype="multipart/form-data" class="far-panorama-upload-form">';
        wp_nonce_field('far_panorama_upload', 'far_panorama_nonce');
        echo '<input type="file" name="panorama_zip" accept=".zip" required class="far-panorama-file-input"><br>';
        if ($is_update) {
            echo '<input type="hidden" name="update_id" value="' . esc_attr($update_id) . '">';
        }
        echo '<input type="submit" name="submit_panorama" class="button button-primary button-hero" value="' . ($is_update ? 'Mettre à jour' : 'Téléverser') . '">';
        echo '</form>';
    }

    echo '</div>'; // .wrap
}
