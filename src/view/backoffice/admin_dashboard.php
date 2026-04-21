<div class="products-section">
    <div class="admin-section">
        <div class="admin-header"><h3><i class="fas fa-chart-line"></i> Panel d'administration</h3></div>
        <div class="admin-content">
            <div class="admin-tabs">
                <button class="admin-tab-btn active" onclick="showAdminTab('dashboard')">📊 Dashboard</button>
                <button class="admin-tab-btn" onclick="showAdminTab('users')">👥 Utilisateurs</button>
                <button class="admin-tab-btn" onclick="showAdminTab('fidelite')">💎 Fidélité</button>
            </div>
            <div id="adminTabDashboard" class="admin-tab-pane active"><div id="statsGrid" class="stats-grid"></div></div>
            <div id="adminTabUsers" class="admin-tab-pane"><div id="usersContent"></div></div>
            <div id="adminTabFidelite" class="admin-tab-pane"><div id="fideliteContent"></div></div>
        </div>
    </div>
</div>
<script>
function showAdminTab(tabName) {
    document.querySelectorAll('.admin-tab-pane').forEach(pane => pane.classList.remove('active'));
    document.getElementById('adminTab' + tabName.charAt(0).toUpperCase() + tabName.slice(1)).classList.add('active');
    document.querySelectorAll('.admin-tab-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    if (tabName === 'dashboard') loadStats();
    if (tabName === 'users') loadUsers();
    if (tabName === 'fidelite') loadFidelite();
}
function loadStats() {
    fetch('index.php?controller=user&action=getStats').then(r=>r.json()).then(data=>{
        document.getElementById('statsGrid').innerHTML = `
            <div class="stat-card"><div class="stat-icon"><i class="fas fa-users"></i></div><div><span class="stat-value">${data.totalUsers}</span><span class="stat-label">Utilisateurs</span></div></div>
            <div class="stat-card"><div class="stat-icon"><i class="fas fa-star"></i></div><div><span class="stat-value">${(data.totalPoints||0).toLocaleString()}</span><span class="stat-label">Points totaux</span></div></div>
            <div class="stat-card"><div class="stat-icon"><i class="fas fa-medal"></i></div><div><span class="stat-value">${data.totalBadges}</span><span class="stat-label">Badges</span></div></div>
            <div class="stat-card"><div class="stat-icon"><i class="fas fa-gem"></i></div><div><span class="stat-value">${data.totalActions}</span><span class="stat-label">Actions</span></div></div>`;
    });
}
function loadUsers() {
    fetch('index.php?controller=user&action=getUsers').then(r=>r.json()).then(data=>{
        let html='<table class="data-table"><thead><tr><th>ID</th><th>Nom</th><th>Points</th></tr></thead><tbody>';
        data.forEach(u=>{html+=`<tr><td>${u.id}</td><td>${u.nom}</td><td>⭐ ${u.nbPoints}</td></tr>`;});
        html+='</tbody></table><a href="index.php?controller=user&action=admin" class="btn-primary" style="margin-top:1rem;">Gérer</a>';
        document.getElementById('usersContent').innerHTML=html;
    });
}
function loadFidelite() {
    fetch('index.php?controller=fidelite&action=index').then(r=>r.text()).then(html=>{document.getElementById('fideliteContent').innerHTML=html;});
}
loadStats();
</script>