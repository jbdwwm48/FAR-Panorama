<?php
// Upload ou mise à jour d’un panorama
add_action('admin_init', function () {
    if (isset($_POST['submit_panorama']) && check_admin_referer('far_panorama_upload', 'far_panorama_nonce')) {
        if (!empty($_FILES['panorama_zip']['tmp_name'])) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            $upload = wp_handle_upload($_FILES['panorama_zip'], ['test_form' => false]);

            if (!isset($upload['error'])) {
                $update_id = intval($_POST['update_id'] ?? 0);
                $post_id = $update_id ?: wp_insert_post([
                    'post_type' => 'panorama',
                    'post_title' => 'Panorama ' . date('d/m/Y H:i'),
                    'post_status' => 'publish',
                ]);

                if ($update_id) {
                    wp_update_post(['ID' => $update_id]);
                }

                update_field('panorama_zip', $upload, $post_id);
                far_panorama_unzip_and_prepare($upload['file'], $post_id);

                $url = admin_url('admin.php?page=far-panorama-upload&success=1');
                $url .= $update_id ? '&update_id=' . $update_id : '&new_id=' . $post_id;
                wp_redirect($url);
                exit;
            }
        }
    }
});

// Suppression sécurisée d’un panorama
add_action('admin_init', function () {
    // Vérifier si suppression demandée avec nonce valide
    if (
        isset($_GET['delete_id']) &&
        isset($_GET['_wpnonce']) &&
        check_admin_referer('far_panorama_delete_' . $_GET['delete_id'])
    ) {
        $post_id = intval($_GET['delete_id']);
        $post = get_post($post_id);

        // Vérifier que le post existe et est bien un panorama
        if (!$post || $post->post_type !== 'panorama') {
            wp_die('Panorama invalide.');
        }

        // Vérifier que l'utilisateur est admin ou auteur du panorama
        if (
            current_user_can('administrator') ||
            get_current_user_id() === intval($post->post_author)
        ) {
            // Suppression définitive
            wp_delete_post($post_id, true);

            // Supprimer aussi les fichiers liés au panorama
            $upload_dir = wp_upload_dir();
            $path = trailingslashit($upload_dir['basedir']) . 'panoramas/' . $post_id . '/';

            require_once ABSPATH . 'wp-admin/includes/file.php';
            global $wp_filesystem;
            if (empty($wp_filesystem)) {
                WP_Filesystem();
            }
            $wp_filesystem->delete($path, true);

            // Redirection avec message succès
            wp_redirect(admin_url('admin.php?page=far-panorama-list&deleted=1'));
            exit;
        } else {
            wp_die('Tu n\'as pas la permission de supprimer ce panorama.');
        }
    }
});
