<div class="products-section">
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-trophy" style="color: var(--green);"></i> Classement des éco-guerriers</h3>
        </div>
        <div class="admin-content">
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Utilisateur</th>
                            <th>Avatar</th>
                            <th>Niveau</th>
                            <th>Points</th>
                            <th>Badges</th>
                        </tr>
                    </thead>
                    <tbody id="leaderboardBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function loadLeaderboard() {
    fetch('index.php?controller=gamification&action=getLeaderboard')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('leaderboardBody');
            tbody.innerHTML = '';
            
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;">Aucun utilisateur</td></tr>';
                return;
            }
            
            data.forEach((user, index) => {
                const rank = index + 1;
                let rankClass = '';
                let rankIcon = '';
                if (rank === 1) { rankClass = 'rank-1'; rankIcon = '🥇'; }
                else if (rank === 2) { rankClass = 'rank-2'; rankIcon = '🥈'; }
                else if (rank === 3) { rankClass = 'rank-3'; rankIcon = '🥉'; }
                
                let avatar = '🥚';
                if (user.points >= 5000) avatar = '🐉';
                else if (user.points >= 2500) avatar = '🦅';
                else if (user.points >= 1000) avatar = '🐺';
                else if (user.points >= 500) avatar = '🦊';
                else if (user.points >= 100) avatar = '🐣';
                
                const isCurrentUser = (user.user_id == 1);
                
                tbody.innerHTML += `
                    <tr class="${isCurrentUser ? 'current-user' : ''} ${rankClass}">
                        <td><strong>${rankIcon} #${rank}</strong></td>
                        <td>${user.username} ${isCurrentUser ? '<span style="color:#00A86B;">(Vous)</span>' : ''}</td>
                        <td style="font-size:1.5rem;">${avatar}</td>
                        <td>📊 Niveau ${user.level}</td>
                        <td class="tbl-price">⭐ ${user.points.toLocaleString()} pts</td>
                        <td>🏅 ${user.badge_count || 0}</td>
                    </tr>
                `;
            });
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.getElementById('leaderboardBody').innerHTML = '<tr><td colspan="6" style="text-align:center;">Erreur de chargement</td></tr>';
        });
}

loadLeaderboard();
setInterval(loadLeaderboard, 30000);
</script>

<style>
.rank-1 { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: #000; }
.rank-2 { background: linear-gradient(135deg, #94a3b8, #64748b); color: #000; }
.rank-3 { background: linear-gradient(135deg, #cd7f32, #b45309); color: #000; }
.current-user { background: rgba(0,168,107,0.15); border-left: 3px solid #00A86B; }
</style>