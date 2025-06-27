<?php

// =============================================================================
// GESTION DE L'EXTRACTION ET PRÉPARATION DU DOSSIER PANORAMA
// =============================================================================

/**
 * Extrait une archive ZIP Marzipano dans un dossier uploads/panoramas/{post_id}
 * Réorganise les fichiers pour les rendre directement utilisables
 * Injecte le fichier wrapper index.html du plugin pour afficher l’iframe
 *
 * @param string $zip_path Chemin du fichier ZIP uploadé
 * @param int    $post_id  ID du post WordPress de type 'panorama'
 */
function far_panorama_unzip_and_prepare($zip_path, $post_id)
{
    // Récupère le chemin absolu vers le dossier uploads de WP
    $upload_dir = wp_upload_dir();

    // Définit le dossier cible final : uploads/panoramas/{post_id}/
    $dest_dir = trailingslashit($upload_dir['basedir']) . 'panoramas/' . $post_id . '/';

    // Crée le dossier s’il n’existe pas encore
    if (!file_exists($dest_dir)) {
        wp_mkdir_p($dest_dir);
    }

    // Ouvre l’archive ZIP avec la classe native de PHP
    $zip = new ZipArchive;
    if ($zip->open($zip_path) === TRUE) {

        // Extraction du contenu vers le dossier de destination
        $zip->extractTo($dest_dir);
        $zip->close();

        // Marzipano exporte souvent les fichiers dans un sous-dossier 'app-files/'
        $app_files = $dest_dir . 'app-files/';
        if (is_dir($app_files)) {
            // On déplace tous les fichiers de 'app-files/' à la racine du panorama
            foreach (scandir($app_files) as $file) {
                if ($file === '.' || $file === '..') continue;
                rename($app_files . $file, $dest_dir . $file);
            }
            // Et on supprime le dossier vide
            rmdir($app_files);
        }

        // On renomme l’index.html original de Marzipano en panorama.html
        // (Il sera ensuite affiché dans une iframe depuis un wrapper personnalisé)
        if (file_exists($dest_dir . 'index.html')) {
            rename($dest_dir . 'index.html', $dest_dir . 'panorama.html');
        }

        // Prépare le chemin du fichier wrapper HTML fourni par le plugin
        // Ce fichier joue le rôle de conteneur pour l’iframe
        $plugin_root = dirname(dirname(dirname(__FILE__))); // remonte jusqu’à la racine du plugin
        $wrapper_src = $plugin_root . '/panorama-wrapper/index.html';
        $wrapper_dst = $dest_dir . 'index.html';

        // On copie ce fichier index.html dans le dossier panorama pour qu’il soit affiché publiquement
        if (file_exists($wrapper_src)) {
            copy($wrapper_src, $wrapper_dst);
        } else {
            // Petit log si jamais le wrapper est absent (bug potentiel)
            error_log("Wrapper manquant : " . $wrapper_src);
        }
    }
}
