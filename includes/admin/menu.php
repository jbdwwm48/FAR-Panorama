<?php

// -----------------------------------------------------------------------------
// Ajout des entrées dans le menu admin WordPress pour le plugin FAR-Panorama
// -----------------------------------------------------------------------------

add_action('admin_menu', function () {

    // -------------------------------
    // Menu principal : "Mes Panoramas"
    // -------------------------------
    add_menu_page(
        'Tableau de bord FAR-Panorama', // Titre de la page (onglet)
        'Mes Panoramas',                // Libellé affiché dans le menu
        'manage_panoramas',             // Capacité requise (définie dans capabilities.php)
        'far-panorama-dashboard',       // Slug de la page (utilisé dans l’URL)
        'far_panorama_dashboard_page',  // Fonction qui affiche la page
        'dashicons-format-image',       // Icône du menu
        6                                // Position dans le menu admin
    );

    // ----------------------------------
    // Sous-menu 1 : "Liste des panoramas"
    // ----------------------------------
    add_submenu_page(
        'far-panorama-dashboard',       // Slug parent
        'Liste des panoramas',          // Titre de la page
        'Liste des panoramas',          // Titre dans le menu
        'manage_panoramas',             // Capacité requise
        'far-panorama-list',            // Slug de la sous-page
        'far_panorama_list_page'        // Fonction qui affiche la page
    );

    // ----------------------------------------
    // Sous-menu 2 : "Ajouter un panorama"
    // (Est masqué plus bas, utile rienque dans les bouton du dashboard)
    // ----------------------------------------
    add_submenu_page(
        'far-panorama-dashboard',       // Slug parent
        'Ajouter un panorama',          // Titre de la page
        'Ajouter un panorama',          // Titre dans le menu
        'manage_panoramas',             // Capacité requise
        'far-panorama-upload',          // Slug de la sous-page
        'far_panorama_upload_page'      // Fonction qui affiche la page
    );
});

// -----------------------------------------------------------------------------
// Masquer le lien "Ajouter un panorama" du menu admin (on le garde actif mais invisible)
// -----------------------------------------------------------------------------

add_action('admin_head', function () {
    global $submenu;

    // Vérifie si le sous-menu du plugin existe
    if (isset($submenu['far-panorama-dashboard'])) {
        foreach ($submenu['far-panorama-dashboard'] as $index => $menu_item) {
            // Chaque $menu_item est un tableau : [0 => nom affiché, 1 => capability, 2 => slug]
            // Si le slug correspond à la page "ajouter un panorama", on la supprime du menu
            if ($menu_item[2] === 'far-panorama-upload') {
                unset($submenu['far-panorama-dashboard'][$index]);
            }
        }
    }
});
