<?php
$id = $_GET['id'] ?? null;
$diagnostic = $controller->getDiagnosticById($id);

if (!$diagnostic) {
    die("Diagnostic introuvable.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->deleteDiagnostic($id);
    header('Location: index.php?action=back_show');
    exit;
}

$pageTitle = "Back Office - Supprimer Diagnostic";
$bodyClass = "admin-body";

ob_start();
?>

<div class="admin-wrapper">
    <main class="admin-tab active-tab">
        <div class="admin-card form-card">
            <div class="card-head"><h3>Supprimer un diagnostic</h3></div>
            <div style="padding: 1.5rem;">
                <p>Voulez-vous vraiment supprimer le diagnostic #<?= $diagnostic['idDiagnostic']; ?> ?</p>
                <br>
                <form method="POST">
                    <button class="btn-danger" type="submit">Supprimer</button>
                    <a class="btn-outline" href="index.php?action=back_show">Annuler</a>
                </form>
            </div>
        </div>
    </main>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>