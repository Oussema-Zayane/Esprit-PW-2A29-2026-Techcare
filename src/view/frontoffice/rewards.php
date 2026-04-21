<div class="products-section">
    <div class="dashboard-stats-row"><div class="stat-card"><div class="stat-icon"><i class="fas fa-star"></i></div><div><span class="stat-value" id="userPoints">1250</span><span class="stat-label">Points disponibles</span></div></div></div>
    <div class="admin-section"><div class="admin-header"><h3>🎁 Boutique des récompenses</h3></div><div class="admin-content"><div class="stats-grid" id="rewardsGrid"></div></div></div>
    <div class="admin-section"><div class="admin-header"><h3>📦 Mes récompenses obtenues</h3></div><div class="admin-content"><div id="rewardsList"></div></div></div>
</div>
<script>
const rewardsList=[{id:1,name:'Réduction 10%',desc:'10% sur réparation',points:500},{id:2,name:'Support Prioritaire',desc:'Accès prioritaire',points:1000},{id:3,name:'Goodies TechCare',desc:'Kit exclusif',points:2000},{id:4,name:'Formation Gratuite',desc:'Formation en ligne',points:3000},{id:5,name:'Réparation Gratuite',desc:'Une réparation offerte',points:5000}];
fetch('index.php?controller=gamification&action=getUserData').then(r=>r.json()).then(data=>{
    document.getElementById('userPoints').innerHTML=data.points;
    let html='';
    for(let r of rewardsList){let can=data.points>=r.points;html+=`<div class="stat-card ${can?'c-green':'c-dark'}"><div class="stat-icon"><i class="fas fa-tag"></i></div><div><span class="stat-value" style="font-size:1rem">${r.name}</span><span class="stat-label">${r.desc}</span><span class="stat-label">⭐ ${r.points} points</span>${can?`<form method="POST" action="index.php?controller=gamification&action=redeemReward"><input type="hidden" name="reward_id" value="${r.id}"><button type="submit" class="btn-primary" style="margin-top:0.5rem">🛒 Échanger</button></form>`:'<button class="btn-primary" disabled>🔒 Points insuffisants</button>'}</div></div>`;}
    document.getElementById('rewardsGrid').innerHTML=html;
    let rewardsHtml='<div style="display:flex;flex-wrap:wrap;gap:0.5rem">';
    for(let r of data.userRewards){rewardsHtml+=`<span class="cat-badge">🎁 ${r.name} - ${r.date}</span>`;}
    rewardsHtml+='</div>';
    document.getElementById('rewardsList').innerHTML=rewardsHtml;
});
</script>