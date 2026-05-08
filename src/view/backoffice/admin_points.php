<div class="products-section">
    <div class="admin-section">
        <div class="admin-header"><h3><i class="fas fa-star"></i> Gestion des points</h3></div>
        <div class="admin-content"><div id="pointsContent"></div></div>
    </div>
</div>
<script>
fetch('index.php?controller=user&action=getUsers').then(r=>r.json()).then(data=>{
    let html='<table class="data-table"><thead><tr><th>ID</th><th>Nom</th><th>Points</th><th>Action</th></tr></thead><tbody>';
    data.forEach(u=>{html+=`<tr><td>${u.id}</td><td>${u.nom}</td><td>⭐ ${u.nbPoints}</td><td><button class="btn-primary" onclick="addPoints(${u.id},50)">+50</button> <button class="btn-primary" onclick="addPoints(${u.id},100)">+100</button></td></tr>`;});
    html+='</tbody></table>';
    document.getElementById('pointsContent').innerHTML=html;
});
function addPoints(id,points){fetch('index.php?controller=fidelite&action=addPoints',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},body:`points=${points}`}).then(()=>location.reload());}
</script>