# FAR‑Panorama

**Version 0.5.3**  
Plugin WordPress pour intégrer et gérer des panoramas 360° générés avec Marzipano.

---

## Fonctionnement

Un menu admin **“Mes Panoramas”** permet de gérer facilement l'import, l'affichage, la mise à jour et la suppression de panoramas :

1. Upload d’une archive ZIP exportée depuis Marzipano Tool  
2. Décompression automatique et insertion d’un wrapper `index.html` personnalisé  
3. Création ou mise à jour d’un panorama (Custom Post Type)  
4. Shortcode généré automatiquement pour intégrer le panorama dans les pages

**Exemple de shortcode :**  
`[panorama id="123"]`

---

## Installation

1. Installer et activer le plugin **Advanced Custom Fields (ACF)**  
2. Copier le dossier `far-panorama/` dans `wp-content/plugins/`  
3. Activer le plugin depuis le tableau de bord WordPress  
4. Ouvrir le menu **Mes Panoramas**, uploader une archive ZIP, récupérer le shortcode une fois généré

---

## Utilisation

### ➕ Ajouter un panorama

- Le bouton “Ajouter un panorama” affiche dynamiquement un formulaire sans rechargement
- Sélectionner un ZIP Marzipano (avec un `index.html` et un dossier `tiles`)
- Le panorama est traité automatiquement et un shortcode s’affiche
- Une section de prévisualisation est visible immédiatement après l’upload

### ✏️ Modifier un panorama

- Depuis la page **Mes Panoramas**, cliquer sur “Modifier”  
- Uploader un nouveau ZIP : le panorama est mis à jour (le shortcode reste identique)

### 🗑️ Supprimer un panorama

- Cliquer sur “Supprimer” supprime le panorama et ses fichiers du serveur

### 👁️ Aperçu direct

- Un bouton **Aperçu** permet de visualiser le panorama dans une modale/lightbox

### 🌐 Intégration front

- Le panorama est affiché en full-width et sans marges parasites
- Feuille de style `front-styles.css` intégrée automatiquement via le shortcode

---

## Structure des fichiers

Les fichiers sont extraits dans :  
`/wp-content/uploads/panoramas/{post_id}/`

```text
{post_id}/
├── tiles/
├── index.html        ← wrapper injecté
├── panorama.html     ← fichier original renommé
└── preview.jpg       ← image d’aperçu automatique (si présente)
```

Wrapper utilisé :  
`far-panorama/panorama-wrapper/index.html`

---

## Architecture du plugin

```text
far-panorama/
├── far-panorama.php
├── assets/
│   ├── css/
│   │   ├── admin-styles.css
│   │   └── front-styles.css
│   ├── img/
│   │   └── tuto1.png
│   └── js/
│       ├── dashboard-actions.js
│       └── preview-modal.js
├── includes/
│   ├── admin/
│   │   ├── menu.php
│   │   ├── enqueue.php
│   │   ├── page-dashboard.php
│   │   ├── page-list.php
│   │   └── page-upload.php
│   ├── core/
│   │   ├── capabilities.php
│   │   ├── cpt.php
│   │   ├── dependencies-check.php
│   │   ├── helpers.php
│   │   └── shortcode.php
│   └── handlers/
│       ├── panorama-handler.php
│       └── unzip-handler.php
├── panorama-wrapper/
│   └── index.html
├── notes.txt
└── README.md
```

---

## Changelog v0.5.3

### ✅ Nouveautés

- Formulaire d’ajout désormais intégré dans la page dashboard (affichage dynamique)
- Ajout d’une section de preview avec lien + shortcode copiable automatiquement
- Intégration d’une modale pour aperçu direct (lightbox)
- Suppression du lien de menu “Ajouter un panorama” (form visible via bouton uniquement)
- Gestion du preview.jpg automatique même depuis un sous-dossier

### 🔧 Corrections / améliorations

- Fix du “gap blanc” sous les panoramas
- CSS front chargé proprement via shortcode
- Shortcode désormais unique et persistant (même après modification)
- Affichage conditionnel des messages de confirmation ou d’action en cours
- Suppression des doublons de titres et notices
- Icône SVG de copie restaurée (non remplie)
- Nettoyage du formulaire et du retour utilisateur

---

## Dépendances

- WordPress 5.8+  
- PHP 7.4+  
- Plugin **Advanced Custom Fields (ACF)**

---

## Licence



---

Développé par **Jean-Baptiste Charbonnier pour Facilearetenir.com**.
