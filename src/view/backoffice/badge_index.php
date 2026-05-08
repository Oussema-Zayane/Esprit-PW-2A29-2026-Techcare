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
                            <th>Image IA</th>
                            <th>Nom</th>
                            <th>Icône</th>
                            <th>Description</th>
                            <th>Seuil</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($badges as $b): ?>
                        <tr>
                            <td><?= $b['badge_id'] ?></td>
                            <td style="text-align: center;">
                                <?php if (!empty($b['image_ia']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/Esprit-PW-2A29-2526-ProjetWeb/' . $b['image_ia'])): ?>
                                    <img src="/Esprit-PW-2A29-2526-ProjetWeb/<?= $b['image_ia'] ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%; border: 2px solid #00A86B;">
                                <?php else: ?>
                                    <span style="font-size: 1.5rem;"><?= $b['icon'] ?? '🏅' ?></span>
                                <?php endif; ?>
                             </td>
                            <td><strong><?= htmlspecialchars($b['nom']) ?></strong></td>
                            <td style="font-size: 1.2rem;"><?= $b['icon'] ?? '🏅' ?></td>
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