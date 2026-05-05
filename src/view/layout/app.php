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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #f1f5f9; min-height: 100vh; }
        
        /* Top Navbar */
        .top-navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            height: 64px;
            background: rgba(8,12,22,0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.07);
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .logo { display: flex; align-items: center; gap: 0.5rem; font-size: 1.3rem; font-weight: 800; color: #f1f5f9; text-decoration: none; }
        .logo i { color: #00A86B; }
        .top-navbar-right { display: flex; align-items: center; gap: 1rem; }
        .btn-home { display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; border: none; border-radius: 30px; padding: 0.4rem 1rem; font: 600 0.85rem 'Inter', sans-serif; text-decoration: none; transition: all 0.3s ease; }
        .btn-home:hover { transform: translateY(-2px); background: linear-gradient(135deg, #2563eb, #1e40af); }
        .top-user { display: flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.08); padding: 0.3rem 0.8rem; border-radius: 30px; }
        .top-user i { color: #00A86B; }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 64px;
            left: 0;
            bottom: 0;
            width: 280px;
            background: #111827;
            border-right: 1px solid rgba(255,255,255,0.07);
            overflow-y: auto;
            z-index: 900;
            transition: all 0.3s ease;
        }
        .sidebar-menu { padding: 1.5rem 0; }
        .sidebar-section { padding: 0 1rem; margin-bottom: 1.5rem; }
        .sidebar-title { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: #00A86B; margin-bottom: 0.75rem; padding-left: 0.75rem; }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.7rem 1rem;
            border-radius: 12px;
            color: #8b9ab5;
            text-decoration: none;
            font: 500 0.9rem 'Inter', sans-serif;
            transition: all 0.3s ease;
            margin-bottom: 0.25rem;
        }
        .sidebar-link:hover { background: rgba(255,255,255,0.05); color: #f1f5f9; }
        .sidebar-link.active { background: linear-gradient(135deg, rgba(0,168,107,0.2), rgba(0,168,107,0.05)); color: #00A86B; border-left: 3px solid #00A86B; }
        .sidebar-link i { width: 22px; text-align: center; font-size: 1.1rem; }
        .sidebar-divider { height: 1px; background: rgba(255,255,255,0.07); margin: 1rem 1rem; }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            margin-top: 64px;
            padding: 2rem;
            min-height: calc(100vh - 64px);
        }
        .products-section { max-width: 1400px; margin: 0 auto; }
        
        /* Stats Cards */
        .dashboard-stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { display: flex; align-items: center; gap: 1rem; padding: 1.2rem 1.3rem; border-radius: 16px; border: 1px solid rgba(255,255,255,0.07); background: linear-gradient(135deg, rgba(0,168,107,0.1), rgba(0,168,107,0.03)); border-color: rgba(0,168,107,0.15); transition: all 0.3s ease; }
        .stat-card:hover { transform: translateY(-3px); border-color: #00A86B; }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: rgba(0,168,107,0.15); color: #00A86B; font-size: 1.3rem; }
        .stat-value { display: block; font: 800 1.6rem 'Inter', sans-serif; color: #f1f5f9; }
        .stat-label { display: block; font: 0.75rem 'Inter', sans-serif; color: #8b9ab5; margin-top: 0.15rem; }
        
        /* Admin Section */
        .admin-section { margin-top: 2rem; background: #111827; border-radius: 20px; border: 1px solid rgba(255,255,255,0.07); overflow: hidden; }
        .admin-header { padding: 1rem 1.5rem; background: linear-gradient(135deg, rgba(0,168,107,0.1), transparent); border-bottom: 1px solid rgba(255,255,255,0.07); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; }
        .admin-header h3 { font-size: 1.1rem; display: flex; align-items: center; gap: 0.5rem; }
        .admin-content { padding: 1.5rem; }
        
        /* Tables */
        .table-scroll { overflow-x: auto; }
        .data-table { width: 100%; border-collapse: collapse; font: 0.86rem 'Inter', sans-serif; }
        .data-table th { text-align: left; padding: 0.85rem 1.2rem; color: #8b9ab5; font: 600 0.78rem 'Inter', sans-serif; text-transform: uppercase; background: rgba(255,255,255,0.02); border-bottom: 1px solid rgba(255,255,255,0.07); }
        .data-table td { padding: 0.85rem 1.2rem; border-bottom: 1px solid rgba(255,255,255,0.04); color: #f1f5f9; }
        .tbl-price { color: #00A86B; font-weight: 700; }
        
        /* Progress Bar */
        .progress-bar { background: #1a2535; height: 8px; border-radius: 10px; overflow: hidden; margin: 10px 0; }
        .progress-fill { background: #00A86B; height: 100%; border-radius: 10px; transition: width 0.5s ease; }
        
        /* Buttons */
        .btn-primary { display: inline-flex; align-items: center; gap: 0.5rem; background: linear-gradient(135deg, #00A86B, #007a4e); color: #fff; border: none; border-radius: 30px; padding: 0.5rem 1.2rem; font: 600 0.85rem 'Inter', sans-serif; cursor: pointer; transition: all 0.3s ease; text-decoration: none; }
        .btn-primary:hover { transform: translateY(-2px); filter: brightness(1.1); }
        .btn-outline { display: inline-flex; align-items: center; gap: 0.5rem; background: transparent; color: #8b9ab5; border: 1px solid rgba(255,255,255,0.15); border-radius: 30px; padding: 0.5rem 1.2rem; font: 600 0.85rem 'Inter', sans-serif; cursor: pointer; transition: all 0.3s ease; text-decoration: none; }
        .btn-outline:hover { border-color: #00A86B; color: #00A86B; }
        
        /* Footer */
        .footer { background: #0A1F44; border-top: 1px solid rgba(255,255,255,0.07); padding: 1rem 2rem; text-align: center; margin-top: 2rem; }
        .footer p { color: #64748b; font-size: 0.8rem; }
        
        /* Toast */
        .toast-container { position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.5rem; }
        .toast { display: flex; align-items: center; gap: 0.7rem; padding: 0.8rem 1.2rem; border-radius: 14px; font: 500 0.87rem 'Inter', sans-serif; color: #fff; box-shadow: 0 8px 24px rgba(0,0,0,0.45); animation: toastIn 0.35s ease both; min-width: 240px; backdrop-filter: blur(10px); }
        .toast-success { background: rgba(0,168,107,0.93); }
        .toast-error { background: rgba(231,76,60,0.93); }
        @keyframes toastIn { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }
        
        /* Responsive */
        .menu-toggle { display: none; position: fixed; top: 72px; left: 1rem; z-index: 1001; background: #00A86B; border: none; color: white; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; font-size: 1.2rem; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 1rem; margin-top: 64px; }
            .menu-toggle { display: flex; align-items: center; justify-content: center; }
            .dashboard-stats-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <button class="menu-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
    
    <!-- Top Navbar -->
    <nav class="top-navbar">
        <a href="index.php?controller=gamification&action=dashboard" class="logo">
            <i class="fas fa-bolt"></i> Tech<span style="color: #00A86B;">Care</span>
        </a>
        <div class="top-navbar-right">
            <a href="/Esprit-PW-2A29-2526-ProjetWeb/index.html" class="btn-home"><i class="fas fa-home"></i> Accueil</a>
            <div class="top-user">
                <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['username'] ?? 'Ayari Firas') ?>
                <span id="userAvatar" style="font-size: 1rem;">
                    <?php
                    $points = $_SESSION['user_points'] ?? 1250;
                    if ($points >= 5000) echo '🐉';
                    elseif ($points >= 2500) echo '🦅';
                    elseif ($points >= 1000) echo '🐺';
                    elseif ($points >= 500) echo '🦊';
                    elseif ($points >= 100) echo '🐣';
                    else echo '🥚';
                    ?>
                </span>
            </div>
        </div>
    </nav>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-menu">
            <!-- Section Utilisateur -->
            <div class="sidebar-section">
                <div class="sidebar-title"><i class="fas fa-user"></i> MON ESPACE</div>
                <a href="index.php?controller=gamification&action=dashboard" class="sidebar-link <?= $activeTab == 'dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-home"></i> <span>Tableau de bord</span>
                </a>
                <a href="index.php?controller=gamification&action=points" class="sidebar-link <?= $activeTab == 'points' ? 'active' : '' ?>">
                    <i class="fas fa-star"></i> <span>Mes points</span>
                </a>
                <a href="index.php?controller=gamification&action=badges" class="sidebar-link <?= $activeTab == 'badges' ? 'active' : '' ?>">
                    <i class="fas fa-medal"></i> <span>Mes badges</span>
                </a>
                <a href="index.php?controller=gamification&action=leaderboard" class="sidebar-link <?= $activeTab == 'leaderboard' ? 'active' : '' ?>">
                    <i class="fas fa-trophy"></i> <span>Classement</span>
                </a>
                <a href="index.php?controller=gamification&action=rewards" class="sidebar-link <?= $activeTab == 'rewards' ? 'active' : '' ?>">
                    <i class="fas fa-gift"></i> <span>Récompenses</span>
                </a>
                <a href="index.php?controller=gamification&action=statistiques" class="sidebar-link <?= $activeTab == 'statistiques' ? 'active' : '' ?>">
                    <i class="fas fa-chart-bar"></i> <span>Statistiques</span>
                </a>
                <a href="index.php?controller=gamification&action=parrainage" class="sidebar-link <?= $activeTab == 'parrainage' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> <span>Parrainage</span>
                </a>
            </div>
            
            <div class="sidebar-divider"></div>
            
            <!-- Section Fidélité -->
            <div class="sidebar-section">
                <div class="sidebar-title"><i class="fas fa-hand-holding-heart"></i> FIDÉLITÉ</div>
                <a href="index.php?controller=fidelite&action=frontoffice" class="sidebar-link <?= $activeTab == 'loyalty' ? 'active' : '' ?>">
                    <i class="fas fa-tag"></i> <span>Offres fidélité</span>
                </a>
                <a href="index.php?controller=fidelite&action=manageFront" class="sidebar-link <?= $activeTab == 'manageFidelite' ? 'active' : '' ?>">
                    <i class="fas fa-edit"></i> <span>Mes demandes</span>
                </a>
            </div>
            
            <!-- Section Administration -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <div class="sidebar-divider"></div>
                <div class="sidebar-section">
                    <div class="sidebar-title"><i class="fas fa-shield-alt"></i> ADMINISTRATION</div>
                    <a href="index.php?controller=fidelite&action=index" class="sidebar-link <?= $activeTab == 'fidelite' ? 'active' : '' ?>">
                        <i class="fas fa-gem"></i> <span>Gérer la fidélité</span>
                    </a>
                    <a href="index.php?controller=badge&action=index" class="sidebar-link <?= $activeTab == 'badges_admin' ? 'active' : '' ?>">
                        <i class="fas fa-medal"></i> <span>Gérer les badges</span>
                    </a>
                    <a href="index.php?controller=badge&action=fideliteBadges" class="sidebar-link <?= $activeTab == 'fideliteBadges' ? 'active' : '' ?>">
                        <i class="fas fa-link"></i> <span>Programme fidélité</span>
                    </a>
                    <a href="index.php?controller=user&action=admin" class="sidebar-link <?= $activeTab == 'admin' ? 'active' : '' ?>">
                        <i class="fas fa-chart-line"></i> <span>Dashboard admin</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <?= $content ?? '<div class="products-section"><p>Chargement...</p></div>' ?>
    </div>
    
    <footer class="footer">
        <div class="footer-inner">
            <p>© 2026 TechCare - Tous droits réservés | Gamification Module</p>
        </div>
    </footer>
    
    <div class="toast-container"></div>
    
    <script>
        function toggleSidebar() { document.getElementById('sidebar')?.classList.toggle('open'); }
        function showToast(message, type = 'success') {
            const container = document.querySelector('.toast-container');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i><span>${message}</span>`;
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
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', () => { document.getElementById('sidebar')?.classList.remove('open'); });
        });
    </script>
</body>
</html>