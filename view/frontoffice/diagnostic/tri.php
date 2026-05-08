<?php
$pageTitle = "TechCare - Tri Diagnostics";
$bodyClass = "front-body";

$ordre = $_GET['ordre'] ?? 'asc';
if (!in_array($ordre, ['asc', 'desc'])) {
    $ordre = 'asc';
}
$diagnostics = $controller->getDiagnosticsSortedByMarque($ordre);

ob_start();
?>

<nav class="navbar">
    <a href="index.php?action=front_show" class="navbar-brand">
        <img src="assets/images/logo.png" alt="TechCare Logo" style="width:32px;height:32px;object-fit:contain;">
        <span class="brand-accent">TechCare</span>
    </a>
    <button class="hamburger" type="button">☰</button>
    <div class="navbar-menu">
        <a class="nav-link" href="#">Boutique</a>
        <a class="nav-link" href="#">Gamification</a>
        <a class="nav-link active" href="index.php?action=front_show">Diagnostic</a>
        <a class="nav-link" href="index.php?action=solution_front_show">Solution</a>
        <a class="nav-link" href="#">Abonnement</a>
        <a class="nav-link" href="index.php?action=back_show">Administration</a>
    </div>
</nav>

<section class="hero metier-hero-small">
    <div class="hero-content">
        <span class="hero-badge">Métier 3</span>
        <h1 class="hero-title">Tri alphabétique <span class="gradient-text">par marque</span></h1>
        <p class="hero-subtitle">Afficher les diagnostics dans un tableau trié par marque en ordre croissant ou décroissant.</p>
    </div>
    <div class="hero-visual"><div class="hero-main-icon">↕</div></div>
</section>

<section class="products-section metier-page-wrap">
    <div class="metier-page-actions">
        <a class="btn-outline" href="index.php?action=front_show">← Retour aux diagnostics</a>
        <a class="metier-link-btn" href="index.php?action=diagnostic_stats"><span class="metier-icon">📊</span>Statistiques</a>
        <a class="metier-link-btn" href="index.php?action=diagnostic_filter"><span class="metier-icon">🔎</span>Filtrage intelligent</a>
        <a class="metier-link-btn active" href="index.php?action=diagnostic_tri"><span class="metier-icon">↕</span>Tri</a>
        <a class="metier-link-btn" href="index.php?action=diagnostic_score"><span class="metier-icon">🧠</span>Calcul intelligent</a>
        <a class="metier-link-btn" href="index.php?action=diagnostic_auto"><span class="metier-icon">🔁</span>Auto amélioration</a>
    </div>

    <div class="sort-card">
        <div>
            <h3>Choisir l’ordre du tri</h3>
            <p>Le tri est appliqué sur la colonne <strong>marque</strong>.</p>
        </div>
        <form method="GET" class="sort-form">
            <input type="hidden" name="action" value="diagnostic_tri">
            <select name="ordre">
                <option value="asc" <?= $ordre === 'asc' ? 'selected' : ''; ?>>Croissant A → Z</option>
                <option value="desc" <?= $ordre === 'desc' ? 'selected' : ''; ?>>Décroissant Z → A</option>
            </select>
            <button class="btn-primary" type="submit">Appliquer</button>
        </form>
    </div>

    <div class="admin-card metier-page-card">
        <div class="card-head">
            <h3>Diagnostics triés par marque</h3>
            <span class="count-badge"><?= count($diagnostics); ?></span>
        </div>
        <div class="table-scroll">
            <table class="data-table pretty-diagnostic-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Marque</th>
                        <th>Type objet</th>
                        <th>Modèle</th>
                        <th>Catégorie</th>
                        <th>Urgence</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($diagnostics as $d): ?>
                        <tr>
                            <td><?= htmlspecialchars($d['idDiagnostic']); ?></td>
                            <td><strong class="brand-cell"><?= htmlspecialchars($d['marque']); ?></strong></td>
                            <td><?= htmlspecialchars($d['typeObjet']); ?></td>
                            <td><?= htmlspecialchars($d['modele']); ?></td>
                            <td><?= htmlspecialchars($d['categorieProbleme']); ?></td>
                            <td><span class="priority-pill"><?= htmlspecialchars($d['urgence']); ?></span></td>
                            <td><a class="btn-cart-mini" href="index.php?action=front_show&id=<?= $d['idDiagnostic']; ?>">Voir</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<script>
const hamburger = document.querySelector('.hamburger');
const menu = document.querySelector('.navbar-menu');
if (hamburger && menu) hamburger.addEventListener('click', () => menu.classList.toggle('open'));
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>
