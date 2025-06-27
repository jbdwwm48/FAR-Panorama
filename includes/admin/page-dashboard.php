<?php

// -----------------------------------------------------------------------------
// Fonction principale qui génère la page "Mes Panoramas" (dashboard du plugin)
// -----------------------------------------------------------------------------
function far_panorama_dashboard_page()
{
    // URL de la page d’upload (utilisée par le bouton "Ajouter un panorama")
    $upload_url = admin_url('admin.php?page=far-panorama-upload');

    // URL de la liste des panoramas (utilisée par le bouton "Voir les panoramas")
    $list_url = admin_url('admin.php?page=far-panorama-list');

    // Lien vers l’outil officiel Marzipano (utilisé dans la doc visuelle)
    $marzipano_url = 'https://www.marzipano.net/tool/';
?>

    <!-- Conteneur principal de la page, centré et limité en largeur -->
    <div class="wrap" style="text-align: center; max-width: 900px; margin: 0 auto;">

        <!-- Titre de la page -->
        <h1 style="font-size: 2.4em; margin-bottom: 10px;">🎥 Bienvenue dans <strong>FAR-Panorama</strong></h1>

        <!-- Description courte avec lien vers Marzipano -->
        <p style="font-size: 1.15em; color: #444;">
            FAR-Panorama est un plugin WordPress qui permet d’intégrer facilement des panoramas 360° interactifs,
            en s’appuyant sur la technologie open-source <a href="<?php echo esc_url($marzipano_url); ?>" target="_blank" rel="noopener">Marzipano</a>.
        </p>

        <!-- Boutons principaux : accéder à la liste ou afficher le formulaire d'ajout -->
        <div style="margin-top: 30px; margin-bottom: 40px;">
            <a href="<?php echo esc_url($list_url); ?>" class="button button-primary" style="margin-right: 15px; padding: 12px 24px; font-size: 16px;">
                📋 Voir les panoramas
            </a>
            <button id="toggle-upload-form" class="button button-secondary" style="padding: 12px 24px; font-size: 16px;">
                ➕ Ajouter un panorama
            </button>
        </div>

        <!-- Séparateur visuel -->
        <hr style="margin: 40px 0;">

        <!-- Formulaire d’ajout de panorama (initialement masqué) -->
        <div id="upload-form-container" style="display: none; margin-top: 30px;">
            <?php
            // Gestion du mode "mise à jour" si un update_id est présent dans l’URL
            $update_id = intval($_GET['update_id'] ?? 0);
            $is_update = $update_id > 0;

            echo '<h2>Ajouter un panorama</h2>';

            // Formulaire d'upload
            echo '<form method="post" enctype="multipart/form-data" class="far-panorama-upload-form">';

            // Protection sécurité WordPress (anti-CSRF(Cross-Site Request Forgery))
            wp_nonce_field('far_panorama_upload', 'far_panorama_nonce');

            // Champ pour sélectionner une archive ZIP
            echo '<input type="file" name="panorama_zip" accept=".zip" required class="far-panorama-file-input"><br>';

            // Si mise à jour → champ caché pour passer l'ID à panorama-handler.php
            if ($is_update) {
                echo '<input type="hidden" name="update_id" value="' . esc_attr($update_id) . '">';
            }

            // Bouton de soumission (texte adaptatif selon le contexte)
            echo '<input type="submit" name="submit_panorama" class="button button-primary" value="' . ($is_update ? 'Mettre à jour' : 'Téléverser') . '">';

            echo '</form>';
            ?>
        </div>

        <!-- Section "Comment ça fonctionne ?" avec 3 étapes illustrées -->
        <h2 style="font-size: 1.8em; margin-bottom: 20px;">🚀 Comment ça fonctionne ?</h2>

        <div style="display: flex; justify-content: center; gap: 30px; flex-wrap: wrap; margin-bottom: 30px;">

            <!-- Étape 1 : Générer le panorama -->
            <div style="flex: 1; min-width: 240px; max-width: 260px;">
                <img src="<?php echo FAR_PANORAMA_URL . 'assets/img/tuto1.jpg'; ?>" alt="Étape 1" style="width: 100%; border-radius: 10px; border: 1px solid #ccc; box-shadow: 0 3px 6px rgba(0,0,0,0.1);">
                <p style="margin-top: 10px;"><strong>1. Générer un panorama</strong><br>depuis <a href="<?php echo esc_url($marzipano_url); ?>" target="_blank">Marzipano Tool</a></p>
            </div>

            <!-- Étape 2 : Importer le fichier ZIP -->
            <div style="flex: 1; min-width: 240px; max-width: 260px;">
                <img src="<?php echo FAR_PANORAMA_URL . 'assets/img/tuto2.jpg'; ?>" alt="Étape 2" style="width: 100%; border-radius: 10px; border: 1px solid #ccc; box-shadow: 0 3px 6px rgba(0,0,0,0.1);">
                <p style="margin-top: 10px;"><strong>2. Importer l’archive ZIP</strong><br>via l’interface d’ajout</p>
            </div>

            <!-- Étape 3 : Intégrer dans une page -->
            <div style="flex: 1; min-width: 240px; max-width: 260px;">
                <img src="<?php echo FAR_PANORAMA_URL . 'assets/img/tuto3.jpg'; ?>" alt="Étape 3" style="width: 100%; border-radius: 10px; border: 1px solid #ccc; box-shadow: 0 3px 6px rgba(0,0,0,0.1);">
                <p style="margin-top: 10px;"><strong>3. Intégrer le shortcode</strong><br>dans une page ou un article</p>
            </div>

        </div>

        <!-- Petit texte de fin explicatif -->
        <p style="font-style: italic; color: #666;">
            Une prévisualisation automatique s’affiche après l’import. Les panoramas peuvent ensuite être modifiés ou supprimés à tout moment.
        </p>

    </div>

<?php
}
