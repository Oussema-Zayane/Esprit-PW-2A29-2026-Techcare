<div class="products-section">
    <div class="dashboard-stats-row">
        <div class="stat-card c-green">
            <div class="stat-icon"><i class="fas fa-star"></i></div>
            <div>
                <span class="stat-value"><?= number_format($user['points']) ?></span>
                <span class="stat-label">Points totaux</span>
            </div>
        </div>
        <div class="stat-card c-purple">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div>
                <span class="stat-value">Niveau <?= $user['level'] ?></span>
                <span class="stat-label">Niveau</span>
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
            <h3><i class="fas fa-history" style="color: var(--green);"></i> Historique complet</h3>
        </div>
        <div class="admin-content">
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr><th>Date</th><th>Action</th><th>Points</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $h): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?></td>
                            <td><?= htmlspecialchars($h['reason']) ?></td>
                            <td class="tbl-price">+<?= $h['points_change'] ?></td>
                            <td>
                                <form method="POST" action="index.php?controller=gamification&action=deleteHistory" onsubmit="return confirm('Supprimer cette entrée ?')">
                                    <input type="hidden" name="id" value="<?= $h['history_id'] ?>">
                                    <button type="submit" class="btn-del" style="background: none; border: none;"><i class="fas fa-trash"></i> Supprimer</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>