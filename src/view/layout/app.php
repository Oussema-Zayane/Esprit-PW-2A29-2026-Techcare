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
            padding-top: 70px; /* Pour éviter que le contenu soit caché sous la navbar fixe */
        }

        /* Navbar FIXE (sticky) - reste en haut quand on scrolle */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0 2rem;
            height: 70px;
            background: rgba(8, 12, 22, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.07);
            flex-wrap: wrap;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.4rem;
            font-weight: 800;
            color: #f1f5f9;
            text-decoration: none;
            flex-shrink: 0;
        }

        .navbar-brand i {
            color: #00A86B;
        }

        /* Menu de navigation - centre */
        .navbar-menu {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            flex-wrap: wrap;
            flex: 1;
            justify-content: center;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 1rem;
            border-radius: 30px;
            color: #8b9ab5;
            text-decoration: none;
            font: 500 0.85rem 'Inter', sans-serif;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.08);
            color: #f1f5f9;
        }

        .nav-link.active {
            background: linear-gradient(135deg, #00A86B, #007a4e);
            color: #fff;
        }

        /* Partie droite de la navbar */
        .navbar-right {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            flex-shrink: 0;
        }

        /* Bouton Retour à l'accueil */
        .btn-home {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border: none;
            border-radius: 30px;
            padding: 0.45rem 1.2rem;
            font: 600 0.85rem 'Inter', sans-serif;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(59,130,246,0.3);
        }

        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59,130,246,0.4);
            background: linear-gradient(135deg, #2563eb, #1e40af);
        }

        .btn-home:active {
            transform: translateY(0);
        }

        /* Profil utilisateur */
        .topbar-user {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.08);
            padding: 0.4rem 1rem;
            border-radius: 30px;
            color: #f1f5f9;
            font: 500 0.85rem 'Inter', sans-serif;
            backdrop-filter: blur(4px);
        }

        .topbar-user i {
            color: #00A86B;
            font-size: 1rem;
        }

        /* Hamburger menu (mobile) */
        .hamburger {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.5rem;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            min-height: calc(100vh - 70px);
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
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.07);
            transition: all 0.3s ease;
            background: linear-gradient(135deg, rgba(0,168,107,0.15), rgba(0,168,107,0.04));
            border-color: rgba(0,168,107,0.2);
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0,168,107,0.18);
            color: #00A86B;
            font-size: 1.3rem;
        }

        .stat-value {
            display: block;
            font: 800 1.6rem 'Inter', sans-serif;
            color: #f1f5f9;
        }

        .stat-label {
            display: block;
            font: 0.75rem 'Inter', sans-serif;
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
            padding: 1.5rem 6%;
            margin-top: 2rem;
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
            font: 700 1rem 'Inter', sans-serif;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .site-footer p {
            color: #8b9ab5;
            font-size: 0.8rem;
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
        @media (max-width: 992px) {
            .navbar-menu {
                gap: 0.2rem;
            }
            .nav-link {
                padding: 0.45rem 0.7rem;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 768px) {
            body {
                padding-top: 60px;
            }
            .navbar {
                flex-wrap: wrap;
                padding: 0.5rem 1rem;
                height: auto;
            }
            .navbar-brand {
                order: 1;
            }
            .hamburger {
                display: block;
                order: 2;
            }
            .navbar-right {
                order: 3;
                margin-left: auto;
            }
            .navbar-menu {
                display: none;
                width: 100%;
                order: 4;
                flex-direction: column;
                align-items: flex-start;
                padding: 0.75rem 0;
                gap: 0.5rem;
            }
            .navbar-menu.open {
                display: flex;
            }
            .nav-link {
                width: 100%;
                padding: 0.5rem 0;
            }
            .products-section {
                padding: 1.5rem 4% 4rem;
            }
            .dashboard-stats-row {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 480px) {
            .dashboard-stats-row {
                grid-template-columns: 1fr;
            }
            .navbar-right {
                gap: 0.4rem;
            }
            .btn-home {
                padding: 0.35rem 0.8rem;
                font-size: 0.75rem;
            }
            .topbar-user {
                padding: 0.3rem 0.7rem;
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body class="front-body">
    <nav class="navbar">
        <a href="index.php?controller=gamification&action=dashboard" class="navbar-brand">
            <i class="fas fa-bolt"></i> Tech<span style="color: #00A86B;">Care</span>
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
            <a href="index.php?controller=fidelite&action=frontoffice" class="nav-link <?= $activeTab == 'loyalty' ? 'active' : '' ?>">
                <i class="fas fa-hand-holding-heart"></i> Fidélité
            </a>
            <a href="index.php?controller=fidelite&action=manageFront" class="nav-link <?= $activeTab == 'manageFidelite' ? 'active' : '' ?>">
                <i class="fas fa-edit"></i> Mes Demandes
            </a>

            <!-- BACKOFFICE LINKS (Admin only) -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <div style="border-top: 1px solid rgba(255,255,255,0.1); margin: 0.5rem 0; padding-top: 0.5rem;"></div>
                <span style="color: #00A86B; font-size: 0.7rem; padding: 0.25rem 0;">⚙️ ADMINISTRATION</span>
                <a href="index.php?controller=fidelite&action=index" class="nav-link <?= $activeTab == 'fidelite' ? 'active' : '' ?>">
                    <i class="fas fa-gem"></i> Gérer Fidélité
                </a>
                <a href="index.php?controller=badge&action=index" class="nav-link <?= $activeTab == 'badges_admin' ? 'active' : '' ?>">
                    <i class="fas fa-medal"></i> Gérer Badges
                </a>
                <a href="index.php?controller=fidelite&action=showByUser" class="nav-link <?= $activeTab == 'jointure' ? 'active' : '' ?>">
                    <i class="fas fa-link"></i> Jointure SQL
                </a>
                <a href="index.php?controller=user&action=admin" class="nav-link <?= $activeTab == 'admin' ? 'active' : '' ?>">
                    <i class="fas fa-chart-line"></i> Dashboard Admin
                </a>
            <?php endif; ?>
        </div>
        
        <div class="navbar-right">
            <!-- Bouton Retour à l'accueil -->
            <a href="/project2A29/index.html" class="btn-home">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            
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

        // Fermer le menu mobile quand on clique sur un lien
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('mobileMenu')?.classList.remove('open');
            });
        });
    </script>
</body>
</html>