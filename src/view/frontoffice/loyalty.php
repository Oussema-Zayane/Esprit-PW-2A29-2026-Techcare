<div class="products-section">
    <div class="dashboard-stats-row">
        <div class="stat-card"><div class="stat-icon"><i class="fas fa-star"></i></div><div><span class="stat-value" id="userPoints">1250</span><span class="stat-label">Points fidélité</span></div></div>
        <div class="stat-card"><div class="stat-icon"><i class="fas fa-gem"></i></div><div><span class="stat-value" id="userLevel">Niveau 2</span><span class="stat-label">Votre niveau</span></div></div>
    </div>
    <div class="admin-section"><div class="admin-header"><h3>💎 Programme de fidélité</h3></div><div class="admin-content"><p>Plus vous agissez, plus vous économisez !</p><div class="stats-grid" id="fideliteGrid"></div></div></div>
    <div class="admin-section"><div class="admin-header"><h3>🎫 Mes codes promo</h3></div><div class="admin-content"><div id="promoCodes"></div></div></div>
</div>
<script>
fetch('index.php?controller=fidelite&action=getAll').then(r=>r.json()).then(data=>{
    document.getElementById('userPoints').innerHTML=data.userPoints;
    document.getElementById('userLevel').innerHTML=`Niveau ${Math.floor(data.userPoints/1000)+1}`;
    let html='';
    for(let f of data.fidelites){html+=`<div class="stat-card"><div class="stat-icon"><i class="fas fa-gift"></i></div><div><span class="stat-value" style="font-size:1rem">${f.titre}</span><span class="stat-label">${f.description}</span><span class="stat-label">⭐ ${f.total} points</span><button class="btn-primary" onclick="showToast('Code promo: TECH-${f.titre.toUpperCase().replace(/ /g,'-')}-${Math.random().toString(36).substring(2,8).toUpperCase()}', 'success')">🛒 Échanger</button></div></div>`;}
    document.getElementById('fideliteGrid').innerHTML=html;
});
function showToast(msg){let t=document.createElement('div');t.className='toast toast-success';t.innerHTML=msg;document.querySelector('.toast-container').appendChild(t);setTimeout(()=>t.remove(),3000);}
</script>