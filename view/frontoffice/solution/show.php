<?php
$pageTitle = "TechCare - Solutions";
$bodyClass = "front-body";

$solutions = $solutionController->getAllSolutions();

function solutionPhotoPath($photoFile)
{
    $photoFile = !empty($photoFile) ? trim($photoFile) : '';

    $paths = [
        [__DIR__ . '/../../../uploads/diagnostic/' . $photoFile, 'uploads/diagnostic/' . $photoFile],
        [__DIR__ . '/../../../assets/uploads/' . $photoFile, 'assets/uploads/' . $photoFile]
    ];

    foreach ($paths as $path) {
        if (!empty($photoFile) && file_exists($path[0])) {
            return $path[1];
        }
    }

    return 'assets/images/logo.png';
}

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
        <a class="nav-link active" href="index.php?action=solution_front_show">Solution</a>
        <a class="nav-link" href="#">Abonnement</a>
        <a class="nav-link" href="index.php?action=back_show">Administration</a>
    </div>
</nav>

<section class="hero">
    <div class="hero-content">
        <span class="hero-badge">Solutions intelligentes</span>
        <h1 class="hero-title">
            Trouvez la <span class="gradient-text">bonne solution</span>
        </h1>
        <p class="hero-subtitle">
            Consultez uniquement les solutions enregistrées et liées aux diagnostics des appareils.
        </p>
    </div>

    <div class="hero-visual">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <div class="hero-main-icon">💡</div>
    </div>
</section>

<section class="products-section" style="max-width:1200px; margin:0 auto;">
    <div class="metier-actions solution-clean-actions">
        <a class="btn-primary" href="index.php?action=solution_front_create">Créer une solution</a>
        <a class="btn-outline" href="index.php?action=solution_search">Recherche par diagnostic</a>
    </div>

    <div class="metier-title-row">
        <div>
            <span class="hero-badge">Liste</span>
            <h2 class="section-title">Solutions</h2>
        </div>
        <span class="count-badge"><?= count($solutions); ?></span>
    </div>

    <?php if (!empty($solutions)): ?>
        <div class="products-grid">
            <?php foreach ($solutions as $s): ?>
                <?php $finalPhoto = solutionPhotoPath($s['photo'] ?? ''); ?>
                <div class="product-card">
                    <div class="card-img-wrap">
                        <img src="<?= htmlspecialchars($finalPhoto); ?>" alt="Photo diagnostic lié">
                        <span class="card-cat-label"><?= htmlspecialchars($s['niveauPriorite'] ?? ''); ?></span>
                    </div>

                    <div class="card-body">
                        <div class="card-name">
                            <?= htmlspecialchars($s['typeObjet'] ?? ''); ?> - <?= htmlspecialchars($s['marque'] ?? ''); ?>
                        </div>

                        <div class="card-desc">
                            <?= htmlspecialchars($s['contenu'] ?? ''); ?>
                        </div>

                        <div style="display:flex; gap:8px; flex-wrap:wrap; margin:12px 0;">
                            <span class="btn-cart-mini">Efficacité : <?= htmlspecialchars($s['efficaciteEstimee'] ?? ''); ?></span>
                            <span class="btn-cart-mini">Type : <?= htmlspecialchars($s['typeSolution'] ?? ''); ?></span>
                        </div>

                        <div class="card-desc"><strong>Justification :</strong> <?= htmlspecialchars($s['justificationTechnique'] ?? ''); ?></div>
                        <div class="card-desc"><strong>Suggestion IA :</strong> <?= htmlspecialchars($s['suggestionIA'] ?? ''); ?></div>

                        <div class="card-footer">
                            <span class="card-price">Diagnostic #<?= htmlspecialchars($s['idDiagnostic'] ?? ''); ?></span>
                            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                <a class="btn-cart-mini" href="index.php?action=solution_front_edit&id=<?= urlencode($s['idSolution']); ?>">Edit</a>
                                <a class="btn-cart-mini" href="index.php?action=solution_front_delete&id=<?= urlencode($s['idSolution']); ?>">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i>💡</i>
            <p>Aucune solution disponible.</p>
            <button onclick="window.location.href='index.php?action=solution_front_create'">Créer une solution</button>
        </div>
    <?php endif; ?>
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
