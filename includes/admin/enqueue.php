<?php

// === ADMIN STYLES & SCRIPTS ===
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

        // Script modale pour bouton "Aperçu" - uniquement sur page liste
        if ($hook === 'mes-panoramas_page_far-panorama-list') {
            wp_enqueue_script(
                'far-panorama-preview-modal',
                FAR_PANORAMA_URL . 'assets/js/preview-modal.js',
                [],
                FAR_PANORAMA_VERSION,
                true
            );
        }

        // Nouveau script pour le toggle du formulaire dans le dashboard uniquement
        if ($hook === 'toplevel_page_far-panorama-dashboard') {
            wp_enqueue_script(
                'far-panorama-dashboard-actions',
                FAR_PANORAMA_URL . 'assets/js/dashboard-actions.js',
                [],
                FAR_PANORAMA_VERSION,
                true
            );
        }
    }
});

// === FRONT STYLES ===
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'far-panorama-front-style',
        FAR_PANORAMA_URL . 'assets/css/front-styles.css',
        [],
        FAR_PANORAMA_VERSION
    );
});
