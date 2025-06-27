<?php

// =============================================================================
// ACTIVATION DU DEBUG PHP (affichage des erreurs, logs...)
// =============================================================================

// Active les erreurs PHP dans l’admin pour le debug, seulement si ce n’est pas déjà défini ailleurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!defined('WP_DEBUG')) define('WP_DEBUG', true);
if (!defined('WP_DEBUG_LOG')) define('WP_DEBUG_LOG', true);
if (!defined('WP_DEBUG_DISPLAY')) define('WP_DEBUG_DISPLAY', true);



// =============================================================================
// TRAITEMENT UPLOAD OU MISE À JOUR DE PANORAMA
// =============================================================================
add_action('admin_init', function () {
    error_log('--- Début traitement upload/mise à jour panorama ---');

    // Vérifie qu’un formulaire a bien été soumis
    if (isset($_POST['submit_panorama'])) {
        error_log('submit_panorama détecté');

        // Vérifie le nonce WordPress pour éviter les attaques CSRF
        if (!check_admin_referer('far_panorama_upload', 'far_panorama_nonce')) {
            error_log('Nonce invalide !');
            wp_die('Erreur de sécurité, nonce invalide.');
        }

        // Vérifie qu’un fichier a bien été uploadé
        if (empty($_FILES['panorama_zip']['tmp_name'])) {
            error_log('Aucun fichier ZIP uploadé.');
            wp_die('Erreur : aucun fichier ZIP envoyé.');
        }

        // Envoie du fichier via l'API WP pour le stocker dans wp-content/uploads
        require_once ABSPATH . 'wp-admin/includes/file.php';
        $upload = wp_handle_upload($_FILES['panorama_zip'], ['test_form' => false]);

        // Gère les erreurs d’upload
        if (isset($upload['error'])) {
            error_log('Erreur upload : ' . $upload['error']);
            wp_die('Erreur d\'upload : ' . esc_html($upload['error']));
        }

        // Vérifie si c’est une mise à jour ou une création (grâce à update_id en hidden)
        $update_id = intval($_POST['update_id'] ?? 0);
        error_log('update_id reçu : ' . $update_id);

        if ($update_id) {
            // === MISE À JOUR D’UN PANORAMA EXISTANT ===
            $post_id = $update_id;

            // Supprime le dossier existant pour éviter de mélanger les fichiers
            $upload_dir = wp_upload_dir();
            $old_path = trailingslashit($upload_dir['basedir']) . 'panoramas/' . $update_id . '/';

            // Utilise WP_Filesystem pour supprimer proprement le dossier
            global $wp_filesystem;
            if (empty($wp_filesystem)) {
                WP_Filesystem();
            }

            $deleted = $wp_filesystem->delete($old_path, true);
            error_log('Ancien dossier supprimé ? ' . ($deleted ? 'Oui' : 'Non'));
        } else {
            // === CRÉATION D’UN NOUVEAU POST PANORAMA ===
            $post_id = wp_insert_post([
                'post_type'   => 'panorama',
                'post_title'  => 'Panorama ' . date('d/m/Y H:i'),
                'post_status' => 'publish',
            ]);
            error_log('Nouveau post inséré avec ID = ' . $post_id);
        }

        // Met à jour le champ ACF 'panorama_zip' avec les infos de l’upload
        update_field('panorama_zip', $upload, $post_id);

        // Décompression du ZIP + restructuration + injection du wrapper
        far_panorama_unzip_and_prepare($upload['file'], $post_id);
        error_log('ZIP décompressé et préparé pour post ID ' . $post_id);

        // Redirige vers la page upload avec succès
        $url = admin_url('admin.php?page=far-panorama-upload&success=1');
        $url .= $update_id ? '&update_id=' . $update_id : '&new_id=' . $post_id;
        error_log('Redirection vers ' . $url);

        wp_redirect($url);
        exit;
    } else {
        // Aucun POST détecté
        error_log('submit_panorama NON détecté');
    }
});



// =============================================================================
// SUPPRESSION SÉCURISÉE D’UN PANORAMA
// =============================================================================
add_action('admin_init', function () {
    // Vérifie que l’action de suppression a bien été demandée, avec un nonce valide
    if (
        isset($_GET['delete_id']) &&
        isset($_GET['_wpnonce']) &&
        check_admin_referer('far_panorama_delete_' . $_GET['delete_id'])
    ) {
        $post_id = intval($_GET['delete_id']);
        $post = get_post($post_id);

        // Vérifie que le post existe et que c’est bien un panorama
        if (!$post || $post->post_type !== 'panorama') {
            error_log("Suppression : panorama invalide ID $post_id");
            wp_die('Panorama invalide.');
        }

        // Autorise uniquement l’admin ou l’auteur du panorama à supprimer
        if (
            current_user_can('administrator') ||
            get_current_user_id() === intval($post->post_author)
        ) {
            error_log("Suppression : panorama ID " . $post->ID . " demandé par user " . get_current_user_id());

            // Supprime le post dans WordPress
            $deleted = wp_delete_post($post_id, true);

            // Supprime le dossier physique du panorama
            $upload_dir = wp_upload_dir();
            $path = trailingslashit($upload_dir['basedir']) . 'panoramas/' . $post_id . '/';

            require_once ABSPATH . 'wp-admin/includes/file.php';
            global $wp_filesystem;
            if (empty($wp_filesystem)) {
                WP_Filesystem();
            }
            $fs_deleted = $wp_filesystem->delete($path, true);
            error_log("Suppression : dossier panorama supprimé ? " . ($fs_deleted ? 'Oui' : 'Non'));

            // Redirection avec feedback dans l’UI
            wp_redirect(admin_url('admin.php?page=far-panorama-list&deleted=1'));
            exit;
        } else {
            // L'utilisateur n'a pas les droits pour supprimer
            error_log("Suppression : pas la permission utilisateur " . get_current_user_id());
            wp_die('Vous n\'avez pas la permission de supprimer ce panorama.');
        }
    }
});
