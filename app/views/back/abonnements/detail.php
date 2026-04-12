<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <h1><i class="fa-solid fa-eye"></i> Détail abonnement #<?= $abonnement['id'] ?></h1>
    <div class="page-header-actions">
        <a href="index.php?admin&action=modifier&id=<?= $abonnement['id'] ?>" class="btn-primary">
            <i class="fa-solid fa-pen"></i> Modifier
        </a>
        <button onclick="if(confirm('Supprimer cet abonnement ?')) location.href='index.php?admin&action=supprimer&id=<?= $abonnement['id'] ?>'" class="btn-danger">
            <i class="fa-solid fa-trash"></i> Supprimer
        </button>
        <a href="index.php?admin&action=liste" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Retour
        </a>
    </div>
</div>

<div class="form-card">
    <div style="text-align: right; margin-bottom: 1.5rem;">
        <span class="badge badge-<?= $abonnement['statut'] ?>">
            <i class="fa-solid fa-circle"></i> <?= ucfirst($abonnement['statut']) ?>
        </span>
    </div>
    
    <div class="form-row">
        <div class="form-group full">
            <label><strong>📋 ID Abonnement</strong></label>
            <div style="padding: 0.75rem; background: rgba(0,168,107,0.05); border-radius: 8px;">#<?= $abonnement['id'] ?></div>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label><strong>👤 Nom du client</strong></label>
            <div style="padding: 0.75rem; background: rgba(0,168,107,0.05); border-radius: 8px;"><?= htmlspecialchars($abonnement['nom_client']) ?></div>
        </div>
        <div class="form-group">
            <label><strong>💼 Type d'abonnement</strong></label>
            <div style="padding: 0.75rem; background: rgba(0,168,107,0.05); border-radius: 8px;"><?= ucfirst($abonnement['type_abonnement']) ?></div>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label><strong>💰 Prix</strong></label>
            <div style="padding: 0.75rem; background: rgba(0,168,107,0.05); border-radius: 8px; color: #00A86B; font-weight: bold; font-size: 1.2rem;">
                <?= number_format($abonnement['prix'], 2) ?> DT
            </div>
        </div>
        <div class="form-group">
            <label><strong>📅 Durée</strong></label>
            <div style="padding: 0.75rem; background: rgba(0,168,107,0.05); border-radius: 8px;">
                Du <?= date('d/m/Y', strtotime($abonnement['date_debut'])) ?> au <?= date('d/m/Y', strtotime($abonnement['date_fin'])) ?>
            </div>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label><strong>📝 Créé le</strong></label>
            <div style="padding: 0.75rem; background: rgba(0,168,107,0.05); border-radius: 8px;"><?= date('d/m/Y H:i', strtotime($abonnement['created_at'] ?? 'now')) ?></div>
        </div>
        <div class="form-group">
            <label><strong>🔄 Dernière modification</strong></label>
            <div style="padding: 0.75rem; background: rgba(0,168,107,0.05); border-radius: 8px;"><?= date('d/m/Y H:i', strtotime($abonnement['updated_at'] ?? 'now')) ?></div>
        </div>
    </div>

    <div class="form-actions" style="margin-top: 2rem; border-top: 1px solid var(--border); padding-top: 2rem;">
        <a href="index.php?admin&action=liste" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Retour à la liste
        </a>
        <a href="index.php?admin&action=modifier&id=<?= $abonnement['id'] ?>" class="btn-primary">
            <i class="fa-solid fa-pen"></i> Modifier cet abonnement
        </a>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>