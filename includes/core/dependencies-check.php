<?php

// Vérifie que ACF est activé à l'activation
function far_panorama_check_acf_active()
{
    if (!class_exists('ACF')) {
        deactivate_plugins(plugin_basename(__FILE__));
        add_action('admin_notices', function () {
            echo '<div class="error"><p><strong>FAR-Panorama :</strong> Le plugin ACF est requis.</p></div>';
        });
    }
}
register_activation_hook(__FILE__, 'far_panorama_check_acf_active');

// Message admin si ACF manquant
add_action('admin_notices', function () {
    if (!class_exists('ACF')) {
        echo '<div class="error"><p><strong>FAR-Panorama :</strong> Activez ACF pour que le plugin fonctionne.</p></div>';
    }
});
