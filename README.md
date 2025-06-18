# FAR‑Panorama

**Version 0.2 (dev)**  
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

- Depuis la page **Mes Panoramas**, cliquer sur l’icône “modifier”  
- Uploader un nouveau ZIP pour remplacer les fichiers (le shortcode reste identique)

### 🗑️ Supprimer un panorama

- Depuis la liste, cliquer sur l’icône “supprimer”  
- Le post et les fichiers associés sont supprimés du serveur

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
├── assets/                          ← Fichiers CSS/JS backend
│   ├── css/
│   │   └── admin-styles.css
│   └── js/
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
└── README.md
```

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
