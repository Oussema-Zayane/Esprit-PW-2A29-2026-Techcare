<?php
$pageTitle = "Back Office - Solutions";
$bodyClass = "admin-body";

$solutions = $solutionController->getAllSolutions();

ob_start();
?>

<div class="admin-wrapper">
    <div class="admin-topbar">
        <div class="topbar-title">Back Office - Solutions</div>
    </div>

    <main class="admin-tab active-tab">
        <div style="display:flex; gap:10px; margin-bottom:20px;">
            <a class="btn-outline" href="index.php?action=back_show">Diagnostic</a>
            <a class="btn-primary" href="index.php?action=solution_back_show">Solution</a>
        </div>

        <div class="admin-card">
            <div class="card-head">
                <h3>Liste des solutions</h3>
                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <a class="btn-outline" href="index.php?action=back_show">Diagnostics</a>
                    <a class="btn-primary" href="index.php?action=solution_back_create">Créer</a>
                </div>
            </div>

            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Contenu</th>
                            <th>Priorité</th>
                            <th>Efficacité</th>
                            <th>Type</th>
                            <th>Justification</th>
                            <th>Suggestion IA</th>
                            <th>Diagnostic</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($solutions as $s): ?>
                            <tr>
                                <td><?= htmlspecialchars($s['idSolution']); ?></td>
                                <td><?= htmlspecialchars($s['contenu'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($s['niveauPriorite'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($s['efficaciteEstimee'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($s['typeSolution'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($s['justificationTechnique'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($s['suggestionIA'] ?? ''); ?></td>
                                <td><?= htmlspecialchars($s['typeObjet'] ?? '') . ' - ' . htmlspecialchars($s['marque'] ?? ''); ?></td>
                                <td class="tbl-actions">
                                    <a class="btn-edit" href="index.php?action=solution_back_edit&id=<?= $s['idSolution']; ?>">Edit</a>
                                    <a class="btn-del" href="index.php?action=solution_back_delete&id=<?= $s['idSolution']; ?>">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>
