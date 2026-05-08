<?php
$id = $_GET['id'] ?? null;
$diagnostic = $controller->getDiagnosticById($id);

if (!$diagnostic) {
    die("Diagnostic introuvable.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->deleteDiagnostic($id);
    header('Location: index.php?action=front_show');
    exit;
}

$pageTitle = "Front Office - Supprimer Diagnostic";
$bodyClass = "front-body";

ob_start();
?>

<nav class="navbar">
    <a href="#" class="navbar-brand"><span class="brand-accent">TechCare</span></a>
</nav>

<section class="products-section">
    <div class="admin-card form-card">
        <div class="card-head"><h3>Supprimer un diagnostic</h3></div>
        <div style="padding: 1.5rem;">
            <p>Voulez-vous vraiment supprimer le diagnostic #<?= $diagnostic['idDiagnostic']; ?> ?</p>
            <br>
            <form method="POST">
                <button class="btn-danger" type="submit">Supprimer</button>
                <a class="btn-outline" href="index.php?action=front_show">Annuler</a>
            </form>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>