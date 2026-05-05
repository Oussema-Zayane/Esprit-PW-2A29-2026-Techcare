<div class="products-section">
    <div class="dashboard-stats-row">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-star"></i></div>
            <div>
                <span class="stat-value" id="userPoints"><?= number_format($user['points']) ?></span>
                <span class="stat-label">Points disponibles</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-file-pdf"></i></div>
            <div>
                <span class="stat-value">
                    <a href="index.php?controller=gamification&action=exportPDF" class="btn-primary" style="padding: 0.5rem 1rem;">
                        <i class="fas fa-download"></i> Exporter PDF
                    </a>
                </span>
                <span class="stat-label">Télécharger mon historique</span>
            </div>
        </div>
    </div>
    
    <!-- Le reste de la page rewards (inchangé) -->
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-gift" style="color: var(--green);"></i> Boutique des récompenses</h3>
        </div>
        <div class="admin-content">
            <div class="stats-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">
                <?php foreach ($rewards as $reward): 
                    $canAfford = $user['points'] >= $reward['points_cost'];
                ?>
                <div class="stat-card <?= $canAfford ? 'c-green' : 'c-dark' ?>">
                    <div class="stat-icon"><i class="fas fa-tag"></i></div>
                    <div>
                        <span class="stat-value" style="font-size: 1rem;"><?= htmlspecialchars($reward['name']) ?></span>
                        <span class="stat-label"><?= htmlspecialchars($reward['description']) ?></span>
                        <span class="stat-label" style="color: var(--warning);">⭐ <?= number_format($reward['points_cost']) ?> points</span>
                        <?php if ($canAfford): ?>
                        <form method="POST" action="index.php?controller=gamification&action=redeemReward">
                            <input type="hidden" name="reward_id" value="<?= $reward['reward_id'] ?>">
                            <button type="submit" class="btn-primary" style="margin-top: 0.5rem; width: 100%;">🛒 Échanger</button>
                        </form>
                        <?php else: ?>
                        <button class="btn-primary" disabled style="margin-top: 0.5rem; width: 100%; opacity: 0.5;">🔒 Points insuffisants</button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>