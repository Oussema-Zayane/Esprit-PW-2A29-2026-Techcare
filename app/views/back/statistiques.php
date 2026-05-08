<?php include __DIR__ . '/layout/header.php'; ?>
<?php include __DIR__ . '/layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <div class="page-title">
            <h1><i class="fa-solid fa-chart-simple"></i> Statistiques</h1>
            <p>Analyse des abonnements TechCare</p>
        </div>
        <div class="user-info">
            <div class="user-avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <span>Admin</span>
        </div>
    </div>

    <!-- Cartes statistiques -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3><?= count($abonnements) ?></h3>
                <p>Total abonnements</p>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-tags"></i>
            </div>
        </div>
        <div class="stat-card green">
            <div class="stat-info">
                <h3><?= count(array_filter($abonnements, fn($a) => $a['statut'] == 'actif')) ?></h3>
                <p>Abonnements actifs</p>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
        <div class="stat-card orange">
            <div class="stat-info">
                <h3><?= count(array_filter($abonnements, fn($a) => $a['statut'] == 'expiré')) ?></h3>
                <p>Abonnements expirés</p>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-calendar-xmark"></i>
            </div>
        </div>
        <div class="stat-card blue">
            <div class="stat-info">
                <h3><?= number_format(array_sum(array_column($abonnements, 'prix')), 2) ?> DT</h3>
                <p>Revenus totaux</p>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-coins"></i>
            </div>
        </div>
    </div>

    <!-- Graphiques en cercle -->
    <div class="charts-container">
        <!-- Graphique par type d'abonnement -->
        <div class="chart-card">
            <h3><i class="fa-solid fa-chart-pie"></i> Répartition par type</h3>
            <div class="donut-wrapper">
                <?php
                $total = count($abonnements);
                $mensuel = count(array_filter($abonnements, fn($a) => $a['type_abonnement'] == 'mensuel'));
                $annuel = count(array_filter($abonnements, fn($a) => $a['type_abonnement'] == 'annuel'));
                $ponctuel = count(array_filter($abonnements, fn($a) => $a['type_abonnement'] == 'ponctuel'));
                
                $mensuelDeg = $total > 0 ? ($mensuel / $total) * 360 : 0;
                $annuelDeg = $total > 0 ? ($annuel / $total) * 360 : 0;
                $ponctuelDeg = $total > 0 ? ($ponctuel / $total) * 360 : 0;
                ?>
                
                <div class="donut-chart">
                    <svg viewBox="0 0 100 100" class="donut">
                        <!-- Mensuel -->
                        <circle cx="50" cy="50" r="40" fill="transparent" stroke="#3498db" stroke-width="15"
                                stroke-dasharray="<?= $mensuelDeg ?> <?= 360 - $mensuelDeg ?>"
                                stroke-dashoffset="0"
                                transform="rotate(-90 50 50)" />
                        <!-- Annuel -->
                        <circle cx="50" cy="50" r="40" fill="transparent" stroke="#9b59b6" stroke-width="15"
                                stroke-dasharray="<?= $annuelDeg ?> <?= 360 - $annuelDeg ?>"
                                stroke-dashoffset="-<?= $mensuelDeg ?>"
                                transform="rotate(-90 50 50)" />
                        <!-- Ponctuel -->
                        <circle cx="50" cy="50" r="40" fill="transparent" stroke="#e67e22" stroke-width="15"
                                stroke-dasharray="<?= $ponctuelDeg ?> <?= 360 - $ponctuelDeg ?>"
                                stroke-dashoffset="-<?= $mensuelDeg + $annuelDeg ?>"
                                transform="rotate(-90 50 50)" />
                    </svg>
                    <div class="donut-center">
                        <span class="donut-total"><?= $total ?></span>
                        <span class="donut-label">Total</span>
                    </div>
                </div>
                
                <div class="donut-legend">
                    <div class="legend-item">
                        <span class="legend-color" style="background: #3498db;"></span>
                        <span class="legend-label">Mensuel</span>
                        <span class="legend-value"><?= $mensuel ?> (<?= $total > 0 ? round($mensuel / $total * 100) : 0 ?>%)</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background: #9b59b6;"></span>
                        <span class="legend-label">Annuel</span>
                        <span class="legend-value"><?= $annuel ?> (<?= $total > 0 ? round($annuel / $total * 100) : 0 ?>%)</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background: #e67e22;"></span>
                        <span class="legend-label">Ponctuel</span>
                        <span class="legend-value"><?= $ponctuel ?> (<?= $total > 0 ? round($ponctuel / $total * 100) : 0 ?>%)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique par statut -->
        <div class="chart-card">
            <h3><i class="fa-solid fa-chart-simple"></i> Répartition par statut</h3>
            <div class="donut-wrapper">
                <?php
                $actif = count(array_filter($abonnements, fn($a) => $a['statut'] == 'actif'));
                $suspendu = count(array_filter($abonnements, fn($a) => $a['statut'] == 'suspendu'));
                $expire = count(array_filter($abonnements, fn($a) => $a['statut'] == 'expiré'));
                $annule = count(array_filter($abonnements, fn($a) => $a['statut'] == 'annulé'));
                
                $actifDeg = $total > 0 ? ($actif / $total) * 360 : 0;
                $suspenduDeg = $total > 0 ? ($suspendu / $total) * 360 : 0;
                $expireDeg = $total > 0 ? ($expire / $total) * 360 : 0;
                $annuleDeg = $total > 0 ? ($annule / $total) * 360 : 0;
                ?>
                
                <div class="donut-chart">
                    <svg viewBox="0 0 100 100" class="donut">
                        <!-- Actif -->
                        <circle cx="50" cy="50" r="40" fill="transparent" stroke="#2ecc71" stroke-width="15"
                                stroke-dasharray="<?= $actifDeg ?> <?= 360 - $actifDeg ?>"
                                stroke-dashoffset="0"
                                transform="rotate(-90 50 50)" />
                        <!-- Suspendu -->
                        <circle cx="50" cy="50" r="40" fill="transparent" stroke="#f39c12" stroke-width="15"
                                stroke-dasharray="<?= $suspenduDeg ?> <?= 360 - $suspenduDeg ?>"
                                stroke-dashoffset="-<?= $actifDeg ?>"
                                transform="rotate(-90 50 50)" />
                        <!-- Expiré -->
                        <circle cx="50" cy="50" r="40" fill="transparent" stroke="#e74c3c" stroke-width="15"
                                stroke-dasharray="<?= $expireDeg ?> <?= 360 - $expireDeg ?>"
                                stroke-dashoffset="-<?= $actifDeg + $suspenduDeg ?>"
                                transform="rotate(-90 50 50)" />
                        <!-- Annulé -->
                        <circle cx="50" cy="50" r="40" fill="transparent" stroke="#95a5a6" stroke-width="15"
                                stroke-dasharray="<?= $annuleDeg ?> <?= 360 - $annuleDeg ?>"
                                stroke-dashoffset="-<?= $actifDeg + $suspenduDeg + $expireDeg ?>"
                                transform="rotate(-90 50 50)" />
                    </svg>
                    <div class="donut-center">
                        <span class="donut-total"><?= $total ?></span>
                        <span class="donut-label">Total</span>
                    </div>
                </div>
                
                <div class="donut-legend">
                    <div class="legend-item">
                        <span class="legend-color" style="background: #2ecc71;"></span>
                        <span class="legend-label">Actif</span>
                        <span class="legend-value"><?= $actif ?> (<?= $total > 0 ? round($actif / $total * 100) : 0 ?>%)</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background: #f39c12;"></span>
                        <span class="legend-label">Suspendu</span>
                        <span class="legend-value"><?= $suspendu ?> (<?= $total > 0 ? round($suspendu / $total * 100) : 0 ?>%)</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background: #e74c3c;"></span>
                        <span class="legend-label">Expiré</span>
                        <span class="legend-value"><?= $expire ?> (<?= $total > 0 ? round($expire / $total * 100) : 0 ?>%)</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-color" style="background: #95a5a6;"></span>
                        <span class="legend-label">Annulé</span>
                        <span class="legend-value"><?= $annule ?> (<?= $total > 0 ? round($annule / $total * 100) : 0 ?>%)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: var(--card);
    border-radius: 12px;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 1px solid var(--border);
}

.stat-info h3 {
    font-size: 28px;
    margin: 0;
    color: var(--txt);
}

.stat-info p {
    margin: 5px 0 0;
    color: var(--txt-muted);
    font-size: 13px;
}

.stat-icon {
    width: 48px;
    height: 48px;
    background: rgba(0,168,107,0.1);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: var(--green);
}

.stat-card.green .stat-icon { background: rgba(46,204,113,0.1); color: #2ecc71; }
.stat-card.orange .stat-icon { background: rgba(243,156,18,0.1); color: #f39c12; }
.stat-card.blue .stat-icon { background: rgba(52,152,219,0.1); color: #3498db; }

/* Graphiques en cercle */
.charts-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
}

.chart-card {
    background: var(--card);
    border-radius: 16px;
    padding: 20px;
    border: 1px solid var(--border);
}

.chart-card h3 {
    margin-bottom: 20px;
    font-size: 16px;
    color: var(--txt);
    display: flex;
    align-items: center;
    gap: 8px;
}

.chart-card h3 i {
    color: var(--green);
}

/* Donut chart */
.donut-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 30px;
    flex-wrap: wrap;
}

.donut-chart {
    position: relative;
    width: 140px;
    height: 140px;
}

.donut {
    width: 100%;
    height: 100%;
    transform: rotate(-90deg);
}

.donut-center {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.donut-total {
    display: block;
    font-size: 24px;
    font-weight: bold;
    color: var(--txt);
}

.donut-label {
    font-size: 10px;
    color: var(--txt-muted);
}

/* Légende */
.donut-legend {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.legend-color {
    width: 14px;
    height: 14px;
    border-radius: 4px;
}

.legend-label {
    font-size: 13px;
    color: var(--txt);
    min-width: 70px;
}

.legend-value {
    font-size: 12px;
    color: var(--txt-muted);
    font-weight: 500;
}

/* Responsive */
@media (max-width: 900px) {
    .charts-container {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .donut-wrapper {
        flex-direction: column;
        text-align: center;
    }
    
    .donut-legend {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .donut-legend {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>

<?php include __DIR__ . '/layout/footer.php'; ?>