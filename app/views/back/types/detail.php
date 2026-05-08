<?php include __DIR__ . '/../layout/header.php'; ?>
<?php include __DIR__ . '/../layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <div class="page-title">
            <h1><i class="fa-solid fa-eye"></i> Détail du Type d'Abonnement</h1>
            <p>Consulter les informations du type #<?= isset($type['id_type']) ? $type['id_type'] : (isset($type['id']) ? $type['id'] : '-') ?></p>
        </div>
        <div class="user-info">
            <div class="user-avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <span>Admin</span>
        </div>
    </div>

    <!-- Carte détail -->
    <div class="detail-card">
        <div class="detail-card-header">
            <div class="header-info">
                <i class="fa-solid fa-layer-group"></i>
                <h3>Informations du type</h3>
            </div>
            <div class="header-actions">
                <a href="index.php?action=types-modifier&id=<?= isset($type['id_type']) ? $type['id_type'] : (isset($type['id']) ? $type['id'] : 0) ?>" class="btn-edit">
                    <i class="fa-solid fa-pen"></i> Modifier
                </a>
                <a href="index.php?action=types" class="btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>

        <div class="detail-content">
            <!-- Ligne 1 : ID Type et Date de création -->
            <div class="detail-row">
                <div class="detail-group">
                    <label><i class="fa-solid fa-hashtag"></i> ID Type</label>
                    <div class="detail-value">#<?= isset($type['id_type']) ? $type['id_type'] : (isset($type['id']) ? $type['id'] : '-') ?></div>
                </div>
                <div class="detail-group">
                    <label><i class="fa-solid fa-calendar"></i> Date de création</label>
                    <div class="detail-value">
                        <?php 
                        $createdAt = isset($type['created_at']) ? $type['created_at'] : (isset($type['date_creation']) ? $type['date_creation'] : null);
                        if ($createdAt): ?>
                            <?= date('d/m/Y H:i', strtotime($createdAt)) ?>
                        <?php else: ?>
                            Non disponible
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Ligne 2 : Nom du type et Prix de base -->
            <div class="detail-row">
                <div class="detail-group">
                    <label><i class="fa-solid fa-tag"></i> Nom du type</label>
                    <div class="detail-value type-name">
                        <?php 
                        $nomType = isset($type['nom_type']) ? $type['nom_type'] : (isset($type['nom']) ? $type['nom'] : '-');
                        $nomTypeLower = strtolower($nomType);
                        ?>
                        <span class="type-badge type-<?= $nomTypeLower ?>">
                            <?= ucfirst($nomType) ?>
                        </span>
                    </div>
                </div>
                <div class="detail-group">
                    <label><i class="fa-solid fa-money-bill"></i> Prix de base</label>
                    <div class="detail-value price">
                        <?php 
                        $prixBase = isset($type['prix_base']) ? $type['prix_base'] : (isset($type['prix']) ? $type['prix'] : 0);
                        echo number_format($prixBase, 2) . ' DT';
                        ?>
                    </div>
                </div>
            </div>

            <!-- Ligne 3 : Durée et Dernière modification -->
            <div class="detail-row">
                <div class="detail-group">
                    <label><i class="fa-solid fa-clock"></i> Durée</label>
                    <div class="detail-value">
                        <?php 
                        $duree = isset($type['duree_jours']) ? $type['duree_jours'] : (isset($type['duree']) ? $type['duree'] : 0);
                        echo $duree . ' jours';
                        ?>
                    </div>
                </div>
                <div class="detail-group">
                    <label><i class="fa-solid fa-pen"></i> Dernière modification</label>
                    <div class="detail-value">
                        <?php 
                        $updatedAt = isset($type['updated_at']) ? $type['updated_at'] : (isset($type['date_modification']) ? $type['date_modification'] : null);
                        if ($updatedAt): ?>
                            <?= date('d/m/Y H:i', strtotime($updatedAt)) ?>
                        <?php else: ?>
                            Non disponible
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Ligne 4 : Icône -->
            <div class="detail-row">
                <div class="detail-group">
                    <label><i class="fa-solid fa-image"></i> Icône</label>
                    <div class="detail-value">
                        <i class="fa-solid fa-<?= isset($type['icone']) ? htmlspecialchars($type['icone']) : 'tag' ?>"></i>
                        <?= isset($type['icone']) ? htmlspecialchars($type['icone']) : 'tag' ?>
                    </div>
                </div>
                <div class="detail-group">
                    <label><i class="fa-solid fa-chart-line"></i> Utilisation</label>
                    <div class="detail-value">
                        <?= isset($type['total_abonnements']) ? $type['total_abonnements'] : '0' ?> abonnement(s)
                    </div>
                </div>
            </div>

            <!-- Ligne 5 : Description (pleine largeur) -->
            <div class="detail-row full-width">
                <div class="detail-group">
                    <label><i class="fa-solid fa-align-left"></i> Description</label>
                    <div class="detail-value description">
                        <?php 
                        $description = isset($type['description']) ? $type['description'] : '';
                        if (!empty($description)): ?>
                            <?= nl2br(htmlspecialchars($description)) ?>
                        <?php else: ?>
                            <em>Aucune description disponible</em>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ═══ SECTION OFFRES ASSOCIÉES ═══ -->
            <div class="offres-section">
                <div class="offres-header">
                    <h3><i class="fa-solid fa-gift"></i> Offres associées à ce type</h3>
                </div>
                
                <?php if (isset($offres) && !empty($offres)): ?>
                    <div class="offres-grid">
                        <?php foreach ($offres as $offre): ?>
                            <div class="offre-card-item">
                                <div class="offre-icon">
                                    <i class="fa-solid fa-plus-circle"></i>
                                </div>
                                <div class="offre-content">
                                    <div class="offre-description"><?= htmlspecialchars($offre['description']) ?></div>
                                    <div class="offre-meta">
                                        <span class="offre-id">ID: #<?= $offre['id'] ?></span>
                                        <span class="offre-badge linked">
                                            <i class="fa-solid fa-link"></i> Associée à ce type
                                        </span>
                                    </div>
                                </div>
                                <div class="offre-actions">
                                    <a href="index.php?action=offres-detail&id=<?= $offre['id'] ?>" class="btn-view-sm" title="Voir l'offre">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="offres-empty">
                        <i class="fa-solid fa-inbox"></i>
                        <p>Aucune offre associée à ce type d'abonnement</p>
                        <a href="index.php?action=types-modifier&id=<?= isset($type['id_type']) ? $type['id_type'] : 0 ?>" class="btn-sm-green">
                            <i class="fa-solid fa-plus"></i> Associer des offres
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
/* Styles pour la carte de détail */
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
    font-size: 1.2rem;
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

.btn-edit {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s;
}

.btn-edit:hover {
    transform: translateY(-2px);
    filter: brightness(1.05);
}

.btn-secondary {
    background: var(--surface2);
    color: var(--txt-muted);
    padding: 8px 16px;
    border-radius: 8px;
    border: 1px solid var(--border);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s;
}

.btn-secondary:hover {
    background: var(--surface3);
    color: var(--txt);
}

.detail-content {
    padding: 25px;
}

.detail-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--border);
}

.detail-row:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.detail-row.full-width {
    grid-template-columns: 1fr;
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
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 12px 16px;
    color: var(--txt);
    font-size: 0.95rem;
    word-break: break-word;
}

.detail-value.description {
    min-height: 80px;
    line-height: 1.6;
}

.detail-value.price {
    font-weight: 700;
    color: var(--green);
    font-size: 1.1rem;
}

.detail-value.type-name {
    background: transparent;
    border: none;
    padding: 0;
}

/* Badges */
.type-badge {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 30px;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: capitalize;
}

.type-mensuel {
    background: rgba(52,152,219,0.15);
    color: #3498db;
    border: 1px solid rgba(52,152,219,0.3);
}

.type-annuel {
    background: rgba(155,89,182,0.15);
    color: #9b59b6;
    border: 1px solid rgba(155,89,182,0.3);
}

.type-ponctuel {
    background: rgba(230,126,34,0.15);
    color: #e67e22;
    border: 1px solid rgba(230,126,34,0.3);
}

/* SECTION OFFRES */
.offres-section {
    margin-top: 25px;
    padding-top: 15px;
    border-top: 2px solid var(--border);
}

.offres-header h3 {
    font-size: 1rem;
    color: var(--txt);
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.offres-header h3 i {
    color: var(--green);
}

.offres-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 12px;
}

.offre-card-item {
    background: var(--surface2);
    border-radius: 10px;
    padding: 12px 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.2s;
    border: 1px solid var(--border);
}

.offre-card-item:hover {
    border-color: rgba(0,168,107,0.3);
    background: rgba(0,168,107,0.05);
}

.offre-icon {
    width: 32px;
    height: 32px;
    background: rgba(0,168,107,0.1);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.offre-icon i {
    color: var(--green);
    font-size: 0.9rem;
}

.offre-content {
    flex: 1;
}

.offre-description {
    font-size: 0.85rem;
    color: var(--txt);
    font-weight: 500;
}

.offre-meta {
    font-size: 0.7rem;
    margin-top: 4px;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    align-items: center;
}

.offre-id {
    color: var(--txt-dim);
}

.offre-badge {
    font-size: 0.65rem;
    padding: 2px 8px;
    border-radius: 20px;
}

.offre-badge.linked {
    background: rgba(0,168,107,0.15);
    color: var(--green-lt);
}

.offre-badge.unlinked {
    background: rgba(108,117,125,0.15);
    color: var(--txt-muted);
}

.offre-actions {
    flex-shrink: 0;
}

.btn-view-sm {
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(52,152,219,0.15);
    color: #3498db;
    border-radius: 6px;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-view-sm:hover {
    background: #3498db;
    color: white;
}

.offres-empty {
    text-align: center;
    padding: 30px;
    color: var(--txt-muted);
    background: var(--surface2);
    border-radius: 12px;
}

.offres-empty i {
    font-size: 2rem;
    margin-bottom: 10px;
    opacity: 0.5;
}

.btn-sm-green {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(0,168,107,0.15);
    color: var(--green);
    border: 1px solid rgba(0,168,107,0.3);
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 0.75rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
}

.btn-sm-green:hover {
    background: var(--green);
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .detail-card-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .header-actions {
        width: 100%;
        justify-content: flex-start;
    }

    .detail-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .detail-row.full-width {
        grid-template-columns: 1fr;
    }
    
    .offres-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include __DIR__ . '/../layout/footer.php'; ?>