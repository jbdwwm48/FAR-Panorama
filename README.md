# FAR‑Panorama

**Version 0.4 (dev)**  
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
À coller dans une page ou un article pour afficher le panorama via iframe.

---

## Installation

1. Installer et activer le plugin **Advanced Custom Fields (ACF)**  
2. Copier le dossier `far-panorama/` dans `wp-content/plugins/`  
3. Activer le plugin depuis le tableau de bord WordPress  
4. Ouvrir le menu **Mes Panoramas**, uploader une archive ZIP, récupérer le shortcode une fois généré

---

## Utilisation

### ➕ Ajouter un panorama

- Ouvrir le menu **Mes Panoramas > Ajouter**  
- Sélectionner un ZIP Marzipano (contenant au moins un `index.html` et un dossier `tiles`)  
- Le plugin extrait les fichiers, remplace le wrapper `index.html`, et crée un CPT Panorama

### ✏️ Modifier un panorama

- Depuis la page **Mes Panoramas**, cliquer sur le bouton “Modifier”  
- Uploader un nouveau ZIP pour remplacer les fichiers (le shortcode reste identique)

### 🗑️ Supprimer un panorama

- Depuis la liste, cliquer sur le bouton “Supprimer”  
- Le post et les fichiers associés sont supprimés du serveur

### 👁️ Aperçu direct

- Un bouton **Aperçu** est disponible dans la liste pour afficher le panorama dans une modale (lightbox) directement depuis le back-office.

### 🌐 Intégration front propre

- Le plugin intègre désormais une feuille de style front (`front-styles.css`) chargée automatiquement
- Elle supprime les marges/blocs vides autour du panorama (notamment le "gap blanc")
- Le rendu du panorama est désormais **full-width, centré et sans bordures**

---

## Structure des fichiers

Les fichiers sont extraits dans :  
`/wp-content/uploads/panoramas/{post_id}/`

Arborescence générée :

```text
{post_id}/
├── tiles/
├── index.html        ← wrapper injecté
└── panorama.html     ← fichier original renommé
```

Le wrapper HTML utilisé est stocké dans :  
`far-panorama/panorama-wrapper/index.html`

---

## Architecture du plugin

Depuis la version 0.2, le plugin adopte une structure **modulaire** :

```text
far-panorama/
├── far-panorama.php                 ← Point d'entrée du plugin
├── assets/                          ← Fichiers CSS/JS
│   ├── css/
│   │   ├── admin-styles.css
│   │   └── front-styles.css
│   └── js/
│       └── preview-modal.js
├── includes/
│   ├── admin/                       ← Pages et menus du back-office
│   │   ├── menu.php
│   │   ├── enqueue.php
│   │   ├── page-dashboard.php
│   │   ├── page-list.php
│   │   └── page-upload.php
│   ├── core/                        ← Logique métier et utilitaires
│   │   ├── capabilities.php
│   │   ├── cpt.php
│   │   ├── dependencies-check.php
│   │   ├── helpers.php
│   │   └── shortcode.php
│   └── handlers/                   ← Gestion upload / suppression
│       ├── panorama-handler.php
│       └── unzip-handler.php
├── panorama-wrapper/               ← Wrapper HTML injecté dans chaque panorama
│   └── index.html
├── notes.txt                       ← Fichier ignoré par Git dès v0.2
└── README.md
```

---

## Nouveautés depuis la version précedente

- Refonte complète de l’interface de la page “Mes Panoramas”
- Affichage du login auteur dans la liste des panoramas
- Compteur de vues par panorama (post meta `panorama_views`)
- Bouton **Aperçu** avec ouverture dans une modale/Lightbox
- Refonte UX des boutons : couleurs, hover, accessibilité
- Refonte du CSS admin (moderne et responsive)
- **Ajout d’un CSS front (`front-styles.css`) pour corriger differents bugs sur l’affichage public**
- **Suppression d'un “gap blanc” récurrant sur tous les panoramas + centrage et rendu full-width**

---

## Dépendances

- WordPress 5.8 ou supérieur  
- PHP 7.4 ou supérieur  
- Plugin **Advanced Custom Fields (ACF)** actif

---

## Licence

GPLv2 — libre d’utilisation, de modification et de redistribution.

---

Développement en cours par **Nycalith (JB)**.
