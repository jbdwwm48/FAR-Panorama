<?php
// Debug activé uniquement si non défini ailleurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!defined('WP_DEBUG')) define('WP_DEBUG', true);
if (!defined('WP_DEBUG_LOG')) define('WP_DEBUG_LOG', true);
if (!defined('WP_DEBUG_DISPLAY')) define('WP_DEBUG_DISPLAY', true);



// Upload ou mise à jour d’un panorama avec debug complet
add_action('admin_init', function () {
    error_log('--- Début traitement upload/mise à jour panorama ---');

    if (isset($_POST['submit_panorama'])) {
        error_log('submit_panorama détecté');

        // Vérification nonce
        if (!check_admin_referer('far_panorama_upload', 'far_panorama_nonce')) {
            error_log('Nonce invalide !');
            wp_die('Erreur de sécurité, nonce invalide.');
        }

        // Vérification fichier uploadé
        if (empty($_FILES['panorama_zip']['tmp_name'])) {
            error_log('Aucun fichier ZIP uploadé.');
            wp_die('Erreur : aucun fichier ZIP envoyé.');
        }

        // Gestion de l’upload
        require_once ABSPATH . 'wp-admin/includes/file.php';
        $upload = wp_handle_upload($_FILES['panorama_zip'], ['test_form' => false]);

        if (isset($upload['error'])) {
            error_log('Erreur upload : ' . $upload['error']);
            wp_die('Erreur d\'upload : ' . esc_html($upload['error']));
        }

        $update_id = intval($_POST['update_id'] ?? 0);
        error_log('update_id reçu : ' . $update_id);

        if ($update_id) {
            // Mise à jour : on garde le même post, on ne modifie rien côté WP post sauf nettoyage fichiers

            $post_id = $update_id;

            // Suppression de l’ancien dossier panorama pour écraser proprement
            $upload_dir = wp_upload_dir();
            $old_path = trailingslashit($upload_dir['basedir']) . 'panoramas/' . $update_id . '/';

            global $wp_filesystem;
            if (empty($wp_filesystem)) {
                WP_Filesystem();
            }
            $deleted = $wp_filesystem->delete($old_path, true);
            error_log('Ancien dossier supprimé ? ' . ($deleted ? 'Oui' : 'Non'));
        } else {
            // Création nouveau post panorama
            $post_id = wp_insert_post([
                'post_type' => 'panorama',
                'post_title' => 'Panorama ' . date('d/m/Y H:i'),
                'post_status' => 'publish',
            ]);
            error_log('Nouveau post inséré avec ID = ' . $post_id);
        }

        // Mise à jour du champ ACF avec le nouveau fichier ZIP uploadé
        update_field('panorama_zip', $upload, $post_id);

        // Appel de ta fonction perso pour dézipper et préparer les fichiers
        far_panorama_unzip_and_prepare($upload['file'], $post_id);
        error_log('ZIP décompressé et préparé pour post ID ' . $post_id);

        // Préparation et lancement redirection
        $url = admin_url('admin.php?page=far-panorama-upload&success=1');
        $url .= $update_id ? '&update_id=' . $update_id : '&new_id=' . $post_id;
        error_log('Redirection vers ' . $url);

        wp_redirect($url);
        exit;
    } else {
        error_log('submit_panorama NON détecté');
    }
});


// Suppression sécurisée d’un panorama avec debug
add_action('admin_init', function () {
    if (
        isset($_GET['delete_id']) &&
        isset($_GET['_wpnonce']) &&
        check_admin_referer('far_panorama_delete_' . $_GET['delete_id'])
    ) {
        $post_id = intval($_GET['delete_id']);
        $post = get_post($post_id);

        if (!$post || $post->post_type !== 'panorama') {
            error_log("Suppression : panorama invalide ID $post_id");
            wp_die('Panorama invalide.');
        }

        if (
            current_user_can('administrator') ||
            get_current_user_id() === intval($post->post_author)
        ) {
            // Log debugg
            error_log("Suppression : panorama ID " . $post->ID . " demandé par user " . get_current_user_id());

            $deleted = wp_delete_post($post_id, true);

            $upload_dir = wp_upload_dir();
            $path = trailingslashit($upload_dir['basedir']) . 'panoramas/' . $post_id . '/';

            require_once ABSPATH . 'wp-admin/includes/file.php';
            global $wp_filesystem;
            if (empty($wp_filesystem)) {
                WP_Filesystem();
            }
            $fs_deleted = $wp_filesystem->delete($path, true);
            error_log("Suppression : dossier panorama supprimé ? " . ($fs_deleted ? 'Oui' : 'Non'));

            wp_redirect(admin_url('admin.php?page=far-panorama-list&deleted=1'));
            exit;
        } else {
            error_log("Suppression : pas la permission utilisateur " . get_current_user_id());
            wp_die('Tu n\'as pas la permission de supprimer ce panorama.');
        }
    }
});
