<div class="products-section">
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-gem" style="color: var(--green);"></i> Gestion de la Fidélité (BackOffice)</h3>
            <a href="index.php?controller=fidelite&action=create" class="btn-primary">➕ Ajouter une offre</a>
        </div>
        <div class="admin-content">
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Description</th>
                            <th>Points</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($fidelites)): ?>
                            <tr><td colspan="6" style="text-align:center;">Aucune offre de fidélité</td></tr>
                        <?php else: ?>
                            <?php foreach ($fidelites as $f): ?>
                            <tr>
                                <td><?= $f['idPoints'] ?></td>
                                <td><strong><?= htmlspecialchars($f['titre']) ?></strong></td>
                                <td><?= htmlspecialchars(substr($f['description'], 0, 60)) ?>...</td>
                                <td>⭐ <?= number_format($f['total']) ?></td>
                                <td><?= date('d/m/Y', strtotime($f['dateGain'])) ?></td>
                                <td>
                                    <a href="index.php?controller=fidelite&action=edit&id=<?= $f['idPoints'] ?>" class="btn-edit">✏️ Modifier</a>
                                    <a href="index.php?controller=fidelite&action=delete&id=<?= $f['idPoints'] ?>" class="btn-del" onclick="return confirm('Supprimer cette offre ?')">🗑️ Supprimer</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>