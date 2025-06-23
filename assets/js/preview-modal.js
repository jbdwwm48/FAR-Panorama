// Fichier : preview-modal.js
// ✅ Script qui gère l'ouverture et la fermeture de la modale d'aperçu Marzipano

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('panoramaModal');
    const iframe = document.getElementById('panoramaIframe');
    const closeBtn = modal.querySelector('.close');

    // Ouvre la modale avec le bon URL
    document.querySelectorAll('.preview-panorama').forEach(button => {
        button.addEventListener('click', function () {
            const url = this.dataset.url;
            iframe.src = url;
            modal.style.display = 'block';
        });
    });

    // Ferme la modale au clic sur le X
    closeBtn.addEventListener('click', function () {
        modal.style.display = 'none';
        iframe.src = ''; // Vide l’iframe pour couper le son ou les ressources
    });

    // Ferme si on clique en dehors du contenu
    window.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            iframe.src = '';
        }
    });
});
