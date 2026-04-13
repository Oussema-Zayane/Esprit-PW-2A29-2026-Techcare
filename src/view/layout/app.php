<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechCare - <?= $pageTitle ?? 'Accueil' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/project2A29/assets/css/style.css">
</head>
<body class="front-body">
    
    <nav class="navbar">
        <a href="index.php?controller=gamification&action=dashboard" class="navbar-brand">
            <i class="fas fa-bolt brand-icon"></i>
            Tech<span class="brand-accent">Care</span>
        </a>
        <button class="hamburger" onclick="toggleMobileMenu()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="navbar-menu" id="mobileMenu">
            <a href="index.php?controller=gamification&action=dashboard" class="nav-link <?= $activeTab == 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="index.php?controller=gamification&action=points" class="nav-link <?= $activeTab == 'points' ? 'active' : '' ?>">
                <i class="fas fa-star"></i> Mes Points
            </a>
            <a href="index.php?controller=gamification&action=badges" class="nav-link <?= $activeTab == 'badges' ? 'active' : '' ?>">
                <i class="fas fa-medal"></i> Badges
            </a>
            <a href="index.php?controller=gamification&action=leaderboard" class="nav-link <?= $activeTab == 'leaderboard' ? 'active' : '' ?>">
                <i class="fas fa-trophy"></i> Classement
            </a>
            <a href="index.php?controller=gamification&action=rewards" class="nav-link <?= $activeTab == 'rewards' ? 'active' : '' ?>">
                <i class="fas fa-gift"></i> Récompenses
            </a>
            <a href="index.php?controller=gamification&action=loyalty" class="nav-link <?= $activeTab == 'loyalty' ? 'active' : '' ?>">
                <i class="fas fa-hand-holding-heart"></i> Fidélité
            </a>
        </div>
        <div class="navbar-right">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <button onclick="location.href='index.php?controller=user&action=admin'" class="btn-primary" style="background: linear-gradient(135deg, var(--purple), #6d28d9); padding: 0.4rem 1rem;">
                    <i class="fas fa-chart-line"></i> Admin
                </button>
            <?php endif; ?>
            <div class="topbar-user">
                <i class="fas fa-user-circle"></i>
                <?= htmlspecialchars($_SESSION['username'] ?? 'Ayari Firas') ?>
            </div>
        </div>
    </nav>
    
    <div class="main-content">
        <?= $content ?? '<div class="products-section"><p>Sélectionnez une option dans le menu.</p></div>' ?>
    </div>
    
    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <i class="fas fa-bolt"></i> TechCare
            </div>
            <p>&copy; 2026 TechCare - Tous droits réservés</p>
            <div class="footer-links">
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                <a href="#"><i class="fab fa-github"></i></a>
            </div>
        </div>
    </footer>
    
    <div class="toast-container"></div>
    
    <script src="/project2A29/assets/js/main.js"></script>
    <script>
        <?php if (isset($_SESSION['success'])): ?>
            showToast('<?= $_SESSION['success'] ?>', 'success');
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            showToast('<?= $_SESSION['error'] ?>', 'error');
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </script>
</body>
</html>