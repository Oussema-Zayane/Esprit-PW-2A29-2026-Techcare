<div class="products-section">
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-medal" style="color: var(--green);"></i> Gestion des Badges</h3>
            <a href="index.php?controller=badge&action=create" class="btn-primary">➕ Ajouter un badge</a>
        </div>
        <div class="admin-content">
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Icône</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Seuil (points)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($badges as $b): ?>
                        <tr>
                            <td><?= $b['badge_id'] ?></td>
                            <td style="font-size: 1.5rem;"><?= $b['icon'] ?? '🏅' ?></td>
                            <td><strong><?= htmlspecialchars($b['nom']) ?></strong></td>
                            <td><?= htmlspecialchars(substr($b['description'] ?? '', 0, 50)) ?>...</td>
                            <td>⭐ <?= number_format($b['seuilPoints']) ?></td>
                            <td>
                                <a href="index.php?controller=badge&action=edit&id=<?= $b['badge_id'] ?>" class="btn-edit">✏️ Modifier</a>
                                <a href="index.php?controller=badge&action=delete&id=<?= $b['badge_id'] ?>" class="btn-del" onclick="return confirm('Supprimer ce badge ?')">🗑️ Supprimer</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>