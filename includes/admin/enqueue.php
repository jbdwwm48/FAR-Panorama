<?php

// =============================================================================
// Chargement conditionnel des styles et scripts pour l'admin WordPress
// =============================================================================

// Hook WordPress pour ajouter les scripts/styles en back-office
add_action('admin_enqueue_scripts', function ($hook) {

    // Debug : log du nom du hook actuel dans le fichier debug.log
    error_log('FAR PANORAMA HOOK: ' . $hook);

    // Liste des pages autorisées pour lesquelles les assets doivent être chargés
    $allowed_pages = [
        'toplevel_page_far-panorama-dashboard',       // Page du dashboard principal
        'mes-panoramas_page_far-panorama-list',       // Page de la liste des panoramas
        'mes-panoramas_page_far-panorama-upload',     // Page d'ajout de panorama
    ];

    // Si la page actuelle fait partie des pages autorisées, on injecte les assets
    if (in_array($hook, $allowed_pages, true)) {

        // Chargement du fichier CSS admin global (structure, tableau, bouton, etc.)
        wp_enqueue_style(
            'far-panorama-admin-style',
            FAR_PANORAMA_URL . 'assets/css/admin-styles.css',
            [],
            FAR_PANORAMA_VERSION
        );

        // Chargement du script JS spécifique à la page de liste des panoramas (modale d'aperçu)
        if ($hook === 'mes-panoramas_page_far-panorama-list') {
            wp_enqueue_script(
                'far-panorama-preview-modal',
                FAR_PANORAMA_URL . 'assets/js/preview-modal.js',
                [],
                FAR_PANORAMA_VERSION,
                true // true = injecté dans le footer
            );
        }

        // Chargement du script JS uniquement sur la page dashboard (toggle du formulaire d'upload)
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

// =============================================================================
// Chargement des styles côté front-end (affichage du panorama dans les pages)
// =============================================================================

// Hook WordPress pour injecter les styles sur le front du site
add_action('wp_enqueue_scripts', function () {

    // Feuille de style front pour le shortcode [panorama] (iframe responsive, etc.)
    wp_enqueue_style(
        'far-panorama-front-style',
        FAR_PANORAMA_URL . 'assets/css/front-styles.css',
        [],
        FAR_PANORAMA_VERSION
    );
});
