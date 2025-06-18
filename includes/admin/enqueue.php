<?php
add_action('admin_enqueue_scripts', function ($hook) {
    error_log('FAR PANORAMA HOOK: ' . $hook);

    $allowed_pages = [
        'toplevel_page_far-panorama-dashboard',
        'mes-panoramas_page_far-panorama-list',
        'mes-panoramas_page_far-panorama-upload',
    ];


    if (in_array($hook, $allowed_pages, true)) {
        wp_enqueue_style(
            'far-panorama-admin-style',
            FAR_PANORAMA_URL . 'assets/css/admin-styles.css',
            [],
            FAR_PANORAMA_VERSION
        );
        wp_enqueue_script(
            'far-panorama-admin-script',
            FAR_PANORAMA_URL . 'assets/js/admin-scripts.js',
            [],
            FAR_PANORAMA_VERSION,
            true
        );
    }
});
