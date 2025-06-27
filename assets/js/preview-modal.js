// =============================================================================
// GESTION DE LA MODALE D’APERÇU DES PANORAMAS (FAR‑Panorama)
// =============================================================================
// Ce script est chargé uniquement dans la page admin "Mes panoramas".
// Il permet d’afficher une iframe Marzipano (index.html) dans une modale,
// lorsque l’utilisateur clique sur un bouton "Aperçu".

// INITIALISATION AU CHARGEMENT DU DOM
document.addEventListener('DOMContentLoaded', function () {

    // Récupère les éléments clés de la modale
    const modal = document.getElementById('panoramaModal');       // le conteneur de la modale
    const iframe = document.getElementById('panoramaIframe');     // l’iframe qui affichera le panorama
    const closeBtn = modal.querySelector('.close');               // le bouton (X) de fermeture


    // AFFICHAGE DE LA MODALE AU CLIC SUR UN BOUTON D’APERÇU

    document.querySelectorAll('.preview-panorama').forEach(button => {
        button.addEventListener('click', function () {

            // Récupère l’URL du panorama depuis l’attribut data-url du bouton
            const url = this.dataset.url;

            // Affecte l’URL à l’iframe, puis affiche la modale
            iframe.src = url;
            modal.style.display = 'block';
        });
    });


    // FERMETURE DE LA MODALE (Clic sur le X)

    closeBtn.addEventListener('click', function () {
        modal.style.display = 'none';  // Cache la modale
        iframe.src = '';               // Vide l’iframe pour stopper le son/chargement
    });


    // FERMETURE DE LA MODALE (Clic en dehors de la modale)

    window.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            iframe.src = '';
        }
    });
});
