<?php
$pageTitle = "Back Office - Relations Diagnostic Solution";
$bodyClass = "admin-body";
$relations = $controller->getDiagnosticSolutionRelations();
ob_start();
?>
<aside class="sidebar">
    <div class="sidebar-logo"><span>TechCare</span></div>
    <div class="sidebar-nav">
        <a class="sidebar-item " href="index.php?action=admin_dashboard">Dashboard</a>
        <a class="sidebar-item " href="index.php?action=back_show">Diagnostics</a>
        <a class="sidebar-item " href="index.php?action=solution_back_show">Solutions</a>
        <a class="sidebar-item " href="index.php?action=admin_alerts">Alertes urgentes</a>
        <a class="sidebar-item active" href="index.php?action=admin_relations">Relations</a>
        <a class="sidebar-item " href="index.php?action=admin_analyse">Analyse automatique</a>
        <a class="sidebar-item " href="index.php?action=admin_clean">Nettoyage</a>
    </div>
</aside>

<div class="admin-wrapper">
    <div class="admin-topbar"><div class="topbar-title">Gestion relation Diagnostic → Solution</div></div>
    <main class="admin-tab active-tab">
        <div class="admin-card">
            <div class="card-head"><h3>Diagnostics et nombre de solutions liées</h3></div>
            <div class="table-scroll"><table class="data-table"><thead><tr><th>ID</th><th>Diagnostic</th><th>Catégorie</th><th>Urgence</th><th>Solutions</th><th>Actions</th></tr></thead><tbody>
            <?php foreach ($relations as $r): ?><tr><td><?= (int)$r['idDiagnostic']; ?></td><td><?= htmlspecialchars($r['typeObjet'].' - '.$r['marque'].' '.$r['modele']); ?></td><td><?= htmlspecialchars($r['categorieProbleme']); ?></td><td><?= htmlspecialchars($r['urgence']); ?></td><td><span class="admin-badge <?= ((int)$r['totalSolutions']===0)?'danger':''; ?>"><?= (int)$r['totalSolutions']; ?></span></td><td class="tbl-actions"><a class="btn-edit" href="index.php?action=solution_front_create&idDiagnostic=<?= (int)$r['idDiagnostic']; ?>">Ajouter solution</a><a class="btn-edit" href="index.php?action=solution_search&idDiagnostic=<?= (int)$r['idDiagnostic']; ?>">Voir solutions</a></td></tr><?php endforeach; ?>
            <?php if (empty($relations)): ?><tr><td colspan="6">Aucune relation disponible.</td></tr><?php endif; ?>
            </tbody></table></div>
        </div>
    </main>
</div>
<?php $content=ob_get_clean(); include __DIR__.'/../../layouts/app.php'; ?>
