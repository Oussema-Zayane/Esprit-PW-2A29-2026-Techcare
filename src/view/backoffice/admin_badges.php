<div class="products-section">
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-medal" style="color: var(--green);"></i> Gestion des badges</h3>
        </div>
        <div class="admin-content">
            <div class="table-scroll">
                <table class="data-table">
                    <thead><tr><th>ID</th><th>Badge</th><th>Icône</th><th>Points requis</th><th>Description</th></tr></thead>
                    <tbody id="badgesTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function loadBadges() {
    fetch('index.php?controller=gamification&action=getBadgesApi')
        .then(response => response.json())
        .then(data => {
            let html = '';
            data.forEach(b => {
                html += `<tr>
                    <td>${b.badge_id}</td>
                    <td><strong>${escapeHtml(b.name)}</strong></td>
                    <td style="font-size: 1.5rem;">${b.icon}</td>
                    <td>⭐ ${b.points_required.toLocaleString()}</td>
                    <td>${escapeHtml(b.description)}</td>
                </tr>`;
            });
            document.getElementById('badgesTableBody').innerHTML = html;
        });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

loadBadges();
</script>