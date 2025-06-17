# FacilePanorama

**Version 1.0.0**  
Plugin WordPress pour gérer et afficher des panoramas 360° via Marzipano.

## Fonctionnement fluide

- Un menu **“Mes Panoramas”** propose une upload page simplifiée :  
  1. Upload d’un ZIP Marzipano  
  2. Décompression automatique + copie du wrapper  
  3. Génération d’un CPT et du shortcode

- Une notification indique que le panorama est prêt :  
  **Shortcode :** `[panorama id=123]`

- Utilisation du shortcode sur n’importe quelle page/post pour l’affichage via iframe.

## Installation

1. Installe et active **Advanced Custom Fields (ACF)**.  
2. Copie le dossier `facile-panorama/` dans `/wp-content/plugins/`.  
3. Active **FacilePanorama** depuis l’admin.  
4. Va dans **Mes Panoramas**, upload ton ZIP, et récupère le shortcode une fois prêt.  
5. Colle le shortcode dans une page pour voir ton panorama.

## Bonus

- Gère automatiquement les uploads/dossiers dans `wp-content/uploads/panoramas/[ID]`  
- Ton wrapper reste ajustable dans `panorama-wrapper/index.html`  
- Expérience admin clean, step-by-step, sans bloc d’édition classique

## Licence

GPL2 — Libre de modifier et de partager.
