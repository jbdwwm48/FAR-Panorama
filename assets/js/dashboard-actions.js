// =============================================================================
// TOGGLE DU FORMULAIRE D’AJOUT DE PANORAMA DANS LE DASHBOARD (FAR‑Panorama)
// =============================================================================
// Ce script est chargé uniquement dans le tableau de bord admin du plugin.
// Il permet d’afficher ou de cacher dynamiquement le formulaire d’ajout de panorama
// lorsque l’utilisateur clique sur le bouton “Ajouter un panorama”.

// INIT AU CHARGEMENT DU DOM
document.addEventListener('DOMContentLoaded', () => {

    // Récupère le bouton qui déclenche l’ouverture/fermeture du formulaire
    const toggleButton = document.getElementById('toggle-upload-form');

    // Récupère le conteneur du formulaire d’upload (caché au chargement)
    const formContainer = document.getElementById('upload-form-container');

    // Si les deux éléments existent bien dans la page…
    if (toggleButton && formContainer) {

        // …alors on ajoute un écouteur de clic sur le bouton
        toggleButton.addEventListener('click', () => {

            // On inverse dynamiquement l’état du conteneur :
            // s’il est caché (display:none), on l’affiche, sinon on le masque
            formContainer.style.display = formContainer.style.display === 'none' ? 'block' : 'none';
        });
    }

    // === GESTION DU NOM DE FICHIER SÉLECTIONNÉ DANS LE CHAMP FILE INPUT ===

    // Récupère l’input file stylé
    const fileInput = document.querySelector('.far-panorama-file-input');

    // Récupère le span qui affiche le nom du fichier sélectionné
    const fileText = document.querySelector('.file-selected-text');

    // Si les deux existent…
    if (fileInput && fileText) {

        // Ajoute un écouteur sur le changement du fichier sélectionné
        fileInput.addEventListener('change', () => {

            // Affiche le nom du fichier si présent, sinon message par défaut
            if (fileInput.files.length > 0) {
                fileText.textContent = fileInput.files[0].name;
            } else {
                fileText.textContent = 'Aucun fichier sélectionné';
            }
        });
    }
});
