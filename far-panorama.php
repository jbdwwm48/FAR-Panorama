<?php

/**
 * Plugin Name: FAR-Panorama
 * Description: Gestion simplifiée de panoramas 360° Marzipano dans WordPress.
 * Version: 1.0.0
 * Author: Nycalith (JB)
 * License: GPL2
 */

if (!defined('ABSPATH')) exit;

// 1. Vérifie que ACF est activé
function far_panorama_check_acf_active()
{
    if (!class_exists('ACF')) {
        deactivate_plugins(plugin_basename(__FILE__));
        add_action('admin_notices', function () {
            echo '<div class="error"><p><strong>FAR-Panorama :</strong> Le plugin ACF est requis.</p></div>';
        });
    }
}
register_activation_hook(__FILE__, 'far_panorama_check_acf_active');

add_action('admin_notices', function () {
    if (!class_exists('ACF')) {
        echo '<div class="error"><p><strong>FAR-Panorama :</strong> Activez ACF pour que le plugin fonctionne.</p></div>';
    }
});

// 2. Enregistrement du CPT
add_action('init', function () {
    register_post_type('panorama', [
        'labels' => [
            'name' => 'Panoramas',
            'singular_name' => 'Panorama',
            'menu_name' => 'Mes Panoramas'
        ],
        'public' => false,
        'show_ui' => false,
        'show_in_rest' => true,
        'supports' => ['title'],
    ]);
});

// 3. Ajout menu admin
add_action('admin_menu', function () {
    add_menu_page('Mes Panoramas', 'Mes Panoramas', 'publish_posts', 'far-panorama-list', 'far_panorama_list_page', 'dashicons-format-image');
    add_submenu_page('far-panorama-list', 'Mes Panoramas', 'Liste', 'publish_posts', 'far-panorama-list', 'far_panorama_list_page');
    add_submenu_page('far-panorama-list', 'Ajouter un panorama', 'Ajouter', 'publish_posts', 'far-panorama-upload', 'far_panorama_upload_page');
});

// 4. Traitement : Upload / Update
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

// 5. Suppression panorama
add_action('admin_init', function () {
    if (
        isset($_GET['delete_id']) &&
        current_user_can('delete_posts') &&
        check_admin_referer('far_panorama_delete_' . $_GET['delete_id'])
    ) {
        $post_id = intval($_GET['delete_id']);
        wp_delete_post($post_id, true);

        $upload_dir = wp_upload_dir();
        $path = trailingslashit($upload_dir['basedir']) . 'panoramas/' . $post_id . '/';

        require_once ABSPATH . 'wp-admin/includes/file.php';
        global $wp_filesystem;
        if (empty($wp_filesystem)) {
            WP_Filesystem();
        }
        $wp_filesystem->delete($path, true);

        wp_redirect(admin_url('admin.php?page=far-panorama-list&deleted=1'));
        exit;
    }
});

// 6. Décompression ZIP + traitement
function far_panorama_unzip_and_prepare($zip_path, $post_id)
{
    $upload_dir = wp_upload_dir();
    $dest_dir = trailingslashit($upload_dir['basedir']) . 'panoramas/' . $post_id . '/';

    if (!file_exists($dest_dir)) {
        wp_mkdir_p($dest_dir);
    }

    $zip = new ZipArchive;
    if ($zip->open($zip_path) === TRUE) {
        $zip->extractTo($dest_dir);
        $zip->close();

        $app_files = $dest_dir . 'app-files/';
        if (is_dir($app_files)) {
            foreach (scandir($app_files) as $file) {
                if ($file === '.' || $file === '..') continue;
                rename($app_files . $file, $dest_dir . $file);
            }
            rmdir($app_files);
        }

        if (file_exists($dest_dir . 'index.html')) {
            rename($dest_dir . 'index.html', $dest_dir . 'panorama.html');
        }

        $wrapper_src = plugin_dir_path(__FILE__) . 'panorama-wrapper/index.html';
        $wrapper_dst = $dest_dir . 'index.html';
        if (file_exists($wrapper_src)) {
            copy($wrapper_src, $wrapper_dst);
        }
    }
}

// 7. Page : Liste des panoramas
function far_panorama_list_page()
{
    $panoramas = get_posts(['post_type' => 'panorama', 'numberposts' => -1]);

    echo '<div class="wrap"><h1>Mes Panoramas</h1>';

    if (isset($_GET['deleted'])) {
        echo '<div class="notice notice-success"><p>Panorama supprimé avec succès.</p></div>';
    }

    if (isset($_GET['updated'])) {
        echo '<div class="notice notice-success"><p>Panorama mis à jour.</p></div>';
    }

    if (!$panoramas) {
        echo '<p>Aucun panorama. <a href="' . admin_url('admin.php?page=far-panorama-upload') . '">Ajouter un panorama</a>.</p></div>';
        return;
    }

    echo '<table class="wp-list-table widefat fixed striped"><thead><tr><th>Titre</th><th>Shortcode</th><th>Actions</th></tr></thead><tbody>';
    foreach ($panoramas as $p) {
        echo '<tr>';
        echo '<td>' . esc_html($p->post_title) . '</td>';
        echo '<td><code>[panorama id="' . $p->ID . '"]</code></td>';
        echo '<td>';
        echo '<a class="button" href="' . admin_url('admin.php?page=far-panorama-upload&update_id=' . $p->ID) . '">Modifier</a> ';
        echo '<a class="button button-danger" href="' . wp_nonce_url(admin_url('admin.php?page=far-panorama-list&delete_id=' . $p->ID), 'far_panorama_delete_' . $p->ID) . '" onclick="return confirm(\'Confirmer la suppression ?\')">Supprimer</a>';
        echo '</td></tr>';
    }
    echo '</tbody></table></div>';
}

// 8. Page : Upload
function far_panorama_upload_page()
{
    $update_id = intval($_GET['update_id'] ?? 0);
    $is_update = $update_id > 0;

    echo '<div class="wrap"><h1>' . ($is_update ? 'Mettre à jour un panorama' : 'Téléverser un panorama') . '</h1>';

    if (isset($_GET['success'])) {
        $id = $update_id ?: intval($_GET['new_id']);
        echo '<div class="notice notice-success"><p>Panorama enregistré avec succès.</p><p>Shortcode : <code>[panorama id="' . $id . '"]</code></p></div>';
    }

    echo '<form method="post" enctype="multipart/form-data">';
    wp_nonce_field('far_panorama_upload', 'far_panorama_nonce');
    echo '<input type="file" name="panorama_zip" accept=".zip" required>';
    if ($is_update) echo '<input type="hidden" name="update_id" value="' . $update_id . '">';
    echo '<p><input type="submit" name="submit_panorama" class="button button-primary" value="' . ($is_update ? 'Mettre à jour' : 'Téléverser') . '"></p>';
    echo '</form></div>';
}

// 9. Shortcode
add_shortcode('panorama', function ($atts) {
    $post_id = intval($atts['id'] ?? 0);
    if (!$post_id) return '<p>Panorama invalide.</p>';

    $upload_dir = wp_upload_dir();
    $url = trailingslashit($upload_dir['baseurl']) . 'panoramas/' . $post_id . '/index.html';

    return '<iframe src="' . esc_url($url) . '" width="100%" height="600" style="border:none;"></iframe>';
});
