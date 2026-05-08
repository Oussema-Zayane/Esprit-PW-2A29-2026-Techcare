<?php
$pageTitle = "Back Office - Audit admin";
$bodyClass = "admin-body";
$auditLogs = $controller->getAdminAuditLogs();
$auditStats = $controller->getAdminAuditStats();
ob_start();
?>
<aside class="sidebar">
    <div class="sidebar-logo"><span>TechCare</span></div>
    <div class="sidebar-nav">
        <a class="sidebar-item" href="index.php?action=admin_dashboard">Smart Dashboard</a>
        <a class="sidebar-item" href="index.php?action=back_show">Diagnostics</a>
        <a class="sidebar-item" href="index.php?action=solution_back_show">Solutions</a>
        <a class="sidebar-item" href="index.php?action=admin_alerts">Alertes urgentes</a>
        <a class="sidebar-item" href="index.php?action=admin_relations">Relations</a>
        <a class="sidebar-item" href="index.php?action=admin_analyse">Analyse prédictive</a>
        <a class="sidebar-item active" href="index.php?action=admin_audit">Audit admin</a>
        <a class="sidebar-item" href="index.php?action=admin_clean">Nettoyage</a>
        <a class="sidebar-item" href="index.php?action=front_show">← Retour site</a>
    </div>
</aside>

<div class="admin-wrapper">
    <div class="admin-topbar"><div class="topbar-title">Audit / Journal admin</div><a class="btn-outline" href="index.php?action=front_show">Retour site</a></div>
    <main class="admin-tab active-tab">
        <div class="admin-card">
            <div class="card-head"><h3>Résumé des actions</h3></div>
            <div class="admin-insight-grid">
                <?php foreach ($auditStats as $stat): ?>
                    <div class="admin-insight"><b><?= htmlspecialchars($stat['actionType']); ?></b><span><?= (int)$stat['total']; ?></span></div>
                <?php endforeach; ?>
                <?php if (empty($auditStats)): ?><div class="admin-insight"><b>Aucune action</b><span>0</span></div><?php endif; ?>
            </div>
        </div>

        <div class="admin-card mt-6">
            <div class="card-head"><h3>Journal des actions importantes</h3></div>
            <div class="table-scroll">
                <table class="data-table">
                    <thead><tr><th>Date</th><th>Admin</th><th>Action</th><th>Entité</th><th>ID</th><th>Détails</th></tr></thead>
                    <tbody>
                    <?php foreach ($auditLogs as $log): ?>
                        <tr>
                            <td><?= htmlspecialchars($log['createdAt']); ?></td>
                            <td><?= htmlspecialchars($log['adminName']); ?></td>
                            <td><span class="admin-badge warning"><?= htmlspecialchars($log['actionType']); ?></span></td>
                            <td><?= htmlspecialchars($log['entityType']); ?></td>
                            <td><?= htmlspecialchars($log['entityId'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($log['details']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($auditLogs)): ?><tr><td colspan="6">Aucune action enregistrée pour le moment.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<?php $content = ob_get_clean(); include __DIR__ . '/../layouts/app.php'; ?>
