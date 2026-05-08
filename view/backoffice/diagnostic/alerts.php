<?php
$pageTitle = "Back Office - Alertes urgentes";
$bodyClass = "admin-body";
$diagnostics = $controller->getUrgentDiagnostics();
ob_start();
?>
<aside class="sidebar">
    <div class="sidebar-logo"><span>TechCare</span></div>
    <div class="sidebar-nav">
        <a class="sidebar-item " href="index.php?action=admin_dashboard">Dashboard</a>
        <a class="sidebar-item " href="index.php?action=back_show">Diagnostics</a>
        <a class="sidebar-item " href="index.php?action=solution_back_show">Solutions</a>
        <a class="sidebar-item active" href="index.php?action=admin_alerts">Alertes urgentes</a>
        <a class="sidebar-item " href="index.php?action=admin_relations">Relations</a>
        <a class="sidebar-item " href="index.php?action=admin_analyse">Analyse automatique</a>
        <a class="sidebar-item " href="index.php?action=admin_clean">Nettoyage</a>
    </div>
</aside>

<div class="admin-wrapper">
    <div class="admin-topbar"><div class="topbar-title">Alertes urgentes</div></div>
    <main class="admin-tab active-tab">
        <div class="admin-card">
            <div class="card-head"><h3>Diagnostics à priorité élevée</h3><span class="count-badge"><?= count($diagnostics); ?></span></div>
            <div class="table-scroll"><table class="data-table"><thead><tr><th>ID</th><th>Type</th><th>Marque</th><th>Modèle</th><th>Catégorie</th><th>Action</th></tr></thead><tbody>
            <?php foreach ($diagnostics as $d): ?><tr><td><?= (int)$d['idDiagnostic']; ?></td><td><?= htmlspecialchars($d['typeObjet']); ?></td><td><?= htmlspecialchars($d['marque']); ?></td><td><?= htmlspecialchars($d['modele']); ?></td><td><?= htmlspecialchars($d['categorieProbleme']); ?></td><td><a class="btn-edit" href="index.php?action=back_show&id=<?= (int)$d['idDiagnostic']; ?>">Voir</a></td></tr><?php endforeach; ?>
            <?php if (empty($diagnostics)): ?><tr><td colspan="6">Aucun diagnostic urgent.</td></tr><?php endif; ?>
            </tbody></table></div>
        </div>
    </main>
</div>
<?php $content=ob_get_clean(); include __DIR__.'/../../layouts/app.php'; ?>
