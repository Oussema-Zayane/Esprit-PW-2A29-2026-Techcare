<?php
$pageTitle = "Back Office - Nettoyage";
$bodyClass = "admin-body";
$deleted = null;
if (isset($_GET['run']) && $_GET['run'] === '1') {
    $deleted = $controller->cleanDiagnosticsWithoutSolution();
}
$relations = $controller->getDiagnosticSolutionRelations();
$without = array_filter($relations, fn($r)=>(int)$r['totalSolutions']===0);
ob_start();
?>
<aside class="sidebar">
    <div class="sidebar-logo"><span>TechCare</span></div>
    <div class="sidebar-nav">
        <a class="sidebar-item " href="index.php?action=admin_dashboard">Dashboard</a>
        <a class="sidebar-item " href="index.php?action=back_show">Diagnostics</a>
        <a class="sidebar-item " href="index.php?action=solution_back_show">Solutions</a>
        <a class="sidebar-item " href="index.php?action=admin_alerts">Alertes urgentes</a>
        <a class="sidebar-item " href="index.php?action=admin_relations">Relations</a>
        <a class="sidebar-item " href="index.php?action=admin_analyse">Analyse automatique</a>
        <a class="sidebar-item active" href="index.php?action=admin_clean">Nettoyage</a>
    </div>
</aside>

<div class="admin-wrapper">
    <div class="admin-topbar"><div class="topbar-title">Nettoyage intelligent</div></div>
    <main class="admin-tab active-tab">
        <div class="admin-card">
            <div class="card-head"><h3>Diagnostics sans solution</h3><span class="count-badge"><?= count($without); ?></span></div>
            <?php if($deleted !== null): ?><div class="admin-clean-success">Nettoyage terminé : <?= (int)$deleted; ?> diagnostic(s) supprimé(s).</div><?php endif; ?>
            <p style="color:#8b9ab5;margin-bottom:18px;">Cette action supprime uniquement les diagnostics qui ne possèdent aucune solution liée.</p>
            <a class="btn-danger" onclick="return confirm('Confirmer le nettoyage des diagnostics sans solution ?');" href="index.php?action=admin_clean&run=1">Nettoyer maintenant</a>
        </div>
        <div class="admin-card mt-6"><div class="card-head"><h3>Aperçu avant nettoyage</h3></div><div class="table-scroll"><table class="data-table"><thead><tr><th>ID</th><th>Diagnostic</th><th>Catégorie</th><th>Urgence</th></tr></thead><tbody>
        <?php foreach($without as $r): ?><tr><td><?= (int)$r['idDiagnostic']; ?></td><td><?= htmlspecialchars($r['typeObjet'].' - '.$r['marque'].' '.$r['modele']); ?></td><td><?= htmlspecialchars($r['categorieProbleme']); ?></td><td><?= htmlspecialchars($r['urgence']); ?></td></tr><?php endforeach; ?>
        <?php if(empty($without)): ?><tr><td colspan="4">Aucun diagnostic à nettoyer.</td></tr><?php endif; ?>
        </tbody></table></div></div>
    </main>
</div>
<?php $content=ob_get_clean(); include __DIR__.'/../../layouts/app.php'; ?>
