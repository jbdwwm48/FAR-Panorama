<?php

// Enregistre le Custom Post Type "panorama"
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
