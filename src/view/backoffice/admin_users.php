<div class="products-section">
    <div class="admin-section">
        <div class="admin-header"><h3><i class="fas fa-users"></i> Gestion des utilisateurs</h3><a href="index.php?controller=user&action=admin" class="btn-primary">📊 Dashboard</a></div>
        <div class="admin-content"><div id="usersList"></div></div>
    </div>
</div>
<script>
fetch('index.php?controller=user&action=getUsers').then(r=>r.json()).then(data=>{
    let html='<table class="data-table"><thead><tr><th>ID</th><th>Nom</th><th>Email</th><th>Points</th><th>Action</th></tr></thead><tbody>';
    data.forEach(u=>{html+=`<tr><td>${u.id}</td><td>${u.nom}</td><td>${u.email}</td><td>⭐ ${u.nbPoints}</td><td><button class="btn-del" onclick="deleteUser(${u.id})">Supprimer</button></td></tr>`;});
    html+='</tbody></table><div style="margin-top:1rem;"><h4>➕ Ajouter</h4><form method="POST" action="index.php?controller=user&action=addUser"><input type="text" name="username" placeholder="Nom" required><input type="number" name="points" placeholder="Points" value="0"><button type="submit" class="btn-primary">Ajouter</button></form></div>';
    document.getElementById('usersList').innerHTML=html;
});
function deleteUser(id){if(confirm('Supprimer ?'))fetch('index.php?controller=user&action=deleteUser&id='+id).then(()=>location.reload());}
</script>