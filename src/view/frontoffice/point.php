<div class="products-section">
    <div class="dashboard-stats-row">
        <div class="stat-card"><div class="stat-icon"><i class="fas fa-star"></i></div><div><span class="stat-value" id="totalPoints">1250</span><span class="stat-label">Points totaux</span></div></div>
        <div class="stat-card"><div class="stat-icon"><i class="fas fa-chart-line"></i></div><div><span class="stat-value" id="userLevel">Niveau 2</span><span class="stat-label">Niveau</span></div></div>
        <div class="stat-card"><div class="stat-icon"><i class="fas fa-leaf"></i></div><div><span class="stat-value" id="actionsCount">15</span><span class="stat-label">Actions</span></div></div>
    </div>
    <div class="admin-section"><div class="admin-header"><h3>📜 Historique complet</h3></div><div class="admin-content"><table class="data-table"><thead><tr><th>Date</th><th>Action</th><th>Points</th><th>Action</th></tr></thead><tbody id="historyBody"></tbody></table></div></div>
</div>
<script>
fetch('index.php?controller=gamification&action=getUserData').then(r=>r.json()).then(data=>{
    document.getElementById('totalPoints').innerHTML=data.points;
    document.getElementById('userLevel').innerHTML=`Niveau ${data.level}`;
    document.getElementById('actionsCount').innerHTML=data.actions;
    let html='';
    for(let h of data.history){html+=`<tr><td>${h.date}</td><td>${h.action}</td><td class="tbl-price">+${h.points}</td><td><form method="POST" action="index.php?controller=gamification&action=deleteHistory" onsubmit="return confirm('Supprimer ?')"><input type="hidden" name="id" value="${h.id}"><button type="submit" class="btn-del">🗑️</button></form></td></tr>`;}
    document.getElementById('historyBody').innerHTML=html;
});
</script>