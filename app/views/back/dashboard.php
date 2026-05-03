<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <div class="page-title">
            <h1><i class="fa-solid fa-chart-line"></i> Dashboard</h1>
            <p>Bienvenue dans l'administration TechCare</p>
        </div>
        <div class="user-info">
            <div class="user-avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <span>Admin</span>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3><?= $totalAbonnements ?? 0 ?></h3>
                <p>Total Abonnements</p>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-tags"></i>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3><?= $actifs ?? 0 ?></h3>
                <p>Actifs</p>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3><?= $expires ?? 0 ?></h3>
                <p>Expirés</p>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-calendar-xmark"></i>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-info">
                <h3><?= $revenus ?? 0 ?> DT</h3>
                <p>Revenus</p>
            </div>
            <div class="stat-icon">
                <i class="fa-solid fa-coins"></i>
            </div>
        </div>
    </div>

    <!-- Derniers abonnements -->
    <div class="table-container">
        <div class="table-header">
            <h3><i class="fa-solid fa-clock"></i> Derniers abonnements</h3>
            <a href="index.php?action=liste" class="btn-add">
                <i class="fa-solid fa-arrow-right"></i> Voir tout
            </a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Type</th>
                    <th>Prix</th>
                    <th>Date fin</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($derniersAbonnements as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['nom_client']) ?></td>
                    <td><?= ucfirst($a['type_abonnement']) ?></td>
                    <td><?= $a['prix'] ?> DT</td>
                    <td><?= date('d/m/Y', strtotime($a['date_fin'])) ?></td>
                    <td><span class="badge badge-<?= $a['statut'] ?>"><?= $a['statut'] ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'layout/footer.php'; ?>