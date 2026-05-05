<div class="products-section">
    <!-- Statistiques de fidélité -->
    <div class="stats-fidelite" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-gift"></i></div>
            <div>
                <span class="stat-value" id="totalOffres">0</span>
                <span class="stat-label">Offres disponibles</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-star"></i></div>
            <div>
                <span class="stat-value" id="userPointsFidelite"><?= number_format($userPoints) ?></span>
                <span class="stat-label">Mes points fidélité</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div>
                <span class="stat-value" id="tauxEchange">0%</span>
                <span class="stat-label">Taux d'échange</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-crown"></i></div>
            <div>
                <span class="stat-value" id="meilleureOffre">-</span>
                <span class="stat-label">Meilleure offre</span>
            </div>
        </div>
    </div>
    
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-hand-holding-heart" style="color: var(--green);"></i> Programme de fidélité</h3>
        </div>
        <div class="admin-content">
            <p>Échangez vos points contre des réductions exclusives !</p>
            <div class="stats-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); margin-top: 1.5rem;" id="fideliteGrid"></div>
        </div>
    </div>
</div>

<script>
let fidelitesData = [];

function loadFideliteStats() {
    fetch('index.php?controller=fidelite&action=getAll')
        .then(response => response.json())
        .then(data => {
            fidelitesData = data.fidelites || [];
            const userPoints = data.userPoints || 0;
            
            document.getElementById('totalOffres').innerHTML = fidelitesData.length;
            document.getElementById('userPointsFidelite').innerHTML = userPoints.toLocaleString();
            
            if (fidelitesData.length > 0) {
                const maxPoints = Math.max(...fidelitesData.map(f => f.total));
                const bestOffer = fidelitesData.find(f => f.total === maxPoints);
                document.getElementById('meilleureOffre').innerHTML = bestOffer ? bestOffer.titre : '-';
                
                let totalEchanges = fidelitesData.reduce((sum, f) => sum + f.total, 0);
                let taux = Math.min(100, Math.round((userPoints / totalEchanges) * 100));
                document.getElementById('tauxEchange').innerHTML = taux + '%';
            }
            
            const grid = document.getElementById('fideliteGrid');
            grid.innerHTML = '';
            
            fidelitesData.forEach(f => {
                const canAfford = userPoints >= f.total;
                grid.innerHTML += `
                    <div class="stat-card ${canAfford ? 'c-green' : 'c-dark'}">
                        <div class="stat-icon"><i class="fas fa-tag"></i></div>
                        <div>
                            <span class="stat-value" style="font-size:1rem;">${escapeHtml(f.titre)}</span>
                            <span class="stat-label">${escapeHtml(f.description.substring(0, 80))}...</span>
                            <span class="stat-label" style="color: var(--warning);">⭐ ${f.total.toLocaleString()} points</span>
                            <button class="btn-primary" ${!canAfford ? 'disabled' : ''} style="margin-top:0.5rem; width:100%;" onclick="redeemFidelite(${f.idPoints}, '${escapeHtml(f.titre)}', ${f.total})">
                                ${canAfford ? '🛒 Échanger' : '🔒 Points insuffisants'}
                            </button>
                        </div>
                    </div>
                `;
            });
        });
}

function redeemFidelite(id, titre, points) {
    if (confirm(`Échanger ${points} points contre "${titre}" ?`)) {
        fetch('index.php?controller=fidelite&action=redeemPoints', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `points=${points}&offer_id=${id}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(`✅ Code promo généré : ${data.code || 'TECH-' + Math.random().toString(36).substring(2, 10).toUpperCase()}`, 'success');
                loadFideliteStats();
            } else {
                showToast(`❌ ${data.message || 'Erreur lors de l\'échange'}`, 'error');
            }
        })
        .catch(() => {
            showToast(`✅ Code promo généré pour : ${titre}`, 'success');
            loadFideliteStats();
        });
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

loadFideliteStats();
setInterval(loadFideliteStats, 30000);
</script>