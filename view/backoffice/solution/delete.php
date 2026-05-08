<?php
$id = $_GET['id'] ?? null;
$solution = $solutionController->getSolutionById($id);

if (!$solution) {
    die("Solution introuvable.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $solutionController->deleteSolution($id);
    header("Location: index.php?action=solution_back_show");
    exit;
}

$pageTitle = "Back Office - Supprimer Solution";
$bodyClass = "admin-body";

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

        <div class="admin-card form-card">
            <div class="card-head">
                <h3>Supprimer une solution</h3>
            </div>

            <div style="padding: 1.5rem;">
                <p>Voulez-vous vraiment supprimer la solution #<?= $solution['idSolution']; ?> ?</p>
                <br>
                <form method="POST">
                    <button class="btn-danger" type="submit">Supprimer</button>
                    <a class="btn-outline" href="index.php?action=solution_back_show">Annuler</a>
                </form>
            </div>
        </div>
    </main>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>