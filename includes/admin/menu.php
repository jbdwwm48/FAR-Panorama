<?php
add_action('admin_menu', function () {
    add_menu_page(
        'Tableau de bord FAR-Panorama',
        'Mes Panoramas',
        'manage_panoramas',
        'far-panorama-dashboard',
        'far_panorama_dashboard_page',
        'dashicons-format-image',
        6
    );

    add_submenu_page(
        'far-panorama-dashboard',
        'Liste des panoramas',
        'Liste des panoramas',
        'manage_panoramas',
        'far-panorama-list',
        'far_panorama_list_page'
    );

    add_submenu_page(
        'far-panorama-dashboard',
        'Ajouter un panorama',
        'Ajouter un panorama',
        'manage_panoramas',
        'far-panorama-upload',
        'far_panorama_upload_page'
    );
});
