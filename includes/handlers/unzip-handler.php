<?php

// Décompression ZIP et préparation du dossier panorama
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

        $plugin_root = dirname(dirname(dirname(__FILE__))); // handlers -> includes -> plugin root
        $wrapper_src = $plugin_root . '/panorama-wrapper/index.html';
        $wrapper_dst = $dest_dir . 'index.html';

        if (file_exists($wrapper_src)) {
            copy($wrapper_src, $wrapper_dst);
        } else {
            error_log("Wrapper manquant : " . $wrapper_src);
        }
    }
}
