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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #f1f5f9;
            min-height: 100vh;
        }

        /* Navbar */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0 2rem;
            height: 64px;
            background: rgba(8, 12, 22, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.07);
            flex-wrap: wrap;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.4rem;
            font-weight: 800;
            color: #f1f5f9;
            text-decoration: none;
        }

        .navbar-brand i {
            color: #00A86B;
        }

        .navbar-menu {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            margin-left: auto;
            flex-wrap: wrap;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 1rem;
            border-radius: 8px;
            color: #8b9ab5;
            text-decoration: none;
            font: 500 0.87rem 'Inter', sans-serif;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.05);
            color: #f1f5f9;
        }

        .nav-link.active {
            background: linear-gradient(135deg, #00A86B, #007a4e);
            color: #fff;
        }

        .navbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #8b9ab5;
            font: 500 0.85rem 'Inter', sans-serif;
        }

        .topbar-user i {
            color: #00A86B;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: linear-gradient(135deg, #00A86B, #007a4e);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font: 600 0.85rem 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-primary:hover {
            filter: brightness(1.15);
            transform: translateY(-1px);
        }

        .btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: transparent;
            color: #8b9ab5;
            border: 1px solid rgba(255,255,255,0.07);
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font: 500 0.85rem 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-outline:hover {
            border-color: #00A86B;
            color: #00A86B;
        }

        .btn-del {
            background: rgba(231,76,60,0.11);
            color: #e74c3c;
            padding: 0.33rem 0.75rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-del:hover {
            background: rgba(231,76,60,0.24);
        }

        .btn-edit {
            background: rgba(245,158,11,0.11);
            color: #f59e0b;
            padding: 0.33rem 0.75rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-edit:hover {
            background: rgba(245,158,11,0.24);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            min-height: calc(100vh - 64px);
        }

        .products-section {
            padding: 2rem 6% 5rem;
        }

        /* Stats Cards */
        .dashboard-stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.2rem 1.3rem;
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,0.07);
            transition: all 0.3s ease;
            background: linear-gradient(135deg, rgba(0,168,107,0.15), rgba(0,168,107,0.04));
            border-color: rgba(0,168,107,0.2);
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0,168,107,0.18);
            color: #00A86B;
        }

        .stat-value {
            display: block;
            font: 800 1.5rem 'Inter', sans-serif;
            color: #f1f5f9;
        }

        .stat-label {
            display: block;
            font: 0.76rem 'Inter', sans-serif;
            color: #8b9ab5;
            margin-top: 0.15rem;
        }

        /* Admin Section */
        .admin-section {
            margin-top: 2rem;
            background: #111827;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.07);
            overflow: hidden;
        }

        .admin-header {
            padding: 1rem 1.5rem;
            background: linear-gradient(135deg, rgba(0,168,107,0.1), transparent);
            border-bottom: 1px solid rgba(255,255,255,0.07);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .admin-header h3 {
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .admin-content {
            padding: 1.5rem;
        }

        /* Progress Bar */
        .progress-bar {
            background: #1a2535;
            height: 8px;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }

        .progress-fill {
            background: #00A86B;
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        /* Table */
        .table-scroll {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font: 0.86rem 'Inter', sans-serif;
        }

        .data-table th {
            text-align: left;
            padding: 0.85rem 1.2rem;
            color: #8b9ab5;
            font: 600 0.78rem 'Inter', sans-serif;
            text-transform: uppercase;
            background: rgba(255,255,255,0.02);
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }

        .data-table td {
            padding: 0.85rem 1.2rem;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            color: #f1f5f9;
        }

        .tbl-price {
            color: #00A86B;
            font-weight: 700;
        }

        /* Form */
        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #8b9ab5;
            font-weight: 500;
        }

        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 0.75rem;
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 8px;
            color: white;
            font-family: inherit;
        }

        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #00A86B;
        }

        .error-border {
            border-color: #ef4444 !important;
        }

        .error-message {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: block;
        }

        /* Footer */
        .site-footer {
            background: #0A1F44;
            border-top: 1px solid rgba(255,255,255,0.07);
            padding: 1.8rem 6%;
        }

        .footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-brand {
            color: #00A86B;
            font: 700 1.05rem 'Inter', sans-serif;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .site-footer p {
            color: #8b9ab5;
            font-size: 0.82rem;
        }

        .footer-links a {
            color: #8b9ab5;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
        }

        .footer-links a:hover {
            color: #00A86B;
        }

        /* Toast */
        .toast-container {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .toast {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.8rem 1.2rem;
            border-radius: 14px;
            font: 500 0.87rem 'Inter', sans-serif;
            color: #fff;
            box-shadow: 0 8px 24px rgba(0,0,0,0.45);
            animation: toastIn 0.35s ease both;
            min-width: 240px;
            backdrop-filter: blur(10px);
        }

        .toast-success {
            background: rgba(0,168,107,0.93);
            border: 1px solid rgba(0,207,133,0.3);
        }

        .toast-error {
            background: rgba(231,76,60,0.93);
            border: 1px solid rgba(231,76,60,0.3);
        }

        @keyframes toastIn {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar { padding: 0 1rem; }
            .navbar-menu { display: none; position: absolute; top: 64px; left: 0; right: 0; background: #111827; flex-direction: column; align-items: flex-start; padding: 0.75rem 1rem; }
            .navbar-menu.open { display: flex; }
            .products-section { padding: 1.5rem 4% 4rem; }
            .dashboard-stats-row { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body class="front-body">
    <nav class="navbar">
        <a href="index.php?controller=gamification&action=dashboard" class="navbar-brand">
            <i class="fas fa-bolt"></i> Tech<span style="color: #00A86B;">Care</span>
        </a>
        <button class="hamburger" onclick="toggleMobileMenu()" style="background: none; border: none; color: white; font-size: 1.2rem; cursor: pointer; display: none;">☰</button>
        <div class="navbar-menu" id="mobileMenu">
            <!-- ===================================================== -->
            <!-- FRONTOFFICE LINKS (Tous les utilisateurs) -->
            <!-- ===================================================== -->
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
            <a href="index.php?controller=fidelite&action=frontoffice" class="nav-link <?= $activeTab == 'loyalty' ? 'active' : '' ?>">
                <i class="fas fa-hand-holding-heart"></i> Fidélité
            </a>
            <a href="index.php?controller=fidelite&action=manageFront" class="nav-link <?= $activeTab == 'manageFidelite' ? 'active' : '' ?>">
                <i class="fas fa-edit"></i> Mes Demandes
            </a>

            <!-- ===================================================== -->
            <!-- BACKOFFICE LINKS (Administrateur uniquement) -->
            <!-- ===================================================== -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <div style="border-top: 1px solid rgba(255,255,255,0.1); margin: 0.5rem 0; padding-top: 0.5rem;"></div>
                <span style="color: #00A86B; font-size: 0.7rem; padding: 0.25rem 1rem;">⚙️ ADMINISTRATION</span>
                <a href="index.php?controller=fidelite&action=index" class="nav-link <?= $activeTab == 'fidelite' ? 'active' : '' ?>">
                    <i class="fas fa-gem"></i> 📋 Gérer Fidélité
                </a>
                <a href="index.php?controller=badge&action=index" class="nav-link <?= $activeTab == 'badges_admin' ? 'active' : '' ?>">
                    <i class="fas fa-medal"></i> 🏅 Gérer Badges
                </a>
                <a href="index.php?controller=user&action=admin" class="nav-link <?= $activeTab == 'admin' ? 'active' : '' ?>">
                    <i class="fas fa-chart-line"></i> 📊 Dashboard Admin
                </a>
            <?php endif; ?>
        </div>
        <div class="navbar-right">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <button onclick="location.href='index.php?controller=user&action=admin'" class="btn-primary" style="background: linear-gradient(135deg, #7c3aed, #6d28d9);">
                    <i class="fas fa-chart-line"></i> Admin
                </button>
            <?php endif; ?>
            <div class="topbar-user">
                <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['username'] ?? 'Ayari Firas') ?>
            </div>
        </div>
    </nav>
    <div class="main-content">
        <?= $content ?? '<div class="products-section"><p>Chargement...</p></div>' ?>
    </div>
    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-brand"><i class="fas fa-bolt"></i> TechCare</div>
            <p>© 2026 TechCare - Tous droits réservés</p>
            <div class="footer-links">
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                <a href="#"><i class="fab fa-github"></i></a>
            </div>
        </div>
    </footer>
    <div class="toast-container"></div>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            if (menu) menu.classList.toggle('open');
        }

        function showToast(message, type = 'success') {
            const container = document.querySelector('.toast-container');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            let icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
            toast.innerHTML = `<i class="fas ${icon}"></i><span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        <?php if (isset($_SESSION['success'])): ?>
            showToast('<?= $_SESSION['success'] ?>', 'success');
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            showToast('<?= $_SESSION['error'] ?>', 'error');
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        document.querySelector('.hamburger')?.addEventListener('click', toggleMobileMenu);
    </script>
</body>
</html>