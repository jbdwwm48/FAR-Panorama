<?php

// =============================================================================
// Attribution des droits d'accès au plugin FAR‑Panorama
// =============================================================================

// Fonction appelée à l’activation du plugin
// Elle ajoute une capacité personnalisée "manage_panoramas" à plusieurs rôles WordPress
function far_panorama_add_capabilities()
{
    // Liste des rôles qui auront le droit d’utiliser le plugin
    $roles = ['administrator', 'editor', 'author'];

    // Pour chaque rôle défini, on ajoute la capacité si elle n’est pas déjà présente
    foreach ($roles as $role_name) {
        $role = get_role($role_name);
        if ($role && !$role->has_cap('manage_panoramas')) {
            $role->add_cap('manage_panoramas');
        }
    }
}

// Fonction appelée à la désactivation du plugin
// Elle supprime la capacité "manage_panoramas" des rôles listés
function far_panorama_remove_capabilities()
{
    // Même logique que ci-dessus, on cible les mêmes rôles
    $roles = ['administrator', 'editor', 'author'];

    // Pour chaque rôle, on supprime la capacité s’ils l’ont
    foreach ($roles as $role_name) {
        $role = get_role($role_name);
        if ($role && $role->has_cap('manage_panoramas')) {
            $role->remove_cap('manage_panoramas');
        }
    }
}
