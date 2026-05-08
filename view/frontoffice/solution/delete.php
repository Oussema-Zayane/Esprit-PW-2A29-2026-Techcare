<?php
$id = $_GET['id'] ?? null;
$solution = $solutionController->getSolutionById($id);

if (!$solution) {
    die("Solution introuvable.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $solutionController->deleteSolution($id);
    header("Location: index.php?action=solution_front_show");
    exit;
}

$pageTitle = "TechCare - Supprimer Solution";
$bodyClass = "front-body";

ob_start();
?>

<nav class="navbar">
    <a href="#" class="navbar-brand">
        <img src="assets/images/logo.png" alt="TechCare Logo" style="width:32px;height:32px;object-fit:contain;">
        <span class="brand-accent">TechCare</span>
    </a>

    <button class="hamburger" type="button">☰</button>

    <div class="navbar-menu">
        <a class="nav-link" href="#">Boutique</a>
        <a class="nav-link" href="#">Gamification</a>
        <a class="nav-link" href="index.php?action=front_show">Diagnostic</a>
        <a class="nav-link active" href="index.php?action=solution_search">Solution</a>
        <a class="nav-link" href="#">Abonnement</a>
        <a class="nav-link" href="index.php?action=back_show">Administration</a>
    </div>
</nav>

<section class="products-section">
    <div class="admin-card form-card">
        <div class="card-head">
            <h3>Supprimer une solution</h3>
        </div>

        <div style="padding: 1.5rem;">
            <p>Voulez-vous vraiment supprimer la solution #<?= $solution['idSolution']; ?> ?</p>
            <br>
            <form method="POST">
                <button class="btn-danger" type="submit">Supprimer</button>
                <a class="btn-outline" href="index.php?action=solution_front_show">Annuler</a>
            </form>
        </div>
    </div>
</section>

<script>
const hamburger = document.querySelector('.hamburger');
const menu = document.querySelector('.navbar-menu');

if (hamburger && menu) {
    hamburger.addEventListener('click', function () {
        menu.classList.toggle('open');
    });
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>