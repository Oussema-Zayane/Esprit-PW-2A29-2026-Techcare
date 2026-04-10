<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.html");
    exit();
}

$pageTitle = "Mon Profil";
$activeTab = "profile";
$content = "";

// Démarrer la bufferisation
ob_start();
?>

<div class="hero">
    <div class="hero-content">
        <div class="hero-badge">
            <i class="fas fa-user-check"></i> Bienvenue
        </div>
        <h1 class="hero-title">
            Bonjour <?= htmlspecialchars($_SESSION['prenom']) ?> <span class="gradient-text">👋</span>
        </h1>
        <p class="hero-subtitle">
            Bienvenue sur votre espace TechCare. Gérez votre compte et découvrez nos services.
        </p>
    </div>
    <div class="hero-visual">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <i class="fas fa-microchip hero-main-icon"></i>
    </div>
</div>

<div class="products-section">
    <div class="section-header">
        <div class="section-title">
            <i class="fas fa-id-card" style="color: #00A86B;"></i>
            Mes Informations
        </div>
    </div>
    
    <div class="stats-grid" style="max-width: 600px;">
        <div class="stat-card c-green">
            <div class="stat-icon"><i class="fas fa-envelope"></i></div>
            <div>
                <span class="stat-value"><?= htmlspecialchars($_SESSION['email']) ?></span>
                <span class="stat-label">Email</span>
            </div>
        </div>
        <div class="stat-card c-blue">
            <div class="stat-icon"><i class="fas fa-user"></i></div>
            <div>
                <span class="stat-value"><?= htmlspecialchars($_SESSION['nom'] . ' ' . $_SESSION['prenom']) ?></span>
                <span class="stat-label">Nom complet</span>
            </div>
        </div>
        <div class="stat-card c-purple">
            <div class="stat-icon"><i class="fas fa-tag"></i></div>
            <div>
                <span class="stat-value"><?= htmlspecialchars($_SESSION['role']) ?></span>
                <span class="stat-label">Rôle</span>
            </div>
        </div>
    </div>
    
    <div class="section-header" style="margin-top: 2rem;">
        <div class="section-title">
            <i class="fas fa-cog" style="color: #00A86B;"></i>
            Actions rapides
        </div>
    </div>
    
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="/User/Controller/logout.php" class="btn-primary">
            <i class="fas fa-sign-out-alt"></i> Se déconnecter
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include "../layouts/app.php";
?>