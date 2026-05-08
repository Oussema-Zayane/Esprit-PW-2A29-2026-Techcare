<?php
// If stats not passed from controller, define empty array
if (!isset($stats)) {
    $stats = ['total_abonnements' => 0, 'abonnements_actifs' => 0];
}
?>

<!-- Dashboard Grid -->
<div class="page-header">
    <h1>Tableau de bord</h1>
    <div class="page-header-actions">
        <a href="index.php?admin&action=ajouter" class="btn-primary">
            <i class="fa-solid fa-plus"></i> Nouvel abonnement
        </a>
    </div>
</div>

<div class="dashboard-grid">
    <div class="stat-card">
        <div class="label">
            <i class="fa-solid fa-check-circle" style="color: #00A86B;"></i>
            Abonnements actifs
        </div>
        <div class="value"><?= $stats['abonnements_actifs'] ?? 0 ?></div>
        <div class="change">↑ 5% ce mois</div>
    </div>

    <div class="stat-card">
        <div class="label">
            <i class="fa-solid fa-list" style="color: #00cf85;"></i>
            Total d'abonnements
        </div>
        <div class="value"><?= $stats['total_abonnements'] ?? 0 ?></div>
        <div class="change">↑ 12% ce mois</div>
    </div>

    <div class="stat-card">
        <div class="label">
            <i class="fa-solid fa-chart-line" style="color: #0891b2;"></i>
            Revenu mensuel
        </div>
        <div class="value">0 DT</div>
        <div class="change">↑ 8% ce mois</div>
    </div>

    <div class="stat-card">
        <div class="label">
            <i class="fa-solid fa-user-check" style="color: #f39c12;"></i>
            Taux de renouvellement
        </div>
        <div class="value">95%</div>
        <div class="change">↓ 2% ce mois</div>
    </div>
</div>

<!-- Recent Subscriptions -->
<div class="table-container">
    <div class="table-header">
        <h3>Abonnements récents</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>Type</th>
                <th>Prix</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Jean Dupont</td>
                <td>Mensuel</td>
                <td>29.90 DT</td>
                <td>10/04/2026</td>
                <td>10/05/2026</td>
                <td><span class="badge badge-success"><i class="fa-solid fa-check"></i> Actif</span></td>
                <td class="table-actions">
                    <a href="index.php?admin&action=detail&id=1" class="btn-sm-outline" title="Voir">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    <a href="index.php?admin&action=modifier&id=1" class="btn-sm-outline" title="Modifier">
                        <i class="fa-solid fa-edit"></i>
                    </a>
                </td>
            </tr>
            <tr>
                <td>Marie Martin</td>
                <td>Annuel</td>
                <td>299 DT</td>
                <td>08/04/2026</td>
                <td>08/04/2027</td>
                <td><span class="badge badge-success"><i class="fa-solid fa-check"></i> Actif</span></td>
                <td class="table-actions">
                    <a href="index.php?admin&action=detail&id=2" class="btn-sm-outline" title="Voir">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    <a href="index.php?admin&action=modifier&id=2" class="btn-sm-outline" title="Modifier">
                        <i class="fa-solid fa-edit"></i>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</div>
