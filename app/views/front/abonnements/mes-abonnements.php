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
                                    <span style="color: var(--green); font-weight: bold;">
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
                                    <a href="index.php?page=detail-abonnement&id=<?= $abo['id'] ?>" 
                                       class="btn-sm-green" title="Voir détail">
                                        <i class="fa-solid fa-eye"></i> Voir
                                    </a>
                                    <!-- ✏️ MODIFIER -->
                                    <a href="index.php?page=modifier-abonnement&id=<?= $abo['id'] ?>" 
                                       class="btn-sm-outline" title="Modifier">
                                        <i class="fa-solid fa-pen"></i> Modifier
                                    </a>
                                    <!-- 🗑️ SUPPRIMER (Résiliation) -->
                                    <a href="index.php?page=supprimer-abonnement&id=<?= $abo['id'] ?>" 
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
/* Style global pour la page mes abonnements */
.container {
    color: var(--txt);
}

.page-header h1 {
    color: white;
}

.page-header p {
    color: rgba(255, 255, 255, 0.8);
}

/* Tableau */
.table-container {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
}

.table-header h3 {
    color: white;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    background: var(--surface2);
    color: var(--txt);
    padding: 12px 15px;
    text-align: left;
    font-weight: 600;
}

.data-table td {
    color: var(--txt);
    padding: 12px 15px;
    border-bottom: 1px solid var(--border);
}

.data-table tr:hover td {
    background: rgba(255, 255, 255, 0.05);
}

/* Badge statut */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-actif {
    background: rgba(46, 204, 113, 0.2);
    color: #2ecc71;
    border: 1px solid rgba(46, 204, 113, 0.5);
}

.badge-actif i {
    color: #2ecc71;
}

/* Boutons d'action */
.table-actions {
    display: flex;
    gap: 8px;
}

.btn-sm-green {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(52, 152, 219, 0.2);
    color: white;
    padding: 5px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.75rem;
    transition: all 0.3s;
    border: 1px solid rgba(52, 152, 219, 0.5);
}

.btn-sm-green:hover {
    background: #3498db;
    color: white;
}

.btn-sm-outline {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(243, 156, 18, 0.2);
    color: white;
    padding: 5px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.75rem;
    transition: all 0.3s;
    border: 1px solid rgba(243, 156, 18, 0.5);
}

.btn-sm-outline:hover {
    background: #f39c12;
    color: white;
}

.btn-danger {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    background: rgba(231, 76, 60, 0.2);
    color: white;
    padding: 5px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.75rem;
    transition: all 0.3s;
    border: 1px solid rgba(231, 76, 60, 0.5);
}

.btn-danger:hover {
    background: #e74c3c;
    color: white;
}

/* Message vide */
.empty-state {
    text-align: center;
    padding: 40px;
    color: rgba(255, 255, 255, 0.7);
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
}

/* Alertes */
.alert-success {
    background: rgba(0, 168, 107, 0.2);
    border: 1px solid rgba(0, 168, 107, 0.5);
    color: #2ecc71;
    padding: 12px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Prix en vert */
.data-table td:nth-child(2) {
    color: #2ecc71;
    font-weight: bold;
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

.alert-success, .table-container {
    animation: fadeIn 0.3s ease;
}

/* Responsive */
@media (max-width: 768px) {
    .table-actions {
        flex-direction: column;
        gap: 5px;
    }
    .btn-sm-green, .btn-sm-outline, .btn-danger {
        justify-content: center;
    }
}
</style>

<?php include __DIR__ . '/../layout/footer.php'; ?>