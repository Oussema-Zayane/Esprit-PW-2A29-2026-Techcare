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
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        .admin-section {
            margin-top: 2rem;
            background: var(--surface);
            border-radius: var(--r-lg);
            border: 1px solid var(--border);
            overflow: hidden;
        }
        .admin-header {
            padding: 1rem 1.5rem;
            background: linear-gradient(135deg, rgba(0,168,107,0.1), transparent);
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        .btn-admin {
            background: linear-gradient(135deg, var(--purple), #6d28d9);
            color: white;
            border: none;
            border-radius: var(--r-sm);
            padding: 0.5rem 1rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            transition: var(--t);
        }
        .btn-admin:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }
        .admin-tabs {
            display: flex;
            gap: 0.5rem;
            border-bottom: 1px solid var(--border);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        .admin-tab-btn {
            background: none;
            border: none;
            padding: 0.6rem 1.2rem;
            color: var(--txt-muted);
            cursor: pointer;
            border-radius: var(--r-sm);
            transition: var(--t);
            font-weight: 500;
        }
        .admin-tab-btn:hover {
            background: rgba(255,255,255,0.05);
            color: var(--txt);
        }
        .admin-tab-btn.active {
            background: var(--green);
            color: white;
        }
        .admin-tab-pane {
            display: none;
        }
        .admin-tab-pane.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .close-admin-btn {
            background: none;
            border: none;
            color: var(--txt-muted);
            cursor: pointer;
            font-size: 1.2rem;
            padding: 0.3rem;
        }
        .close-admin-btn:hover {
            color: var(--danger);
        }
        .dashboard-stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .chart-container {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--r-lg);
            padding: 1.5rem;
            margin-top: 1.5rem;
        }
        .chart-container h4 {
            margin-bottom: 1rem;
            color: var(--txt);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .chart-wrapper {
            max-width: 400px;
            margin: 0 auto;
        }
        .toast-container {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            pointer-events: none;
        }
        .toast {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.8rem 1.2rem;
            border-radius: var(--r-md);
            font: 500 0.87rem var(--font);
            color: #fff;
            box-shadow: var(--sh-lg);
            animation: toastIn 0.35s cubic-bezier(0.34,1.56,0.64,1) both;
            pointer-events: all;
            min-width: 240px;
            max-width: 340px;
            backdrop-filter: blur(10px);
        }
        .toast-success { background: rgba(0,168,107,0.93); border: 1px solid rgba(0,207,133,0.3); }
        .toast-error   { background: rgba(231,76,60,0.93);  border: 1px solid rgba(231,76,60,0.3); }
        @keyframes toastIn { from { opacity:0; transform:translateX(30px); } to { opacity:1; transform:translateX(0); } }
        @keyframes toastOut { from { opacity:1; transform:translateX(0); } to { opacity:0; transform:translateX(30px); } }
        .toast-hide { animation: toastOut 0.3s ease forwards; }
    </style>
</head>
<body class="front-body">
    
    <nav class="navbar">
        <a href="profile.php" class="navbar-brand">
            <i class="fas fa-bolt brand-icon"></i>
            Tech<span class="brand-accent">Care</span>
        </a>
        <button class="hamburger" onclick="toggleMobileMenu()">
            <i class="fas fa-bars"></i>
        </button>
        <div class="navbar-menu" id="mobileMenu">
            <a href="profile.php" class="nav-link <?= $activeTab == 'profile' ? 'active' : '' ?>">
                <i class="fas fa-home"></i> Accueil
            </a>
            <a href="products.php" class="nav-link <?= $activeTab == 'products' ? 'active' : '' ?>">
                <i class="fas fa-box"></i> Produits
            </a>
            <a href="cart.php" class="nav-link <?= $activeTab == 'cart' ? 'active' : '' ?>">
                <i class="fas fa-shopping-cart"></i> Panier
            </a>
        </div>
        <div class="navbar-right">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <button onclick="toggleAdminPanel()" class="btn-admin" id="adminToggleBtn">
                    <i class="fas fa-chart-line"></i> Admin
                </button>
            <?php endif; ?>
            <div class="topbar-user">
                <i class="fas fa-user-circle"></i>
                <?= htmlspecialchars($_SESSION['prenom'] ?? 'Invité') ?>
            </div>
        </div>
    </nav>
    
    <div class="main-content">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
        <div id="adminPanel" style="display: none;">
            <div class="products-section">
                <div class="admin-section">
                    <div class="admin-header">
                        <h3><i class="fas fa-chart-line" style="color: var(--green);"></i> Panel d'administration</h3>
                        <button onclick="toggleAdminPanel()" class="close-admin-btn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="admin-content">
                        <div class="admin-tabs">
                            <button class="admin-tab-btn active" onclick="showAdminTab('dashboard')">
                                <i class="fas fa-chart-simple"></i> Dashboard
                            </button>
                            <button class="admin-tab-btn" onclick="showAdminTab('users')">
                                <i class="fas fa-users"></i> Utilisateurs
                            </button>
                            <button class="admin-tab-btn" onclick="showAdminTab('products')">
                                <i class="fas fa-box"></i> Produits
                            </button>
                            <button class="admin-tab-btn" onclick="showAdminTab('orders')">
                                <i class="fas fa-shopping-cart"></i> Commandes
                            </button>
                        </div>
                        
                        <div id="adminTabDashboard" class="admin-tab-pane active">
                            <div id="dashboardContent">
                                <div class="dashboard-stats-row" id="statsGrid"></div>
                                <div class="chart-container" id="chartContainer" style="display: none;">
                                    <h4><i class="fas fa-chart-pie" style="color: var(--green);"></i> Répartition des utilisateurs par rôle</h4>
                                    <div class="chart-wrapper">
                                        <canvas id="roleChart"></canvas>
                                    </div>
                                    <div class="chart-legend" id="chartLegend"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="adminTabUsers" class="admin-tab-pane">
                            <div id="usersContent"></div>
                        </div>
                        
                        <div id="adminTabProducts" class="admin-tab-pane">
                            <div id="productsContent"></div>
                        </div>
                        
                        <div id="adminTabOrders" class="admin-tab-pane">
                            <div id="ordersContent"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
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

    <script>
    let roleChart = null;
    
    function toggleMobileMenu() {
        document.getElementById('mobileMenu')?.classList.toggle('open');
    }
    
    function toggleAdminPanel() {
        const panel = document.getElementById('adminPanel');
        if (panel.style.display === 'none') {
            panel.style.display = 'block';
            loadDashboardStats();
            loadChartData();
            loadUsersList();
        } else {
            panel.style.display = 'none';
        }
    }
    
    function showAdminTab(tabName) {
        document.querySelectorAll('.admin-tab-pane').forEach(pane => pane.classList.remove('active'));
        document.getElementById('adminTab' + tabName.charAt(0).toUpperCase() + tabName.slice(1)).classList.add('active');
        
        document.querySelectorAll('.admin-tab-btn').forEach(btn => btn.classList.remove('active'));
        if (event && event.target) event.target.classList.add('active');
        
        if (tabName === 'dashboard') { loadDashboardStats(); loadChartData(); }
        if (tabName === 'users') loadUsersList();
        if (tabName === 'products') loadProductsList();
        if (tabName === 'orders') loadOrdersList();
    }
    
    function loadDashboardStats() {
        fetch('../../Controller/AdminAjax.php?action=dashboard_stats')
            .then(response => response.json())
            .then(data => {
                document.getElementById('statsGrid').innerHTML = `
                    <div class="stat-card c-green"><div class="stat-icon"><i class="fas fa-users"></i></div><div><span class="stat-value">${data.totalUsers}</span><span class="stat-label">Utilisateurs</span></div></div>
                    <div class="stat-card c-purple"><div class="stat-icon"><i class="fas fa-box"></i></div><div><span class="stat-value">${data.totalProducts}</span><span class="stat-label">Produits</span></div></div>
                    <div class="stat-card c-orange"><div class="stat-icon"><i class="fas fa-shopping-cart"></i></div><div><span class="stat-value">${data.totalOrders}</span><span class="stat-label">Commandes</span></div></div>
                `;
            })
            .catch(error => console.error('Erreur:', error));
    }
    
    function loadChartData() {
        fetch('../../Controller/AdminAjax.php?action=role_stats')
            .then(response => response.json())
            .then(data => {
                document.getElementById('chartContainer').style.display = 'block';
                const ctx = document.getElementById('roleChart').getContext('2d');
                if (roleChart) roleChart.destroy();
                roleChart = new Chart(ctx, {
                    type: 'pie',
                    data: { labels: ['Administrateurs', 'Utilisateurs'], datasets: [{ data: [data.adminCount, data.userCount], backgroundColor: ['rgba(124, 58, 237, 0.8)', 'rgba(0, 168, 107, 0.8)'], borderWidth: 2 }] },
                    options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { color: '#f0f4ff' } }, tooltip: { callbacks: { label: function(ctx) { const total = ctx.dataset.data.reduce((a,b)=>a+b,0); return `${ctx.label}: ${ctx.raw} (${total>0?((ctx.raw/total)*100).toFixed(1):0}%)`; } } } } }
                });
                document.getElementById('chartLegend').innerHTML = `Total: ${data.adminCount + data.userCount} | Admins: ${data.adminCount} | Utilisateurs: ${data.userCount}`;
            })
            .catch(error => console.error('Erreur:', error));
    }
    
    function loadUsersList() {
        fetch('../../Controller/AdminAjax.php?action=get_users')
            .then(response => response.text())
            .then(html => { document.getElementById('usersContent').innerHTML = html; loadChartData(); loadDashboardStats(); })
            .catch(error => console.error('Erreur:', error));
    }
    
    function submitAddUser() {
        const form = document.getElementById('addUserForm');
        if (!form) return;
        const formData = new FormData(form);
        fetch('../../Controller/UserController.php?action=add', { method: 'POST', body: formData })
            .then(response => {
                if (response.redirected && response.url.includes('success=add')) {
                    showToast('✅ Utilisateur ajouté avec succès !', 'success');
                    form.reset();
                    loadUsersList();
                    loadDashboardStats();
                    loadChartData();
                } else { showToast('❌ Erreur lors de l\'ajout !', 'error'); }
            })
            .catch(error => { console.error('Erreur:', error); showToast('❌ Erreur lors de l\'ajout !', 'error'); });
    }
    
    function updateUser(userId) {
        const nom = document.getElementById('nom_' + userId).value;
        const prenom = document.getElementById('prenom_' + userId).value;
        const email = document.getElementById('email_' + userId).value;
        const telephone = document.getElementById('telephone_' + userId).value;
        const role = document.getElementById('role_' + userId).value;
        
        const phoneNumbers = telephone.replace(/\D/g, '');
        if (phoneNumbers.length !== 8) {
            showToast('❌ Le téléphone doit contenir exactement 8 chiffres', 'error');
            return;
        }
        if (!email.includes('@') || !email.includes('.')) {
            showToast('❌ Email invalide', 'error');
            return;
        }
        if (!nom.trim() || !prenom.trim()) {
            showToast('❌ Nom et prénom sont requis', 'error');
            return;
        }
        
        const formData = new URLSearchParams();
        formData.append('id', userId); formData.append('nom', nom); formData.append('prenom', prenom);
        formData.append('email', email); formData.append('telephone', telephone); formData.append('role', role);
        
        if (!confirm('Modifier cet utilisateur ?')) return;
        
        fetch('../../Controller/UserController.php?action=update', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: formData.toString() })
            .then(response => {
                if (response.redirected && response.url.includes('success=update')) {
                    showToast('✅ Utilisateur modifié avec succès !', 'success');
                    loadUsersList(); loadDashboardStats(); loadChartData();
                } else { showToast('❌ Erreur lors de la modification !', 'error'); }
            })
            .catch(error => { console.error('Erreur:', error); showToast('❌ Erreur lors de la modification !', 'error'); });
    }
    
    function deleteUser(userId) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) return;
        fetch('../../Controller/UserController.php?action=delete&id=' + userId)
            .then(response => {
                if (response.redirected && response.url.includes('success=delete')) {
                    showToast('✅ Utilisateur supprimé avec succès !', 'success');
                    loadUsersList(); loadDashboardStats(); loadChartData();
                } else { showToast('❌ Erreur lors de la suppression !', 'error'); }
            })
            .catch(error => { console.error('Erreur:', error); showToast('❌ Erreur lors de la suppression !', 'error'); });
    }
    
    function showToast(message, type = 'info') {
        let container = document.querySelector('.toast-container');
        if (!container) { container = document.createElement('div'); container.className = 'toast-container'; document.body.appendChild(container); }
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i><span>${message}</span>`;
        container.appendChild(toast);
        setTimeout(() => { toast.classList.add('toast-hide'); setTimeout(() => toast.remove(), 300); }, 3000);
    }
    
    function loadProductsList() {
        document.getElementById('productsContent').innerHTML = '<div class="table-scroll"><p class="table-empty"><i class="fas fa-box"></i> Gestion des produits à implémenter</p></div>';
    }
    
    function loadOrdersList() {
        document.getElementById('ordersContent').innerHTML = '<div class="table-scroll"><p class="table-empty"><i class="fas fa-shopping-cart"></i> Gestion des commandes à implémenter</p></div>';
    }
    
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => { document.getElementById('mobileMenu')?.classList.remove('open'); });
    });
    
    <?php if (isset($_GET['admin_action'])): ?>
    setTimeout(() => {
        const panel = document.getElementById('adminPanel');
        if (panel) {
            panel.style.display = 'block';
            showAdminTab('users');
            
            <?php if (isset($_GET['success']) && $_GET['success'] == 'add'): ?>
                showToast('✅ Utilisateur ajouté avec succès !', 'success');
            <?php elseif (isset($_GET['error'])): ?>
                let errMsg = "<?= htmlspecialchars($_GET['error']) ?>";
                if (errMsg === 'email_exists') errMsg = "Cet email est déjà utilisé !";
                if (errMsg === 'password_court') errMsg = "Le mot de passe doit contenir au moins 8 caractères.";
                if (errMsg.includes('requis')) errMsg = "Veuillez remplir tous les champs.";
                showToast('❌ Erreur : ' + errMsg, 'error');
            <?php endif; ?>
        }
    }, 100);
    <?php endif; ?>
    </script>
</body>
</html>