<?php include __DIR__ . '/../layout/header.php'; ?>
<?php include __DIR__ . '/../layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <div class="page-title">
            <h1><i class="fa-solid fa-eye"></i> Détail de l'abonnement</h1>
            <p>Consulter les informations de l'abonnement #<?= $abonnement['id'] ?></p>
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
                <i class="fa-solid fa-receipt"></i>
                <h3>Informations abonnement</h3>
            </div>
            <div class="header-actions">
                <a href="index.php?action=modifier&id=<?= $abonnement['id'] ?>" class="btn-edit">
                    <i class="fa-solid fa-pen"></i> Modifier
                </a>
                <a href="index.php?action=liste" class="btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>

        <div class="detail-content">
            <div class="detail-row">
                <div class="detail-group">
                    <label><i class="fa-solid fa-hashtag"></i> ID Abonnement</label>
                    <div class="detail-value">#<?= $abonnement['id'] ?></div>
                </div>
                <div class="detail-group">
                    <label><i class="fa-solid fa-circle"></i> Statut</label>
                    <div class="detail-value">
                        <span class="badge badge-<?= $abonnement['statut'] ?>">
                            <?= ucfirst($abonnement['statut']) ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-group">
                    <label><i class="fa-solid fa-user"></i> Nom du client</label>
                    <div class="detail-value"><?= htmlspecialchars($abonnement['nom_client']) ?></div>
                </div>
                <div class="detail-group">
                    <label><i class="fa-solid fa-envelope"></i> Email</label>
                    <div class="detail-value"><?= htmlspecialchars($abonnement['email'] ?? 'Non renseigné') ?></div>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-group">
                    <label><i class="fa-solid fa-tag"></i> Type d'abonnement</label>
                    <div class="detail-value">
                        <span class="type-badge type-<?= $abonnement['type_abonnement'] ?>">
                            <?= ucfirst($abonnement['type_abonnement']) ?>
                        </span>
                    </div>
                </div>
                <div class="detail-group">
                    <label><i class="fa-solid fa-money-bill"></i> Prix</label>
                    <div class="detail-value price"><?= number_format($abonnement['prix'], 2) ?> DT</div>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-group">
                    <label><i class="fa-solid fa-calendar-day"></i> Date de début</label>
                    <div class="detail-value"><?= date('d/m/Y', strtotime($abonnement['date_debut'])) ?></div>
                </div>
                <div class="detail-group">
                    <label><i class="fa-solid fa-calendar-day"></i> Date de fin</label>
                    <div class="detail-value"><?= date('d/m/Y', strtotime($abonnement['date_fin'])) ?></div>
                </div>
            </div>

            <!-- Barre de progression -->
            <?php
            $date_debut = new DateTime($abonnement['date_debut']);
            $date_fin = new DateTime($abonnement['date_fin']);
            $aujourdhui = new DateTime();
            $total_jours = $date_debut->diff($date_fin)->days;
            $jours_ecoules = $date_debut->diff($aujourdhui)->days;
            $pourcentage = $total_jours > 0 ? min(100, round(($jours_ecoules / $total_jours) * 100)) : 0;
            $couleur_progress = $pourcentage >= 90 ? '#e74c3c' : ($pourcentage >= 60 ? '#f39c12' : '#2ecc71');
            ?>
            <div class="detail-row">
                <div class="detail-group span-2">
                    <label><i class="fa-solid fa-chart-line"></i> Progression de l'abonnement</label>
                    <div class="progress-container">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= $pourcentage ?>%; background: <?= $couleur_progress ?>;">
                                <span class="progress-text"><?= $pourcentage ?>%</span>
                            </div>
                        </div>
                        <div class="progress-dates">
                            <span><i class="fa-solid fa-calendar"></i> Début: <?= date('d/m/Y', strtotime($abonnement['date_debut'])) ?></span>
                            <span><i class="fa-solid fa-flag-checkered"></i> Fin: <?= date('d/m/Y', strtotime($abonnement['date_fin'])) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Options supplémentaires -->
            <?php if (isset($options) && !empty($options)): ?>
            <div class="detail-row">
                <div class="detail-group span-2">
                    <label><i class="fa-solid fa-list-check"></i> Options supplémentaires</label>
                    <div class="options-list">
                        <?php foreach ($options as $opt): ?>
                            <div class="option-item">
                                <i class="fa-solid fa-plus-circle"></i>
                                <span><?= htmlspecialchars($opt['description']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Historique (si disponible) -->
            <div class="detail-row">
                <div class="detail-group">
                    <label><i class="fa-solid fa-clock"></i> Créé le</label>
                    <div class="detail-value"><?= date('d/m/Y H:i', strtotime($abonnement['created_at'])) ?></div>
                </div>
                <div class="detail-group">
                    <label><i class="fa-solid fa-pen"></i> Dernière modification</label>
                    <div class="detail-value"><?= date('d/m/Y H:i', strtotime($abonnement['updated_at'])) ?></div>
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

.detail-group.span-2 {
    grid-column: span 2;
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

.detail-value.price {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--green);
}

/* Badges */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}
.badge-actif {
    background: rgba(46,204,113,0.15);
    color: #2ecc71;
    border: 1px solid rgba(46,204,113,0.3);
}
.badge-suspendu {
    background: rgba(243,156,18,0.15);
    color: #f39c12;
    border: 1px solid rgba(243,156,18,0.3);
}
.badge-expiré, .badge-annulé {
    background: rgba(231,76,60,0.15);
    color: #e74c3c;
    border: 1px solid rgba(231,76,60,0.3);
}

/* Type badges */
.type-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}
.type-mensuel {
    background: rgba(52,152,219,0.15);
    color: #3498db;
}
.type-annuel {
    background: rgba(155,89,182,0.15);
    color: #9b59b6;
}
.type-ponctuel {
    background: rgba(230,126,34,0.15);
    color: #e67e22;
}

/* Barre de progression */
.progress-container {
    width: 100%;
}

.progress-bar {
    background: var(--surface2);
    border-radius: 10px;
    height: 30px;
    overflow: hidden;
    margin: 10px 0;
}

.progress-fill {
    height: 100%;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding-right: 10px;
    transition: width 0.5s ease;
}

.progress-text {
    color: white;
    font-size: 12px;
    font-weight: bold;
    text-shadow: 0 0 2px rgba(0,0,0,0.5);
}

.progress-dates {
    display: flex;
    justify-content: space-between;
    font-size: 0.75rem;
    color: var(--txt-muted);
}

.progress-dates i {
    margin-right: 5px;
}

/* Options supplémentaires */
.options-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 5px;
}

.option-item {
    background: rgba(0,168,107,0.1);
    border: 1px solid rgba(0,168,107,0.2);
    border-radius: 20px;
    padding: 6px 12px;
    font-size: 0.8rem;
    color: var(--txt);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.option-item i {
    color: var(--green);
    font-size: 0.7rem;
}

/* Boutons */
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

.btn-edit:hover {
    transform: translateY(-2px);
    filter: brightness(1.05);
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

.btn-secondary:hover {
    background: var(--surface3);
    color: var(--txt);
}

/* Responsive */
@media (max-width: 768px) {
    .detail-card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .detail-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    .detail-group.span-2 {
        grid-column: span 1;
    }
    .header-actions {
        width: 100%;
    }
    .btn-edit, .btn-secondary {
        flex: 1;
        justify-content: center;
    }
    .progress-dates {
        flex-direction: column;
        gap: 5px;
        text-align: center;
    }
    .options-list {
        flex-direction: column;
    }
}
</style>

<?php include __DIR__ . '/../layout/footer.php'; ?>