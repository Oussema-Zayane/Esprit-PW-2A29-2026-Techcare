<div class="sidebar">
    <div class="sidebar-header">
        <h2><i class="fa-solid fa-bolt"></i> TechCare</h2>
        <p>Back Office Administration</p>
    </div>
    <div class="sidebar-nav">
        <a href="index.php?action=dashboard" class="nav-item <?= (isset($_GET['action']) && $_GET['action'] == 'dashboard') ? 'active' : '' ?>">
            <i class="fa-solid fa-chart-line"></i>
            <span>Dashboard</span>
        </a>
        <a href="index.php?action=liste" class="nav-item <?= (isset($_GET['action']) && $_GET['action'] == 'liste') ? 'active' : '' ?>">
            <i class="fa-solid fa-tags"></i>
            <span>Gestion Abonnements</span>
        </a>
        <a href="index.php?action=ajouter" class="nav-item <?= (isset($_GET['action']) && $_GET['action'] == 'ajouter') ? 'active' : '' ?>">
            <i class="fa-solid fa-plus-circle"></i>
            <span>Ajouter Abonnement</span>
        </a>
        <a href="index.php?action=statistiques" class="nav-item <?= (isset($_GET['action']) && $_GET['action'] == 'statistiques') ? 'active' : '' ?>">
            <i class="fa-solid fa-chart-simple"></i>
            <span>Statistiques</span>
        </a>
        <hr style="border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 20px 0;">
        <a href="index.php?page=accueil" class="nav-item">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Retour au site</span>
        </a>
        <a href="index.php?action=logout" class="nav-item" style="color: #ff6b6b;">
            <i class="fa-solid fa-sign-out-alt"></i>
            <span>Déconnexion</span>
        </a>
    </div>
</div>