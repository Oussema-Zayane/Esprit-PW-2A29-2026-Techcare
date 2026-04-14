<div class="products-section">
    <div class="dashboard-stats-row">
        <div class="stat-card c-green">
            <div class="stat-icon"><i class="fas fa-star"></i></div>
            <div>
                <span class="stat-value"><?= number_format($user['points']) ?></span>
                <span class="stat-label">Points fidélité</span>
            </div>
        </div>
        <div class="stat-card c-purple">
            <div class="stat-icon"><i class="fas fa-gem"></i></div>
            <div>
                <span class="stat-value">Niveau <?= $user['level'] ?></span>
                <span class="stat-label">Votre niveau</span>
            </div>
        </div>
    </div>
    
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-hand-holding-heart" style="color: var(--green);"></i> Programme de fidélité</h3>
        </div>
        <div class="admin-content">
            <p>Plus vous agissez, plus vous économisez ! Échangez vos points contre des réductions exclusives.</p>
            <div class="stats-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); margin-top: 1.5rem;">
                <div class="stat-card c-teal">
                    <div class="stat-icon"><i class="fas fa-truck"></i></div>
                    <div>
                        <span class="stat-value" style="font-size: 1rem;">Livraison -5%</span>
                        <span class="stat-label">⭐ 200 points</span>
                        <button class="btn-primary" style="margin-top: 0.5rem; width: 100%;" onclick="showToast('Code promo: TECH-LIVRAISON-5-ABC123', 'success')">🛒 Échanger</button>
                    </div>
                </div>
                <div class="stat-card c-orange">
                    <div class="stat-icon"><i class="fas fa-tag"></i></div>
                    <div>
                        <span class="stat-value" style="font-size: 1rem;">Produit -10%</span>
                        <span class="stat-label">⭐ 500 points</span>
                        <button class="btn-primary" style="margin-top: 0.5rem; width: 100%;" onclick="showToast('Code promo: TECH-PRODUIT-10-XYZ789', 'success')">🛒 Échanger</button>
                    </div>
                </div>
                <div class="stat-card c-purple">
                    <div class="stat-icon"><i class="fas fa-gift"></i></div>
                    <div>
                        <span class="stat-value" style="font-size: 1rem;">Livraison offerte</span>
                        <span class="stat-label">⭐ 800 points</span>
                        <button class="btn-primary" style="margin-top: 0.5rem; width: 100%;" onclick="showToast('Code promo: TECH-LIVRAISON-100-DEF456', 'success')">🛒 Échanger</button>
                    </div>
                </div>
                <div class="stat-card c-green">
                    <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
                    <div>
                        <span class="stat-value" style="font-size: 1rem;">Global -15%</span>
                        <span class="stat-label">⭐ 1500 points</span>
                        <button class="btn-primary" <?= $user['points'] < 1500 ? 'disabled' : '' ?> style="margin-top: 0.5rem; width: 100%; opacity: <?= $user['points'] < 1500 ? '0.5' : '1' ?>;" onclick="showToast('Code promo: TECH-GLOBAL-15-GHI123', 'success')">🛒 Échanger</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>