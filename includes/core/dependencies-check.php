<?php

// =============================================================================
// Vérification de la dépendance au plugin ACF (Advanced Custom Fields)
// =============================================================================

// Fonction appelée lors de l'activation du plugin FAR-Panorama
// Objectif : vérifier que ACF est bien installé et activé
function far_panorama_check_acf_active()
{
    // Vérifie si la classe ACF existe (donc si le plugin est actif)
    if (!class_exists('ACF')) {
        // Si ACF n'est pas présent, on désactive le plugin FAR-Panorama
        deactivate_plugins(plugin_basename(__FILE__));

        // Et on ajoute un message d'erreur dans le back-office
        add_action('admin_notices', function () {
            echo '<div class="error"><p><strong>FAR-Panorama :</strong> Le plugin ACF est requis.</p></div>';
        });
    }
}

// Enregistre la fonction ci-dessus comme hook d'activation du plugin
// Elle sera appelée uniquement lors de l'activation du plugin depuis le back-office
register_activation_hook(__FILE__, 'far_panorama_check_acf_active');


// =============================================================================
// Affichage d’un message d’erreur si ACF est désactivé pendant l’utilisation
// =============================================================================

// Ce bloc s'affiche à chaque chargement du back-office si ACF est manquant
add_action('admin_notices', function () {
    // Si ACF est désactivé ou supprimé, on affiche un message d'alerte
    if (!class_exists('ACF')) {
        echo '<div class="error"><p><strong>FAR-Panorama :</strong> Activez ACF pour que le plugin fonctionne.</p></div>';
    }
});
