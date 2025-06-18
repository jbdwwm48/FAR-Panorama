<?php

// Ajout de la capability personnalisée lors de l’activation du plugin
function far_panorama_add_capabilities()
{
    $roles = ['administrator', 'editor', 'author'];
    foreach ($roles as $role_name) {
        $role = get_role($role_name);
        if ($role && !$role->has_cap('manage_panoramas')) {
            $role->add_cap('manage_panoramas');
        }
    }
}

// Suppression de la capability lors de la désactivation du plugin
function far_panorama_remove_capabilities()
{
    $roles = ['administrator', 'editor', 'author'];
    foreach ($roles as $role_name) {
        $role = get_role($role_name);
        if ($role && $role->has_cap('manage_panoramas')) {
            $role->remove_cap('manage_panoramas');
        }
    }
}
