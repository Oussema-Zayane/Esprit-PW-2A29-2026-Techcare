<div class="products-section">
    <div class="dashboard-stats-row"><div class="stat-card"><div class="stat-icon" id="avatarIcon" style="font-size:2.5rem">🐺</div><div><span class="stat-value" id="avatarName">Loup Écolo</span><span class="stat-label">Votre avatar</span></div></div>
    <div class="stat-card"><div class="stat-icon"><i class="fas fa-star"></i></div><div><span class="stat-value" id="badgesCount">3/5</span><span class="stat-label">Badges débloqués</span></div></div>
    <div class="stat-card"><div class="stat-icon"><i class="fas fa-chart-line"></i></div><div><span class="stat-value" id="userPoints">1250</span><span class="stat-label">Points</span></div></div></div>
    <div class="admin-section"><div class="admin-header"><h3>🏅 Collection de badges</h3></div><div class="admin-content"><div class="stats-grid" id="badgesGrid"></div></div></div>
</div>
<script>
const avatars=[{min:0,max:99,icon:'🥚',name:'Œuf'},{min:100,max:499,icon:'🐣',name:'Poussin'},{min:500,max:999,icon:'🦊',name:'Renard'},{min:1000,max:2499,icon:'🐺',name:'Loup'},{min:2500,max:4999,icon:'🦅',name:'Aigle'},{min:5000,max:999999,icon:'🐉',name:'Dragon'}];
const badges=[{id:1,name:'Éco-Poussin',icon:'🐣',points:100,desc:'Première action'},{id:2,name:'Éco-Gardien',icon:'🦊',points:500,desc:'5 actions'},{id:3,name:'Éco-Guerrier',icon:'🐺',points:1000,desc:'15 actions'},{id:4,name:'Éco-Maître',icon:'🦅',points:2500,desc:'30 actions'},{id:5,name:'Éco-Légende',icon:'🐉',points:5000,desc:'50 actions'}];
fetch('index.php?controller=gamification&action=getUserData').then(r=>r.json()).then(data=>{
    document.getElementById('userPoints').innerHTML=data.points;
    let avatar=avatars[0];
    for(let a of avatars){if(data.points>=a.min&&data.points<=a.max){avatar=a;break;}}
    document.getElementById('avatarIcon').innerHTML=avatar.icon;
    document.getElementById('avatarName').innerHTML=avatar.name+' Écolo';
    let earnedCount=0;
    let html='';
    for(let b of badges){let earned=data.badges.includes(b.id);if(earned)earnedCount++;html+=`<div class="stat-card ${earned?'c-green':'c-dark'}" style="opacity:${earned?'1':'0.6'}"><div class="stat-icon" style="font-size:2rem">${b.icon}</div><div><span class="stat-value" style="font-size:1rem">${b.name}</span><span class="stat-label">${b.desc}</span><span class="stat-label">⭐ ${b.points} pts</span><span class="stat-label">${earned?'✓ Débloqué':'🔒 Verrouillé'}</span></div></div>`;}
    document.getElementById('badgesCount').innerHTML=`${earnedCount}/5`;
    document.getElementById('badgesGrid').innerHTML=html;
});
</script>