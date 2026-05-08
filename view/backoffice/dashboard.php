<?php
$pageTitle = "Back Office - Smart Dashboard";
$bodyClass = "admin-body";
$stats = $controller->getSmartDashboardData();
$urgents = $controller->getUrgentDiagnostics();
$performanceSolutions = $solutionController->getSolutionPerformance();
ob_start();
?>
<aside class="sidebar">
    <div class="sidebar-logo"><span>TechCare</span></div>
    <div class="sidebar-nav">
        <a class="sidebar-item active" href="index.php?action=admin_dashboard">Smart Dashboard</a>
        <a class="sidebar-item" href="index.php?action=back_show">Diagnostics</a>
        <a class="sidebar-item" href="index.php?action=solution_back_show">Solutions</a>
        <a class="sidebar-item" href="index.php?action=admin_alerts">Alertes urgentes</a>
        <a class="sidebar-item" href="index.php?action=admin_relations">Relations</a>
        <a class="sidebar-item" href="index.php?action=admin_analyse">Analyse prédictive</a>
        <a class="sidebar-item" href="index.php?action=admin_audit">Audit admin</a>
        <a class="sidebar-item" href="index.php?action=admin_clean">Nettoyage</a>
        <a class="sidebar-item" href="index.php?action=front_show">← Retour site</a>
    </div>
</aside>

<div class="admin-wrapper">
    <div class="admin-topbar">
        <div class="topbar-title">Smart Dashboard Admin</div>
        <a class="btn-outline" href="index.php?action=front_show">Retour site</a>
    </div>
    <main class="admin-tab active-tab">
        <div class="admin-dashboard-grid">
            <div class="admin-stat-card"><span>Diagnostics</span><strong><?= (int)$stats['totalDiagnostics']; ?></strong><small>Total enregistré</small></div>
            <div class="admin-stat-card danger"><span>Taux d’urgence</span><strong><?= htmlspecialchars($stats['tauxUrgence']); ?>%</strong><small><?= (int)$stats['totalUrgents']; ?> diagnostics urgents</small></div>
            <div class="admin-stat-card"><span>Solutions</span><strong><?= (int)$stats['totalSolutions']; ?></strong><small>Solutions créées</small></div>
            <div class="admin-stat-card warning"><span>Évolution</span><strong><?= ($stats['evolution'] >= 0 ? '+' : '') . (int)$stats['evolution']; ?></strong><small>Aujourd’hui vs hier</small></div>
        </div>

        <div class="admin-card mt-6">
            <div class="card-head"><h3>Dashboard décisionnel</h3></div>
            <div class="admin-insight-grid">
                <div class="admin-insight"><b>Type d’appareil le plus problématique</b><span><?= htmlspecialchars($stats['typeFrequent']); ?></span></div>
                <div class="admin-insight"><b>Catégorie la plus fréquente</b><span><?= htmlspecialchars($stats['categorieFrequente']); ?></span></div>
                <div class="admin-insight"><b>Décision recommandée</b><span><?= htmlspecialchars($stats['decision']); ?></span></div>
            </div>
        </div>

        <div class="admin-card mt-6 smart-prediction">
            <div class="card-head"><h3>Analyse prédictive simple</h3><a class="btn-outline" href="index.php?action=admin_analyse">Voir analyse</a></div>
            <p class="prediction-text">🧠 <?= htmlspecialchars($stats['prediction']); ?></p>
            <p class="prediction-sub">Basé sur les types d’appareils, les catégories fréquentes et le taux d’urgence.</p>
        </div>

        <div class="admin-card mt-6">
            <div class="card-head"><h3>Score de performance des solutions</h3><a class="btn-outline" href="index.php?action=solution_back_show">Gérer solutions</a></div>
            <div class="table-scroll"><table class="data-table"><thead><tr><th>Solution</th><th>Diagnostic lié</th><th>Efficacité</th><th>Score</th><th>Décision</th></tr></thead><tbody>
            <?php foreach ($performanceSolutions as $s): ?>
                <tr>
                    <td><?= htmlspecialchars(strlen($s['contenu'] ?? '') > 60 ? substr($s['contenu'], 0, 60) . '...' : ($s['contenu'] ?? '')); ?></td>
                    <td><?= htmlspecialchars(($s['typeObjet'] ?? '') . ' - ' . ($s['categorieProbleme'] ?? '')); ?></td>
                    <td><?= htmlspecialchars($s['efficaciteEstimee'] ?? ''); ?></td>
                    <td><span class="admin-badge <?= ((int)$s['scorePerformance'] >= 80) ? 'success' : (((int)$s['scorePerformance'] >= 50) ? 'warning' : 'danger'); ?>"><?= (int)$s['scorePerformance']; ?>%</span></td>
                    <td><?= ((int)$s['scorePerformance'] >= 80) ? 'Solution recommandée' : (((int)$s['scorePerformance'] >= 50) ? 'À surveiller' : 'À améliorer'); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($performanceSolutions)): ?><tr><td colspan="5">Aucune solution disponible.</td></tr><?php endif; ?>
            </tbody></table></div>
        </div>

        <div class="admin-card mt-6">
            <div class="card-head"><h3>Dernières alertes urgentes</h3><a class="btn-outline" href="index.php?action=admin_alerts">Voir tout</a></div>
            <div class="table-scroll"><table class="data-table"><thead><tr><th>ID</th><th>Objet</th><th>Marque</th><th>Catégorie</th><th>Urgence</th></tr></thead><tbody>
            <?php foreach (array_slice($urgents,0,5) as $d): ?><tr><td><?= (int)$d['idDiagnostic']; ?></td><td><?= htmlspecialchars($d['typeObjet']); ?></td><td><?= htmlspecialchars($d['marque']); ?></td><td><?= htmlspecialchars($d['categorieProbleme']); ?></td><td><span class="admin-badge danger">Urgent</span></td></tr><?php endforeach; ?>
            <?php if (empty($urgents)): ?><tr><td colspan="5">Aucune alerte urgente.</td></tr><?php endif; ?>
            </tbody></table></div>
        </div>
    </main>
</div>
<?php $content=ob_get_clean(); include __DIR__.'/../layouts/app.php'; ?>
