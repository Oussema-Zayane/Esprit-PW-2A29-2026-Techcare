<div class="products-section">
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-chart-bar" style="color: var(--green);"></i> Statistiques globales</h3>
        </div>
        <div class="admin-content">
            
            <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); margin-bottom: 2rem;" id="globalStats"></div>
            
            <div class="admin-section" style="margin-top: 1rem;">
                <div class="admin-header">
                    <h3><i class="fas fa-chart-pie"></i> Répartition des badges</h3>
                </div>
                <div class="admin-content">
                    <canvas id="badgesChart" style="max-width: 400px; margin: 0 auto;"></canvas>
                </div>
            </div>
            
            <div class="admin-section" style="margin-top: 1rem;">
                <div class="admin-header">
                    <h3><i class="fas fa-chart-line"></i> Top 5 des utilisateurs</h3>
                </div>
                <div class="admin-content">
                    <canvas id="topUsersChart" style="max-width: 100%; height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function loadGlobalStats() {
    fetch('index.php?controller=gamification&action=getGlobalStats')
        .then(response => response.json())
        .then(data => {
            document.getElementById('globalStats').innerHTML = `
                <div class="stat-card c-green"><div class="stat-icon"><i class="fas fa-users"></i></div><div><span class="stat-value">${data.totalUsers}</span><span class="stat-label">Utilisateurs</span></div></div>
                <div class="stat-card c-purple"><div class="stat-icon"><i class="fas fa-star"></i></div><div><span class="stat-value">${data.totalPoints.toLocaleString()}</span><span class="stat-label">Points totaux</span></div></div>
                <div class="stat-card c-orange"><div class="stat-icon"><i class="fas fa-medal"></i></div><div><span class="stat-value">${data.totalBadgesUnlocked}</span><span class="stat-label">Badges débloqués</span></div></div>
                <div class="stat-card c-teal"><div class="stat-icon"><i class="fas fa-gift"></i></div><div><span class="stat-value">${data.totalRewardsExchanged}</span><span class="stat-label">Récompenses échangées</span></div></div>
                <div class="stat-card c-blue"><div class="stat-icon"><i class="fas fa-chart-line"></i></div><div><span class="stat-value">${data.avgPoints}</span><span class="stat-label">Moyenne points</span></div></div>
                <div class="stat-card c-dark"><div class="stat-icon"><i class="fas fa-trophy"></i></div><div><span class="stat-value">${data.topUser || '-'}</span><span class="stat-label">Meilleur utilisateur</span></div></div>
            `;
            
            // Graphique badges
            new Chart(document.getElementById('badgesChart'), {
                type: 'pie',
                data: {
                    labels: ['Débloqués', 'Verrouillés'],
                    datasets: [{ data: [data.unlockedBadges, data.lockedBadges], backgroundColor: ['#00A86B', '#334155'] }]
                },
                options: { responsive: true, plugins: { legend: { labels: { color: '#fff' } } } }
            });
            
            // Graphique top utilisateurs
            if (data.topUsers && data.topUsers.length > 0) {
                new Chart(document.getElementById('topUsersChart'), {
                    type: 'bar',
                    data: {
                        labels: data.topUsers.map(u => u.username),
                        datasets: [{ label: 'Points', data: data.topUsers.map(u => u.points), backgroundColor: '#00A86B' }]
                    },
                    options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { color: '#fff' } }, x: { ticks: { color: '#fff' } } }, plugins: { legend: { labels: { color: '#fff' } } } }
                });
            }
        });
}

loadGlobalStats();
</script>