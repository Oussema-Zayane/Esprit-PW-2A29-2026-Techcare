<?php include __DIR__ . '/../layout/header.php'; ?>
<?php include __DIR__ . '/../layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <div class="page-title">
            <h1><i class="fa-solid fa-eye"></i> Détail de l'option</h1>
            <p>Consulter les informations de l'option #<?= $offre['id'] ?></p>
        </div>
        <div class="user-info">
            <div class="user-avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <span>Admin</span>
        </div>
    </div>

    <div class="detail-card">
        <div class="detail-card-header">
            <div class="header-info">
                <i class="fa-solid fa-list-check"></i>
                <h3>Informations de l'option</h3>
            </div>
            <div class="header-actions">
                <a href="index.php?action=offres-modifier&id=<?= $offre['id'] ?>" class="btn-edit">
                    <i class="fa-solid fa-pen"></i> Modifier
                </a>
                <a href="index.php?action=offres" class="btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>

        <div class="detail-content">
            <div class="detail-row">
                <div class="detail-group">
                    <label><i class="fa-solid fa-hashtag"></i> ID Option</label>
                    <div class="detail-value">#<?= $offre['id'] ?></div>
                </div>
                <div class="detail-group">
                    <label><i class="fa-solid fa-tag"></i> Abonnement lié</label>
                    <div class="detail-value">
                        <?php if ($abonnement): ?>
                            #<?= $abonnement['id'] ?> - <?= htmlspecialchars($abonnement['nom_client']) ?> (<?= $abonnement['type_abonnement'] ?>)
                        <?php else: ?>
                            Non lié
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-group">
                    <label><i class="fa-solid fa-align-left"></i> Description</label>
                    <div class="detail-value"><?= htmlspecialchars($offre['description']) ?></div>
                </div>
                <div class="detail-group">
                    <label><i class="fa-solid fa-clock"></i> Date de création</label>
                    <div class="detail-value"><?= date('d/m/Y H:i', strtotime($offre['created_at'] ?? date('Y-m-d H:i:s'))) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.detail-card {
    background: var(--card);
    border-radius: 16px;
    border: 1px solid var(--border);
    overflow: hidden;
    margin-top: 20px;
}
.detail-card-header {
    background: linear-gradient(135deg, rgba(0,168,107,0.1), rgba(0,168,107,0.05));
    padding: 18px 25px;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
}
.header-info {
    display: flex;
    align-items: center;
    gap: 10px;
}
.header-info i {
    font-size: 1.3rem;
    color: var(--green);
}
.header-info h3 {
    font-size: 1rem;
    color: var(--txt);
    margin: 0;
}
.header-actions {
    display: flex;
    gap: 10px;
}
.detail-content {
    padding: 25px;
}
.detail-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--border);
}
.detail-row:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}
.detail-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.detail-group label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--txt-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.detail-group label i {
    color: var(--green);
    font-size: 0.7rem;
}
.detail-value {
    font-size: 1rem;
    font-weight: 500;
    color: var(--txt);
    word-break: break-word;
}
.btn-edit {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, var(--green), var(--green-dk));
    color: white;
    padding: 8px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.8rem;
    transition: all 0.3s;
}
.btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--surface2);
    color: var(--txt-muted);
    padding: 8px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.8rem;
    transition: all 0.3s;
    border: 1px solid var(--border);
}
@media (max-width: 768px) {
    .detail-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    .detail-card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .header-actions {
        width: 100%;
    }
    .btn-edit, .btn-secondary {
        flex: 1;
        justify-content: center;
    }
}
</style>

<?php include __DIR__ . '/../layout/footer.php'; ?>