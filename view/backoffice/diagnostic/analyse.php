<?php
$pageTitle = "Back Office - Analyse automatique";
$bodyClass = "admin-body";
$analysis = $controller->getAdminAnalysis();
function totalRows($rows){ return array_sum(array_map(fn($r)=>(int)$r['total'], $rows)); }
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
        <a class="sidebar-item active" href="index.php?action=admin_analyse">Analyse automatique</a>
        <a class="sidebar-item " href="index.php?action=admin_clean">Nettoyage</a>
    </div>
</aside>

<div class="admin-wrapper">
    <div class="admin-topbar">
        <div class="topbar-title">Analyse automatique</div>
        <div class="topbar-actions">
            <a class="btn-outline" href="index.php?action=admin_dashboard">← Dashboard</a>
            <button type="button" class="btn-primary print-btn" onclick="window.print()">Enregistrer PDF</button>
        </div>
    </div>
    <main class="admin-tab active-tab">
        <div class="admin-card">
            <div class="card-head"><h3>Tendances détectées automatiquement</h3></div>
            <div class="admin-analysis-grid">
                <?php $blocks=['Par type objet'=>$analysis['parType'],'Par catégorie'=>$analysis['parCategorie'],'Par urgence'=>$analysis['parUrgence']]; ?>
                <?php foreach($blocks as $title=>$rows): $total=totalRows($rows); ?>
                    <div class="admin-analysis-card"><h3><?= htmlspecialchars($title); ?></h3>
                    <?php foreach($rows as $row): $pct=$total>0?round(((int)$row['total']/$total)*100):0; ?>
                        <div class="analysis-line"><div><span><?= htmlspecialchars($row['label']); ?></span><b><?= (int)$row['total']; ?> - <?= $pct; ?>%</b></div><div class="analysis-bar"><i style="width:<?= $pct; ?>%"></i></div></div>
                    <?php endforeach; ?>
                    <?php if(empty($rows)): ?><p>Aucune donnée.</p><?php endif; ?></div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="admin-card mt-6"><div class="card-head"><h3>Solutions par diagnostic</h3></div><div class="table-scroll"><table class="data-table"><thead><tr><th>Diagnostic</th><th>Total solutions</th></tr></thead><tbody>
        <?php foreach($analysis['solutionsParDiagnostic'] as $r): ?><tr><td><?= htmlspecialchars($r['typeObjet'].' - '.$r['marque'].' '.$r['modele']); ?></td><td><?= (int)$r['totalSolutions']; ?></td></tr><?php endforeach; ?>
        </tbody></table></div></div>
    </main>
</div>
<?php $content=ob_get_clean(); include __DIR__.'/../../layouts/app.php'; ?>
