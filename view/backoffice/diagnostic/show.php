<?php
$pageTitle = "Back Office - Diagnostics";
$bodyClass = "admin-body";

$diagnostics = $controller->getAllDiagnostics();
$id = $_GET['id'] ?? null;
$selected = $id ? $controller->getDiagnosticById($id) : null;

ob_start();
?>

<aside class="sidebar">
    <div class="sidebar-logo"><span>TechCare</span></div>
    <div class="sidebar-nav">
        <a class="sidebar-item" href="index.php?action=admin_dashboard">Dashboard</a>
        <a class="sidebar-item active" href="index.php?action=back_show">Diagnostics</a>
        <a class="sidebar-item" href="index.php?action=solution_back_show">Solutions</a>
        <a class="sidebar-item" href="index.php?action=admin_alerts">Alertes urgentes</a>
        <a class="sidebar-item" href="index.php?action=admin_relations">Relations</a>
        <a class="sidebar-item" href="index.php?action=admin_analyse">Analyse automatique</a>
        <a class="sidebar-item" href="index.php?action=admin_clean">Nettoyage</a>
    </div>
</aside>

<div class="admin-wrapper">
    <div class="admin-topbar">
        <div class="topbar-title">Back Office - Diagnostics</div>
    </div>

    <main class="admin-tab active-tab">
        <div class="admin-card">
            <div class="card-head">
    <h3>Liste des diagnostics</h3>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a class="btn-outline" href="index.php?action=solution_back_show">Solutions</a>
        <a class="btn-primary" href="index.php?action=back_create">Créer</a>
    </div>
</div>

            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Marque</th>
                            <th>Catégorie</th>
                            <th>Urgence</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($diagnostics as $d): ?>
                        <tr>
                            <td><?= $d['idDiagnostic']; ?></td>
                            <td><?= htmlspecialchars($d['typeObjet']); ?></td>
                            <td><?= htmlspecialchars($d['marque']); ?></td>
                            <td><?= htmlspecialchars($d['categorieProbleme']); ?></td>
                            <td><?= htmlspecialchars($d['urgence']); ?></td>
                            <td class="tbl-actions">
                                <a class="btn-edit" href="index.php?action=back_show&id=<?= $d['idDiagnostic']; ?>">Voir</a>
                                <a class="btn-edit" href="index.php?action=back_edit&id=<?= $d['idDiagnostic']; ?>">Modifier</a>
                                <a class="btn-del" href="index.php?action=back_delete&id=<?= $d['idDiagnostic']; ?>">Supprimer</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <br>

        <?php if ($selected): ?>
        <div class="admin-card form-card">
            <div class="card-head"><h3>Détail du diagnostic</h3></div>

            <div class="form-grid">
                <div class="form-group"><label>ID</label><input value="<?= $selected['idDiagnostic']; ?>" readonly></div>
                <div class="form-group"><label>Type Objet</label><input value="<?= htmlspecialchars($selected['typeObjet']); ?>" readonly></div>
                <div class="form-group"><label>Marque</label><input value="<?= htmlspecialchars($selected['marque']); ?>" readonly></div>
                <div class="form-group"><label>Modèle</label><input value="<?= htmlspecialchars($selected['modele']); ?>" readonly></div>
                <div class="form-group"><label>Date Achat</label><input value="<?= $selected['dateAchat']; ?>" readonly></div>
                <div class="form-group"><label>Statut Objet</label><input value="<?= htmlspecialchars($selected['statutObjet']); ?>" readonly></div>
                <div class="form-group"><label>Catégorie</label><input value="<?= htmlspecialchars($selected['categorieProbleme']); ?>" readonly></div>
                <div class="form-group"><label>Urgence</label><input value="<?= htmlspecialchars($selected['urgence']); ?>" readonly></div>
                <div class="form-group span-2"><label>Description</label><textarea readonly><?= htmlspecialchars($selected['description']); ?></textarea></div>
                <div class="form-group span-2"><label>Suggestion IA</label><textarea readonly><?= htmlspecialchars($selected['suggestionIA']); ?></textarea></div>
            </div>
        </div>
        <?php endif; ?>
    </main>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>