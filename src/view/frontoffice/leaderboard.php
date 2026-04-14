<div class="products-section">
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-trophy" style="color: var(--green);"></i> Classement des éco-guerriers</h3>
        </div>
        <div class="admin-content">
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr><th>#</th><th>Utilisateur</th><th>Niveau</th><th>Points</th></tr>
                    </thead>
                    <tbody>
                        <?php $rank = 1; ?>
                        <?php foreach ($users as $u): ?>
                        <tr class="<?= $u['user_id'] == 1 ? 'current-user' : '' ?>" style="<?= $u['user_id'] == 1 ? 'background: rgba(0,168,107,0.1); border-left: 3px solid var(--green);' : '' ?>">
                            <td><strong>#<?= $rank++ ?></strong></td>
                            <td><?= htmlspecialchars($u['username']) ?> <?= $u['user_id'] == 1 ? '(Vous)' : '' ?></td>
                            <td>📊 Niveau <?= $u['level'] ?></td>
                            <td class="tbl-price">⭐ <?= number_format($u['points']) ?> pts</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>