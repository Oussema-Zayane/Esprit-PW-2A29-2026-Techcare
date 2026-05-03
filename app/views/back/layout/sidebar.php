<div class="sidebar">
    <div class="sidebar-header">
        <h2><i class="fa-solid fa-bolt"></i> TechCare</h2>
        <p>Back Office Administration</p>
    </div>
    <div class="sidebar-nav">
        <!-- Dashboard -->
        <a href="index.php?action=dashboard" class="nav-item <?= (isset($_GET['action']) && $_GET['action'] == 'dashboard') ? 'active' : '' ?>">
            <i class="fa-solid fa-chart-line"></i>
            <span>Dashboard</span>
        </a>

        <!-- Gestion des Abonnements -->
        <a href="index.php?action=liste" class="nav-item <?= (isset($_GET['action']) && $_GET['action'] == 'liste') ? 'active' : '' ?>">
            <i class="fa-solid fa-tags"></i>
            <span>Gestion Abonnements</span>
        </a>

        <!-- Ajouter Abonnement -->
        <a href="index.php?action=ajouter" class="nav-item <?= (isset($_GET['action']) && $_GET['action'] == 'ajouter') ? 'active' : '' ?>">
            <i class="fa-solid fa-plus-circle"></i>
            <span>Ajouter Abonnement</span>
        </a>

        <!-- Types d'Abonnement (Offres) -->
        <a href="index.php?action=types" class="nav-item <?= (isset($_GET['action']) && $_GET['action'] == 'types') ? 'active' : '' ?>">
            <i class="fa-solid fa-layer-group"></i>
            <span>Types d'Abonnement</span>
        </a>

        <!-- Options supplémentaires -->
        <a href="index.php?action=offres" class="nav-item <?= (isset($_GET['action']) && $_GET['action'] == 'offres') ? 'active' : '' ?>">
            <i class="fa-solid fa-list-check"></i>
            <span>Options supplémentaires</span>
        </a>

        <!-- Statistiques -->
        <a href="index.php?action=statistiques" class="nav-item <?= (isset($_GET['action']) && $_GET['action'] == 'statistiques') ? 'active' : '' ?>">
            <i class="fa-solid fa-chart-simple"></i>
            <span>Statistiques</span>
        </a>

        <!-- Recherche Avancée -->
        <a href="index.php?action=recherche" class="nav-item <?= (isset($_GET['action']) && $_GET['action'] == 'recherche') ? 'active' : '' ?>">
            <i class="fa-solid fa-magnifying-glass"></i>
            <span>Recherche Avancée</span>
        </a>

        <hr style="border: none; border-top: 1px solid var(--border); margin: 20px 0;">

        <!-- Retour au site (Front Office) -->
        <a href="index.php?page=accueil" class="nav-item">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Retour au site</span>
        </a>

        <!-- Déconnexion -->
        <a href="index.php?action=logout" class="nav-item" style="color: #ff6b6b;">
            <i class="fa-solid fa-sign-out-alt"></i>
            <span>Déconnexion</span>
        </a>
    </div>
</div>