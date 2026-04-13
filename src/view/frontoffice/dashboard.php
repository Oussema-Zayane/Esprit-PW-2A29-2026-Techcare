<div class="products-section">
    <div class="dashboard-stats-row">
        <div class="stat-card c-green">
            <div class="stat-icon"><i class="fas fa-star"></i></div>
            <div>
                <span class="stat-value"><?= number_format($user['points']) ?></span>
                <span class="stat-label">Points</span>
            </div>
        </div>
        <div class="stat-card c-purple">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div>
                <span class="stat-value">Niveau <?= $user['level'] ?></span>
                <span class="stat-label">Niveau</span>
            </div>
        </div>
        <div class="stat-card c-orange">
            <div class="stat-icon"><i class="fas fa-medal"></i></div>
            <div>
                <span class="stat-value"><?= count($userBadges) ?></span>
                <span class="stat-label">Badges</span>
            </div>
        </div>
        <div class="stat-card c-teal">
            <div class="stat-icon"><i class="fas fa-leaf"></i></div>
            <div>
                <span class="stat-value"><?= $user['total_actions'] ?></span>
                <span class="stat-label">Actions</span>
            </div>
        </div>
    </div>
    
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-bolt" style="color: var(--green);"></i> Gagnez des points</h3>
        </div>
        <div class="admin-content">
            <form method="POST" action="index.php?controller=gamification&action=addPoints" style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <input type="hidden" name="points" value="50">
                <input type="hidden" name="action" value="🔧 Réparer un appareil">
                <button type="submit" class="btn-primary"><i class="fas fa-tools"></i> Réparer (+50)</button>
            </form>
            <form method="POST" action="index.php?controller=gamification&action=addPoints" style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 0.5rem;">
                <input type="hidden" name="points" value="100">
                <input type="hidden" name="action" value="♻️ Recycler un produit">
                <button type="submit" class="btn-primary"><i class="fas fa-recycle"></i> Recycler (+100)</button>
            </form>
            <form method="POST" action="index.php?controller=gamification&action=addPoints" style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 0.5rem;">
                <input type="hidden" name="points" value="25">
                <input type="hidden" name="action" value="📝 Donner un avis">
                <button type="submit" class="btn-primary"><i class="fas fa-pen"></i> Avis (+25)</button>
            </form>
            <form method="POST" action="index.php?controller=gamification&action=addPoints" style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 0.5rem;">
                <input type="hidden" name="points" value="200">
                <input type="hidden" name="action" value="👥 Parrainer un ami">
                <button type="submit" class="btn-primary"><i class="fas fa-users"></i> Parrainer (+200)</button>
            </form>
        </div>
    </div>
    
    <?php if ($nextBadge): ?>
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-chart-line" style="color: var(--green);"></i> Prochain badge</h3>
        </div>
        <div class="admin-content">
            <p><strong><?= $nextBadge['icon'] ?> <?= $nextBadge['name'] ?></strong> - <?= $nextBadge['points_required'] ?> points requis</p>
            <div class="progress-bar"><div class="progress-fill" style="width: <?= $progress ?>%"></div></div>
            <p><?= number_format($progress, 1) ?>% complété (<?= $user['points'] ?>/<?= $nextBadge['points_required'] ?> points)</p>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-history" style="color: var(--green);"></i> Historique récent</h3>
            <a href="index.php?controller=gamification&action=points" class="btn-sm-outline">Voir tout <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="admin-content">
            <div class="table-scroll">
                <table class="data-table">
                    <thead><tr><th>Date</th><th>Action</th><th>Points</th></tr></thead>
                    <tbody>
                        <?php foreach ($history as $h): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></td>
                            <td><?= htmlspecialchars($h['reason']) ?></td>
                            <td class="tbl-price">+<?= $h['points_change'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>