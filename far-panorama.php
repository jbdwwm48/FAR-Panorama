<?php

/**
 * Plugin Name: FAR-Panorama
 * Description: Gestion simplifiée de panoramas 360° Marzipano dans WordPress.
 * Version: 1.1.0
 * Author: Nycalith (JB)
 * License: GPL2
 */


if (!defined('ABSPATH')) exit;

// 1) Check ACF à l'activation
function facile_panorama_check_acf_active()
{
    if (!class_exists('ACF')) {
        deactivate_plugins(plugin_basename(__FILE__));
        add_action('admin_notices', function () {
            echo '<div class="error"><p><strong>FacilePanorama :</strong> Le plugin <strong>ACF</strong> doit être activé.</p></div>';
        });
    }
}
register_activation_hook(__FILE__, 'facile_panorama_check_acf_active');

add_action('admin_notices', function () {
    if (!class_exists('ACF')) {
        echo '<div class="error"><p><strong>FAR-Panorama :</strong> Activez <strong>ACF</strong> pour que le plugin fonctionne.</p></div>';
    }
});

// 2) Register CPT (sans UI WP natif)
add_action('init', function () {
    $labels = [
        'name' => 'Panoramas',
        'singular_name' => 'Panorama',
        'menu_name' => 'Mes Panoramas',
        'add_new_item' => 'Ajouter un nouveau panorama',
        'edit_item' => 'Modifier le panorama',
        'new_item' => 'Nouveau panorama',
        'view_item' => 'Voir le panorama',
        'search_items' => 'Rechercher un panorama',
        'not_found' => 'Aucun panorama trouvé',
        'not_found_in_trash' => 'Aucun panorama dans la corbeille',
    ];

    register_post_type('panorama', [
        'labels' => $labels,
        'public' => false,
        'show_ui' => false,
        'show_in_rest' => true,
        'supports' => ['title'],
        'has_archive' => false,
    ]);
});

// 3) Menu + sous-menus custom
add_action('admin_menu', function () {
    add_menu_page(
        'Mes Panoramas',
        'Mes Panoramas',
        'publish_posts',
        'facile-panorama-list',
        'facile_panorama_list_page',
        'dashicons-format-image',
        20
    );

    add_submenu_page(
        'facile-panorama-list',
        'Liste des panoramas',
        'Mes panoramas',
        'publish_posts',
        'facile-panorama-list',
        'facile_panorama_list_page'
    );

    add_submenu_page(
        'facile-panorama-list',
        'Ajouter un panorama',
        'Ajouter un nouveau panorama',
        'publish_posts',
        'facile-panorama-upload',
        'facile_panorama_upload_page'
    );
});

// 4) Page liste panoramas (tableau simple)
function facile_panorama_list_page()
{
?>
    <div class="wrap">
        <h1>Mes Panoramas</h1>

        <?php if (isset($_GET['updated']) && $_GET['updated'] === '1'): ?>
            <div class="notice notice-success">
                <p>✅ Panorama mis à jour avec succès !</p>
            </div>
        <?php endif; ?>

        <?php
        $panoramas = get_posts([
            'post_type' => 'panorama',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ]);
        if (!$panoramas) {
            echo '<p>Aucun panorama trouvé. <a href="' . admin_url('admin.php?page=facile-panorama-upload') . '">Ajoutez-en un</a>.</p>';
            return;
        }
        ?>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Shortcode</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($panoramas as $p): ?>
                    <tr>
                        <td><?php echo esc_html($p->post_title); ?></td>
                        <td><code>[panorama id="<?php echo $p->ID; ?>"]</code></td>
                        <td><a href="<?php echo admin_url('admin.php?page=facile-panorama-upload&update_id=' . $p->ID); ?>" class="button">Mettre à jour</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php
}

// 5) Page upload + mise à jour minimaliste
function facile_panorama_upload_page()
{
    $update_id = isset($_GET['update_id']) ? intval($_GET['update_id']) : 0;
    $is_update = $update_id > 0;
?>
    <div class="wrap">
        <h1><?php echo $is_update ? 'Mettre à jour un panorama' : 'Téléversez votre archive .zip de panorama'; ?></h1>

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="notice notice-success">
                <p>✅ Panorama <?php echo $is_update ? 'mis à jour' : 'prêt'; ?> !</p>
                <p>Shortcode : <code>[panorama id="<?php echo esc_html($update_id ?: $_GET['new_id']); ?>"]</code></p>
                <p>Collez ce shortcode là où vous voulez afficher le panorama.</p>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('facile_panorama_upload', 'facile_panorama_nonce'); ?>
            <input type="file" name="panorama_zip" accept=".zip" required>
            <?php if ($is_update): ?>
                <input type="hidden" name="update_id" value="<?php echo $update_id; ?>">
            <?php endif; ?>
            <p><input type="submit" name="submit_panorama" class="button button-primary" value="<?php echo $is_update ? 'Mettre à jour' : 'Téléverser'; ?>"></p>
        </form>
    </div>
<?php
}

// 6) Gestion upload + unzip
add_action('admin_init', function () {
    if (isset($_POST['submit_panorama']) && check_admin_referer('facile_panorama_upload', 'facile_panorama_nonce')) {
        if (!empty($_FILES['panorama_zip']['tmp_name'])) {
            $file = $_FILES['panorama_zip'];
            require_once ABSPATH . 'wp-admin/includes/file.php';
            $upload = wp_handle_upload($file, ['test_form' => false]);

            if (!isset($upload['error'])) {
                $update_id = isset($_POST['update_id']) ? intval($_POST['update_id']) : 0;

                if ($update_id) {
                    $post_id = $update_id;
                    wp_update_post(['ID' => $post_id]);
                } else {
                    $post_id = wp_insert_post([
                        'post_type' => 'panorama',
                        'post_title' => 'Panorama ' . date('d/m/Y H:i'),
                        'post_status' => 'publish',
                    ]);
                }

                update_field('panorama_zip', $upload, $post_id);
                facile_panorama_unzip_zip($upload['file'], $post_id);

                $redirect_url = admin_url('admin.php?page=facile-panorama-upload&success=1');
                $redirect_url .= $update_id ? '&update_id=' . $update_id : '&new_id=' . $post_id;
                wp_redirect($redirect_url);
                exit;
            }
        }
    }
});

// 7) Fonction unzip + wrapper + renommage index.html
function facile_panorama_unzip_zip($zip_path, $post_id)
{
    $upload_dir = wp_upload_dir();
    $dest_dir = trailingslashit($upload_dir['basedir']) . 'panoramas/' . $post_id . '/';

    if (!file_exists($dest_dir) && !wp_mkdir_p($dest_dir)) {
        error_log("FacilePanorama : Impossible de créer le dossier $dest_dir");
        return false;
    }

    $zip = new ZipArchive;
    if ($zip->open($zip_path) === TRUE) {

        if (!$zip->extractTo($dest_dir)) {
            error_log("FacilePanorama : Échec extraction ZIP vers $dest_dir");
            $zip->close();
            return false;
        }
        $zip->close();

        // Déplacer le contenu de app-files/ vers $dest_dir
        $app_files = $dest_dir . 'app-files/';
        if (is_dir($app_files)) {
            $files = scandir($app_files);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..') continue;

                $src = $app_files . $file;
                $dst = $dest_dir . $file;

                if (file_exists($dst)) {
                    if (is_dir($dst)) {
                        rmdir($dst);
                    } else {
                        unlink($dst);
                    }
                }

                if (!rename($src, $dst)) {
                    error_log("FacilePanorama : Impossible de déplacer $src vers $dst");
                }
            }
            rmdir($app_files);
        }

        // Renommer index.html en panorama.html
        $index_path = $dest_dir . 'index.html';
        $panorama_path = $dest_dir . 'panorama.html';

        if (file_exists($index_path)) {
            if (file_exists($panorama_path)) unlink($panorama_path);

            if (!rename($index_path, $panorama_path)) {
                error_log("FacilePanorama : Impossible de renommer index.html en panorama.html");
            }
        } else {
            error_log("FacilePanorama : Fichier index.html manquant dans $dest_dir");
        }

        // Copier le wrapper index.html (ton index.html perso) à la racine de $dest_dir
        $wrapper_src = plugin_dir_path(__FILE__) . 'panorama-wrapper/index.html';
        $wrapper_dst = $dest_dir . 'index.html';

        if (file_exists($wrapper_src)) {
            if (!copy($wrapper_src, $wrapper_dst)) {
                error_log("FacilePanorama : Échec copie du wrapper index.html vers la racine");
            }
        } else {
            error_log("FacilePanorama : Wrapper index.html manquant dans panorama-wrapper");
        }

        return true;
    } else {
        error_log("FacilePanorama : Impossible d'ouvrir le ZIP $zip_path");
        return false;
    }
}

// 8) Shortcode d'affichage
add_shortcode('panorama', function ($atts) {
    $atts = shortcode_atts(['id' => 0], $atts, 'panorama');
    $post_id = intval($atts['id']);
    if (!$post_id) return '<p>Panorama non spécifié.</p>';

    $upload_dir = wp_upload_dir();
    $panorama_url = trailingslashit($upload_dir['baseurl']) . 'panoramas/' . $post_id . '/index.html';

    return '<iframe src="' . esc_url($panorama_url) . '" width="100%" height="600" style="border:none;"></iframe>';
});
