<div class="products-section">
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-chart-line" style="color: var(--green);"></i> Panel d'administration</h3>
        </div>
        <div class="admin-content">
            <div class="admin-tabs">
                <button class="admin-tab-btn active" onclick="showAdminTab('dashboard')">📊 Dashboard</button>
                <button class="admin-tab-btn" onclick="showAdminTab('users')">👥 Utilisateurs</button>
                <button class="admin-tab-btn" onclick="showAdminTab('points')">⭐ Points</button>
                <button class="admin-tab-btn" onclick="showAdminTab('badges')">🏅 Badges</button>
                <button class="admin-tab-btn" onclick="showAdminTab('rewards')">🎁 Récompenses</button>
            </div>
            
            <div id="adminTabDashboard" class="admin-tab-pane active">
                <div id="statsGrid" class="stats-grid"></div>
            </div>
            
            <div id="adminTabUsers" class="admin-tab-pane">
                <div class="table-scroll">
                    <table class="data-table">
                        <thead><tr><th>ID</th><th>Nom</th><th>Points</th><th>Niveau</th><th>Actions</th><th>Actions</th></tr></thead>
                        <tbody id="usersTableBody"></tbody>
                    </table>
                </div>
                <div style="margin-top: 1.5rem;">
                    <h4>➕ Ajouter un utilisateur</h4>
                    <form method="POST" action="index.php?controller=user&action=addUser" style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 0.5rem;">
                        <input type="text" name="username" placeholder="Nom d'utilisateur" required style="flex: 1; padding: 0.5rem; background: var(--surface2); border: 1px solid var(--border); border-radius: var(--r-sm); color: var(--txt);">
                        <input type="number" name="points" placeholder="Points" value="0" style="width: 120px; padding: 0.5rem; background: var(--surface2); border: 1px solid var(--border); border-radius: var(--r-sm); color: var(--txt);">
                        <button type="submit" class="btn-primary">➕ Ajouter</button>
                    </form>
                </div>
            </div>
            
            <div id="adminTabPoints" class="admin-tab-pane">
                <div class="table-scroll">
                    <table class="data-table">
                        <thead><tr><th>ID</th><th>Nom</th><th>Points</th><th>Niveau</th><th>Actions</th></tr></thead>
                        <tbody id="pointsTableBody"></tbody>
                    </table>
                </div>
                <div style="margin-top: 1.5rem;">
                    <h4>⭐ Ajouter des points</h4>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 0.5rem;">
                        <select id="pointsUserId" style="flex: 1; padding: 0.5rem; background: var(--surface2); border: 1px solid var(--border); border-radius: var(--r-sm); color: var(--txt);"></select>
                        <input type="number" id="pointsToAdd" placeholder="Points à ajouter" style="width: 150px; padding: 0.5rem; background: var(--surface2); border: 1px solid var(--border); border-radius: var(--r-sm); color: var(--txt);">
                        <button class="btn-primary" onclick="addPointsToUser()">⭐ Ajouter</button>
                    </div>
                </div>
            </div>
            
            <div id="adminTabBadges" class="admin-tab-pane">
                <div class="table-scroll">
                    <table class="data-table">
                        <thead><tr><th>ID</th><th>Badge</th><th>Icône</th><th>Points requis</th><th>Description</th></tr></thead>
                        <tbody id="badgesTableBody"></tbody>
                    </table>
                </div>
            </div>
            
            <div id="adminTabRewards" class="admin-tab-pane">
                <div class="table-scroll">
                    <table class="data-table">
                        <thead><tr><th>ID</th><th>Nom</th><th>Points</th><th>Stock</th></tr></thead>
                        <tbody id="rewardsTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.admin-tabs { display: flex; gap: 0.5rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
.admin-tab-btn { background: none; border: none; padding: 0.6rem 1.2rem; color: var(--txt-muted); cursor: pointer; border-radius: var(--r-sm); transition: var(--t); font-weight: 500; }
.admin-tab-btn:hover { background: rgba(255,255,255,0.05); color: var(--txt); }
.admin-tab-btn.active { background: var(--green); color: white; }
.admin-tab-pane { display: none; }
.admin-tab-pane.active { display: block; animation: fadeIn 0.3s ease; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<script>
function showAdminTab(tabName) {
    document.querySelectorAll('.admin-tab-pane').forEach(pane => pane.classList.remove('active'));
    document.getElementById('adminTab' + tabName.charAt(0).toUpperCase() + tabName.slice(1)).classList.add('active');
    document.querySelectorAll('.admin-tab-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    if (tabName === 'dashboard') loadStats();
    if (tabName === 'users') loadUsers();
    if (tabName === 'points') loadPoints();
    if (tabName === 'badges') loadBadges();
    if (tabName === 'rewards') loadRewards();
}

function loadStats() {
    fetch('index.php?controller=user&action=getStats')
        .then(response => response.json())
        .then(data => {
            document.getElementById('statsGrid').innerHTML = `
                <div class="stat-card c-green"><div class="stat-icon"><i class="fas fa-users"></i></div><div><span class="stat-value">${data.totalUsers}</span><span class="stat-label">Utilisateurs</span></div></div>
                <div class="stat-card c-purple"><div class="stat-icon"><i class="fas fa-star"></i></div><div><span class="stat-value">${data.totalPoints.toLocaleString()}</span><span class="stat-label">Points totaux</span></div></div>
                <div class="stat-card c-orange"><div class="stat-icon"><i class="fas fa-medal"></i></div><div><span class="stat-value">${data.totalBadges}</span><span class="stat-label">Badges débloqués</span></div></div>
                <div class="stat-card c-teal"><div class="stat-icon"><i class="fas fa-gift"></i></div><div><span class="stat-value">${data.totalRewards}</span><span class="stat-label">Récompenses échangées</span></div></div>
            `;
        });
}

function loadUsers() {
    fetch('index.php?controller=user&action=getUsers')
        .then(response => response.json())
        .then(data => {
            let html = '';
            data.forEach(u => {
                html += `<tr>
                    <td>${u.user_id}</td>
                    <td><strong>${escapeHtml(u.username)}</strong></td>
                    <td>⭐ ${u.points.toLocaleString()}</td>
                    <td>📊 Niveau ${u.level}</td>
                    <td>🌍 ${u.total_actions}</td>
                    <td><button class="btn-del" onclick="deleteUser(${u.user_id})">🗑️ Supprimer</button></td>
                </tr>`;
            });
            document.getElementById('usersTableBody').innerHTML = html;
        });
}

function deleteUser(id) {
    if (confirm('Supprimer cet utilisateur ?')) {
        fetch('index.php?controller=user&action=deleteUser&id=' + id)
            .then(() => { loadUsers(); loadStats(); showToast('Utilisateur supprimé', 'success'); });
    }
}

function loadPoints() {
    fetch('index.php?controller=user&action=getUsers')
        .then(response => response.json())
        .then(data => {
            let html = '';
            let options = '';
            data.forEach(u => {
                html += `<tr>
                    <td>${u.user_id}</td>
                    <td><strong>${escapeHtml(u.username)}</strong></td>
                    <td>⭐ ${u.points.toLocaleString()}</td>
                    <td>📊 Niveau ${u.level}</td>
                    <td><button class="btn-del" onclick="addPointsToUser(${u.user_id}, 50)">⭐ +50</button>
                    <button class="btn-del" onclick="addPointsToUser(${u.user_id}, 100)">⭐ +100</button></td>
                </tr>`;
                options += `<option value="${u.user_id}">${escapeHtml(u.username)} (${u.points.toLocaleString()} pts)</option>`;
            });
            document.getElementById('pointsTableBody').innerHTML = html;
            document.getElementById('pointsUserId').innerHTML = options;
        });
}

function addPointsToUser(userId, points) {
    fetch('index.php?controller=gamification&action=addPointsApi', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `userId=${userId}&points=${points}&reason=Ajout admin`
    }).then(() => { loadPoints(); loadStats(); showToast(`+${points} points ajoutés`, 'success'); });
}

function loadBadges() {
    fetch('index.php?controller=gamification&action=getBadgesApi')
        .then(response => response.json())
        .then(data => {
            let html = '';
            data.forEach(b => {
                html += `<tr>
                    <td>${b.badge_id}</td>
                    <td><strong>${escapeHtml(b.name)}</strong></td>
                    <td>${b.icon}</td>
                    <td>⭐ ${b.points_required}</td>
                    <td>${escapeHtml(b.description)}</td>
                </tr>`;
            });
            document.getElementById('badgesTableBody').innerHTML = html;
        });
}

function loadRewards() {
    fetch('index.php?controller=gamification&action=getRewardsApi')
        .then(response => response.json())
        .then(data => {
            let html = '';
            data.forEach(r => {
                html += `<tr>
                    <td>${r.reward_id}</td>
                    <td><strong>${escapeHtml(r.name)}</strong></td>
                    <td>⭐ ${r.points_cost.toLocaleString()}</td>
                    <td>📦 ${r.stock}</td>
                </tr>`;
            });
            document.getElementById('rewardsTableBody').innerHTML = html;
        });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

loadStats();
</script>