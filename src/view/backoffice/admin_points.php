<div class="products-section">
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-star" style="color: var(--green);"></i> Gestion des points</h3>
        </div>
        <div class="admin-content">
            <div style="margin-bottom: 1.5rem; display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.8rem; margin-bottom: 0.25rem; color: var(--txt-muted);">Utilisateur</label>
                    <select id="pointsUserId" style="width: 100%; padding: 0.5rem; background: var(--surface2); border: 1px solid var(--border); border-radius: var(--r-sm); color: var(--txt);"></select>
                </div>
                <div style="width: 150px;">
                    <label style="display: block; font-size: 0.8rem; margin-bottom: 0.25rem; color: var(--txt-muted);">Points à ajouter</label>
                    <input type="number" id="pointsToAdd" placeholder="Ex: 50" style="width: 100%; padding: 0.5rem; background: var(--surface2); border: 1px solid var(--border); border-radius: var(--r-sm); color: var(--txt);">
                </div>
                <div>
                    <button class="btn-primary" onclick="addPoints()">⭐ Ajouter les points</button>
                </div>
            </div>
            <div class="table-scroll">
                <table class="data-table">
                    <thead><tr><th>ID</th><th>Nom</th><th>Points</th><th>Niveau</th><th>Actions</th></tr></thead>
                    <tbody id="pointsTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
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

function addPoints() {
    const userId = document.getElementById('pointsUserId').value;
    const points = document.getElementById('pointsToAdd').value;
    if (!points || points <= 0) { showToast('Entrez un nombre de points valide', 'error'); return; }
    addPointsToUser(userId, points);
}

function addPointsToUser(userId, points) {
    fetch('index.php?controller=gamification&action=addPointsApi', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `userId=${userId}&points=${points}&reason=Ajout admin`
    }).then(() => { loadPoints(); showToast(`+${points} points ajoutés`, 'success'); });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

loadPoints();
</script>