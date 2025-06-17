# FAR‑Panorama

**Version 0.2 (dev)**  
Plugin WordPress pour intégrer et gérer des panoramas 360° générés avec Marzipano.

---

## Fonctionnement

Un menu admin **“Mes Panoramas”** permet de gérer facilement l'import, l'affichage, la mise à jour et la suppression de panoramas :

1. Upload d’une archive ZIP exportée depuis Marzipano Tool  
2. Décompression automatique et insertion d’un wrapper `index.html`  personnalisé  
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
4. Aller dans **Mes Panoramas**, uploader un ZIP, récupérer le shortcode une fois généré

---

## Utilisation

### ➕ Ajouter un panorama

- Aller dans le menu **Mes Panoramas > Ajouter**  
- Sélectionner un ZIP Marzipano (contenant au moins un `index.html` et le dossier `tiles`)  
- Le plugin extrait les fichiers, remplace le wrapper `index.html`, et crée un CPT Panorama

### ✏️ Modifier un panorama

- Aller dans **Mes Panoramas**  
- Cliquer sur l’icône “modifier”  
- Uploader un nouveau ZIP pour remplacer les fichiers (le shortcode reste identique)

### 🗑️ Supprimer un panorama

- Depuis la liste, cliquer sur l’icône “supprimer”  
- Le post + les fichiers associés sont effacés du serveur

---

## Dossiers et structure

Les fichiers sont extraits dans :  
`/wp-content/uploads/panoramas/{post_id}/`

Structure générée :

```text
{post_id}/
├── tiles/
├── index.html        ← wrapper injecté
└── panorama.html     ← fichier original renommé
```

Le wrapper par défaut est stocké ici :  
`far-panorama/panorama-wrapper/index.html`

---

## Dépendances

- WordPress 5.8+  
- PHP 7.4+  
- Plugin **Advanced Custom Fields** actif

---

## Licence

GPLv2 — libre d’utiliser, modifier et redistribuer.

---

Développement en cours par **Nycalith (JB)**
