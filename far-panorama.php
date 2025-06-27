<?php

// Définition de la version actuelle du plugin
define('FAR_PANORAMA_VERSION', '0.5.1');

// Définition de l’URL du plugin (utile pour charger des assets dans l’admin ou le front)
define('FAR_PANORAMA_URL', plugin_dir_url(__FILE__));

/**
 * Plugin Name: FAR-Panorama
 * Description: Gestion simplifiée de panoramas 360° Marzipano dans WordPress.
 * Version: 0.5.1
 * Author: Nycalith (JB)
 * Author URI: https://github.com/jbdwwm48/
 * Plugin URI: https://github.com/jbdwwm48/FAR-Panorama
 * License: GPL2
 * 
 * Ces métadonnées permettent à WordPress d’identifier le plugin.
 */

// Sécurité : si le fichier est accédé directement (hors de WordPress), on bloque l’exécution
if (!defined('ABSPATH')) exit;

// -----------------------------
// Chargement des fichiers ADMIN
// -----------------------------

// Création du menu personnalisé dans l’admin
require_once plugin_dir_path(__FILE__) . 'includes/admin/menu.php';

// Chargement des fichiers CSS et JS uniquement pour l’admin
require_once plugin_dir_path(__FILE__) . 'includes/admin/enqueue.php';

// Page d’accueil du plugin : "Mes Panoramas"
require_once plugin_dir_path(__FILE__) . 'includes/admin/page-dashboard.php';

// Page listant les panoramas ajoutés
require_once plugin_dir_path(__FILE__) . 'includes/admin/page-list.php';

// Page contenant le formulaire d’ajout de panorama (fichier ZIP)
require_once plugin_dir_path(__FILE__) . 'includes/admin/page-upload.php';

// -----------------------------
// Chargement des fichiers CORE
// -----------------------------

// Vérifie que les dépendances nécessaires (Marzipano) sont bien présentes
require_once plugin_dir_path(__FILE__) . 'includes/core/dependencies-check.php';

// Déclaration du Custom Post Type "Panorama"
require_once plugin_dir_path(__FILE__) . 'includes/core/cpt.php';

// Shortcode [panorama id=""] permettant d’intégrer un panorama dans une page/article
require_once plugin_dir_path(__FILE__) . 'includes/core/shortcode.php';

// Attribution des permissions personnalisées selon les rôles WordPress
require_once plugin_dir_path(__FILE__) . 'includes/core/capabilities.php';

// Fonctions utilitaires utilisées un peu partout dans le plugin
require_once plugin_dir_path(__FILE__) . 'includes/core/helpers.php';

// -----------------------------
// Chargement des HANDLERS
// -----------------------------

// Gestion de l’upload et de la mise à jour des panoramas (détection de fichiers, champs POST, etc.)
require_once plugin_dir_path(__FILE__) . 'includes/handlers/panorama-handler.php';

// Gestion de la décompression du ZIP Marzipano et placement des fichiers
require_once plugin_dir_path(__FILE__) . 'includes/handlers/unzip-handler.php';

// -----------------------------
// Hooks d’activation / désactivation
// -----------------------------

// Lors de l’activation du plugin : ajout des droits personnalisés (voir capabilities.php)
register_activation_hook(__FILE__, 'far_panorama_add_capabilities');

// Lors de la désactivation du plugin : suppression des droits personnalisés
register_deactivation_hook(__FILE__, 'far_panorama_remove_capabilities');
