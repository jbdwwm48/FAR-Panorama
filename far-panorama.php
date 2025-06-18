<?php

define('FAR_PANORAMA_VERSION', '0.2.0');
define('FAR_PANORAMA_URL', plugin_dir_url(__FILE__));


/**
 * Plugin Name: FAR-Panorama
 * Description: Gestion simplifiée de panoramas 360° Marzipano dans WordPress.
 * Version: 0.2.0
 * Author: Nycalith (JB)
 * License: GPL2
 */

if (!defined('ABSPATH')) exit;

// Page de gestion (/admin)
require_once plugin_dir_path(__FILE__) . 'includes/admin/menu.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/enqueue.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/page-dashboard.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/page-list.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin/page-upload.php';

// Loigque Metiers & helper
require_once plugin_dir_path(__FILE__) . 'includes/core/dependencies-check.php';
require_once plugin_dir_path(__FILE__) . 'includes/core/cpt.php';
require_once plugin_dir_path(__FILE__) . 'includes/core/shortcode.php';
require_once plugin_dir_path(__FILE__) . 'includes/core/capabilities.php';
require_once plugin_dir_path(__FILE__) . 'includes/core/helpers.php';

// Handlers
require_once plugin_dir_path(__FILE__) . 'includes/handlers/panorama-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/handlers/unzip-handler.php';

register_activation_hook(__FILE__, 'far_panorama_add_capabilities');
register_deactivation_hook(__FILE__, 'far_panorama_remove_capabilities');
