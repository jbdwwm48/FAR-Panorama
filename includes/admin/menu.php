<?php
// Ajouter les pages du menu FAR-Panorama
add_action('admin_menu', function () {
    // Menu principal
    add_menu_page(
        'Tableau de bord FAR-Panorama',
        'Mes Panoramas',
        'manage_panoramas',
        'far-panorama-dashboard',
        'far_panorama_dashboard_page',
        'dashicons-format-image',
        6
    );

    // Sous-menu : Liste des panoramas
    add_submenu_page(
        'far-panorama-dashboard',
        'Liste des panoramas',
        'Liste des panoramas',
        'manage_panoramas',
        'far-panorama-list',
        'far_panorama_list_page'
    );

    // Sous-menu : Ajouter un panorama (on ajoute mais on masque ensuite)
    add_submenu_page(
        'far-panorama-dashboard',
        'Ajouter un panorama',
        'Ajouter un panorama',
        'manage_panoramas',
        'far-panorama-upload',
        'far_panorama_upload_page'
    );
});

// Masquer le lien "Ajouter un panorama" dans le menu admin
add_action('admin_head', function () {
    global $submenu;
    if (isset($submenu['far-panorama-dashboard'])) {
        foreach ($submenu['far-panorama-dashboard'] as $index => $menu_item) {
            // Le slug est à la position 2 du tableau $menu_item
            if ($menu_item[2] === 'far-panorama-upload') {
                unset($submenu['far-panorama-dashboard'][$index]);
            }
        }
    }
});
