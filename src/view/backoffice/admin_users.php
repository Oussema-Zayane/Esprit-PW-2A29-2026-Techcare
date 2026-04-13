<div class="products-section">
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-users" style="color: var(--green);"></i> Gestion des utilisateurs</h3>
            <button class="btn-sm-green" onclick="document.getElementById('addUserForm').style.display='flex'">➕ Ajouter</button>
        </div>
        <div class="admin-content">
            <div id="addUserForm" style="display: none; margin-bottom: 1.5rem; padding: 1rem; background: var(--surface2); border-radius: var(--r-md);">
                <form method="POST" action="index.php?controller=user&action=addUser" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <input type="text" name="username" placeholder="Nom" required style="flex: 1; padding: 0.5rem; background: var(--bg); border: 1px solid var(--border); border-radius: var(--r-sm); color: var(--txt);">
                    <input type="number" name="points" placeholder="Points" value="0" style="width: 100px; padding: 0.5rem; background: var(--bg); border: 1px solid var(--border); border-radius: var(--r-sm); color: var(--txt);">
                    <button type="submit" class="btn-primary">Ajouter</button>
                    <button type="button" class="btn-outline" onclick="document.getElementById('addUserForm').style.display='none'">Annuler</button>
                </form>
            </div>
            <div class="table-scroll">
                <table class="data-table">
                    <thead><tr><th>ID</th><th>Nom</th><th>Points</th><th>Niveau</th><th>Actions</th><th>Actions</th></tr></thead>
                    <tbody id="usersTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
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
            .then(() => { loadUsers(); showToast('Utilisateur supprimé', 'success'); });
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

loadUsers();
</script>