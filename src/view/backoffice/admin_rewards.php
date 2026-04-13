<div class="products-section">
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-gift" style="color: var(--green);"></i> Gestion des récompenses</h3>
        </div>
        <div class="admin-content">
            <div class="table-scroll">
                <table class="data-table">
                    <thead><tr><th>ID</th><th>Nom</th><th>Points</th><th>Stock</th><th>Description</th></tr></thead>
                    <tbody id="rewardsTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
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
                    <td>${escapeHtml(r.description)}</td>
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

loadRewards();
</script>