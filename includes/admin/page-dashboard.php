<?php
// Affichage de la page dashboard FAR-Panorama

function far_panorama_dashboard_page()
{
?>
    <div class="wrap">
        <h1>Bienvenue dans FAR-Panorama</h1>
        <p>Gère tes panoramas 360° facilement depuis ce tableau de bord.</p>

        <a href="<?php echo admin_url('admin.php?page=far-panorama-list'); ?>"
            class="button button-primary" style="margin-right: 10px;">
            📋 Liste des panoramas
        </a>

        <a href="<?php echo admin_url('admin.php?page=far-panorama-upload'); ?>"
            class="button button-secondary">
            ➕ Ajouter un panorama
        </a>

        <hr style="margin-top: 20px;">

        <h2>À venir</h2>
        <p>Un tutoriel pour bien commencer arrivera bientôt ici ! 🚀</p>
    </div>
<?php
}
