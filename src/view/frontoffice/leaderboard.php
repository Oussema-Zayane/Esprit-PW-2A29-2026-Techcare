<div class="products-section">
    <div class="admin-section"><div class="admin-header"><h3>🏆 Classement des éco-guerriers</h3></div><div class="admin-content"><table class="data-table"><thead><tr><th>#</th><th>Utilisateur</th><th>Niveau</th><th>Points</th></tr></thead><tbody id="leaderboardBody"></tbody></table></div></div>
</div>
<script>
fetch('index.php?controller=gamification&action=getLeaderboard').then(r=>r.json()).then(data=>{
    let html='';let rank=1;
    for(let u of data){html+=`<tr ${u.id==1?'class="current-user"':''}><td><strong>#${rank++}</strong></td><td>${u.nom} ${u.id==1?'(Vous)':''}</td><td>📊 Niveau ${Math.floor(u.nbPoints/1000)+1}</td><td class="tbl-price">⭐ ${u.nbPoints.toLocaleString()}</td></tr>`;}
    document.getElementById('leaderboardBody').innerHTML=html;
});
</script>