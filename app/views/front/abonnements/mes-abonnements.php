<?php include __DIR__ . '/../layout/header.php'; ?>

<section class="hero-section">
    <div class="container">
        <h1>Mes abonnements</h1>
        <p>Consultez et gérez vos abonnements TechCare</p>
    </div>
</section>

<section class="offres-section">
    <div class="container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-check-circle"></i>
                <div><?= $_SESSION['success'] ?></div>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div style="margin-bottom: 2rem; text-align: center;">
            <a href="index.php?page=offres" class="btn-primary">
                <i class="fa-solid fa-plus"></i> Souscrire à un abonnement
            </a>
        </div>

        <!-- Tableau des abonnements -->
        <div class="table-container">
            <div class="table-header">
                <h3><i class="fa-solid fa-list"></i> Vos abonnements</h3>
            </div>
            
            <?php if (empty($mesAbonnements)): ?>
                <div style="padding: 2rem; text-align: center; color: var(--txt-muted);">
                    <i class="fa-solid fa-inbox" style="font-size: 3rem; opacity: 0.3; display: block; margin-bottom: 1rem;"></i>
                    <p>Vous n'avez aucun abonnement actuellement</p>
                    <a href="index.php?page=offres" class="btn-primary" style="margin-top: 1rem;">
                        <i class="fa-solid fa-plus"></i> En souscrire un
                    </a>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Prix</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mesAbonnements as $abo): ?>
                            <tr>
                                <td>
                                    <strong><?= ucfirst($abo['type_abonnement']) ?></strong>
                                </td>
                                <td>
                                    <span style="color: #00A86B; font-weight: bold;">
                                        <?= number_format($abo['prix'], 2) ?> DT
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($abo['date_debut'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($abo['date_fin'])) ?></td>
                                <td>
                                    <span class="badge badge-<?= $abo['statut'] ?>">
                                        <i class="fa-solid fa-circle"></i> <?= ucfirst($abo['statut']) ?>
                                    </span>
                                </td>
                                <td class="table-actions">
                                    <!-- 👁️ VOIR -->
                                    <a href="index.php?page=detail-abo&id=<?= $abo['id'] ?>" 
                                       class="btn-sm-green" title="Voir détail">
                                        <i class="fa-solid fa-eye"></i> Voir
                                    </a>
                                    <!-- ✏️ MODIFIER -->
                                    <a href="index.php?page=modifier-abo&id=<?= $abo['id'] ?>" 
                                       class="btn-sm-outline" title="Modifier">
                                        <i class="fa-solid fa-pen"></i> Modifier
                                    </a>
                                    <!-- 🗑️ SUPPRIMER (Résiliation) -->
                                    <a href="index.php?page=supprimer-abo&id=<?= $abo['id'] ?>" 
                                       class="btn-danger"
                                       style="display: inline-flex; align-items: center; gap: 0.35rem; background: #e74c3c; color: white; padding: 0.4rem 0.9rem; border-radius: 8px; font-size: 0.82rem; text-decoration: none; cursor: pointer; border: none;"
                                       title="Résilier"
                                       onclick="return confirm('Voulez-vous vraiment résilier cet abonnement ?')">
                                        <i class="fa-solid fa-trash"></i> Résilier
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../layout/footer.php'; ?>

<style>
.abonnements-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    margin-top: 30px;
}
.abonnement-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    overflow: hidden;
    transition: transform 0.2s;
}
.abonnement-card:hover {
    transform: translateY(-5px);
}
.card-header {
    background: #0A1F44;
    color: white;
    padding: 15px;
    display: flex;
    justify-content: space-between;
}
.card-body {
    padding: 20px;
    text-align: center;
}
.prix {
    font-size: 28px;
    font-weight: bold;
    color: #00A86B;
}
.periode {
    margin-top: 10px;
    color: #666;
    font-size: 14px;
}
.card-actions {
    display: flex;
    gap: 10px;
    padding: 15px;
    border-top: 1px solid #eee;
}
.card-actions a {
    flex: 1;
    text-align: center;
    padding: 8px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
}
.btn-view { background: #3498db; color: white; }
.btn-edit { background: #f39c12; color: white; }
.btn-delete { background: #e74c3c; color: white; }
.statut-badge.actif { background: #2ecc71; color: white; padding: 3px 10px; border-radius: 20px; font-size: 12px; }
.statut-badge.expiré { background: #e74c3c; color: white; padding: 3px 10px; border-radius: 20px; font-size: 12px; }
.type-badge { background: #00A86B; padding: 3px 10px; border-radius: 20px; font-size: 12px; }
</style>

<?php include __DIR__ . '/../layout/footer.php'; ?>