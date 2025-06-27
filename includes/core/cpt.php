<?php

// ----------------------------------------------------------
// Déclaration du Custom Post Type (CPT) "panorama"
// ----------------------------------------------------------

// On utilise le hook 'init' pour enregistrer un nouveau type de contenu personnalisé
add_action('init', function () {

    // Enregistrement du CPT "panorama" avec ses options
    register_post_type('panorama', [

        // Définition des libellés (labels) affichés dans l’interface admin
        'labels' => [
            'name' => 'Panoramas',               // Nom pluriel affiché dans les listes
            'singular_name' => 'Panorama',       // Nom au singulier
            'menu_name' => 'Mes Panoramas'       // Nom visible dans le menu (si show_ui = true)
        ],

        // Le CPT n’est pas public (non accessible depuis l’interface classique de WordPress)
        'public' => false,

        // On ne souhaite pas que l’interface de gestion par défaut de WP soit visible
        // car on gère les pages nous-mêmes (via le plugin)
        'show_ui' => false,

        // Autorise l’utilisation via l’API REST (utile si besoin d’exposer les données plus tard)
        'show_in_rest' => true,

        // Le type de contenu accepte uniquement le titre (pas d’éditeur, ni d’image à la une)
        'supports' => ['title'],
    ]);
});
