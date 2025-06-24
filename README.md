# FAR‑Panorama

**Version 0.5**  
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

- Ouvrir le menu **Mes Panoramas** puis cliquer sur **"Ajouter un panorama"**
- Un formulaire d’upload se déplie dynamiquement dans la page (plus de redirection)
- Sélectionner un ZIP Marzipano (contenant au moins un `index.html` et un dossier `tiles`)
- Le plugin extrait les fichiers, remplace le wrapper `index.html`, et crée un CPT Panorama
- Une **section de prévisualisation** s’affiche automatiquement avec :
  - Le lien vers le panorama
  - Un **shortcode cliquable pour copier automatiquement** dans le presse-papier

### ✏️ Modifier un panorama

- Depuis la page **Mes Panoramas**, cliquer sur le bouton “Modifier”  
- Uploader un nouveau ZIP pour remplacer les fichiers (le shortcode reste identique)

### 🗑️ Supprimer un panorama

- Depuis la liste, cliquer sur le bouton “Supprimer”  
- Le post et les fichiers associés sont supprimés du serveur

### 👁️ Aperçu direct

- Un bouton **Aperçu** est disponible dans la liste pour afficher le panorama dans une **modale (lightbox)** directement depuis le back-office
- Le contenu affiché est le `index.html` personnalisé du panorama concerné

### 🌐 Intégration front propre

- Le plugin intègre une feuille de style front (`front-styles.css`) chargée automatiquement via le shortcode
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
├── panorama.html     ← fichier original renommé
└── preview.jpg       ← image d’aperçu automatique si présente dans l’archive
```

Le wrapper HTML utilisé est stocké dans :  
`far-panorama/panorama-wrapper/index.html`

---

## Architecture du plugin

Depuis la version 0.2, le plugin adopte une structure **modulaire** :

```text
far-panorama/
├── far-panorama.php                 ← Point d'entrée du plugin
├── assets/                          ← Fichiers CSS/JS/images
│   ├── css/
│   │   ├── admin-styles.css
│   │   └── front-styles.css
│   ├── img/
│   │   └── tuto1.png
│   └── js/
│       ├── dashboard-actions.js
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
├── panorama-wrapper/
│   └── index.html
├── notes.txt
└── README.md
```

---

## Nouveautés / changelog v0.5

### ✅ Nouvelles fonctionnalités

- Affichage **inline** du formulaire d’ajout dans le dashboard (plus besoin d’une page dédiée)
- Ajout d’une **section de prévisualisation** après l’upload avec :
  - Lien vers le panorama
  - Shortcode **copiable automatiquement**
- Intégration d’un bouton **“Aperçu”** (iframe dans une lightbox via modale JS)
- Suppression du lien du menu admin “Ajouter un panorama” (page toujours accessible en direct)
- Gestion du bouton “Ajouter un panorama” via JS (affiche/masque dynamiquement le formulaire)
- Support automatique d’un fichier `preview.jpg` (extrait même s’il est dans un sous-dossier de l’archive ZIP)

### 🔧 Fixs et améliorations

- Correction du **"gap blanc"** en-dessous des panoramas intégrés via shortcode
- Le wrapper `index.html` injecté gère maintenant correctement l’affichage sans bordures
- Les fichiers CSS et JS sont désormais enqueue proprement selon le contexte (admin/front)
- Refonte complète de la page **Mes Panoramas** :
  - Preview image visible directement dans la liste
  - Boutons “Modifier”, “Supprimer” et “Aperçu” plus visibles
  - Style modernisé compatible WordPress
- Clean du code PHP, structure plus lisible, logique regroupée
- Sécurité renforcée sur les chemins d’extraction et suppression

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
