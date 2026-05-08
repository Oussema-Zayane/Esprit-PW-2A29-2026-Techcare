<?php
$pageTitle = "TechCare - Filtrage Diagnostics";
$bodyClass = "front-body";

$keyword = '';
$typeObjetFilter = $_GET['typeObjet'] ?? '';
$categorieFilter = $_GET['categorieProbleme'] ?? '';
$urgenceFilter = $_GET['urgence'] ?? '';

$diagnostics = $controller->filterDiagnostics($keyword, $categorieFilter, $urgenceFilter, $typeObjetFilter);

$typeObjets = [
    'Téléphone',
    'PC',
    'Ordinateur bureau',
    'Machine à laver',
    'Tablette',
    'iPad',
    'Smart watch',
    'Imprimante',
    'Autre'
];

$categories = [
    'Écran cassé',
    'Batterie',
    'Problème de charge',
    'Appareil lent',
    'Virus',
    'Autre'
];

$urgences = [
    'faible' => 'Faible',
    'moyen' => 'Moyen',
    'eleve' => 'Élevé'
];

function diagnosticFilterPhotoPath($photoFile)
{
    $photoFile = !empty($photoFile) ? trim($photoFile) : '';
    $serverPhotoPath = __DIR__ . '/../../../uploads/diagnostic/' . $photoFile;
    $webPhotoPath = 'uploads/diagnostic/' . $photoFile;

    return (!empty($photoFile) && file_exists($serverPhotoPath)) ? $webPhotoPath : 'assets/images/logo.png';
}

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
        <span class="hero-badge">Métier 2</span>
        <h1 class="hero-title">
            Filtrage <span class="gradient-text">intelligent</span>
        </h1>
        <p class="hero-subtitle">
            Filtrer les diagnostics par type d’objet, catégorie ou urgence.
        </p>
    </div>

    <div class="hero-visual">
        <div class="hero-main-icon">🔎</div>
    </div>
</section>

<section class="products-section metier-page-wrap">
    <div class="metier-page-actions">
        <a class="btn-outline" href="index.php?action=front_show">← Retour aux diagnostics</a>

        <a class="metier-link-btn" href="index.php?action=diagnostic_stats">
            <span class="metier-icon">📊</span>Statistiques
        </a>

        <a class="metier-link-btn active" href="index.php?action=diagnostic_filter">
            <span class="metier-icon">🔎</span>Filtrage intelligent
        </a>

        <a class="metier-link-btn" href="index.php?action=diagnostic_tri">
            <span class="metier-icon">↕</span>Tri
        </a>
        <a class="metier-link-btn" href="index.php?action=diagnostic_score">
            <span class="metier-icon">🧠</span>Calcul intelligent
        </a>
        <a class="metier-link-btn" href="index.php?action=diagnostic_auto">
            <span class="metier-icon">🔁</span>Auto amélioration
        </a>
    </div>

    <div class="filter-beauty-card metier-page-card">
        <div class="card-head">
            <h3>Critères de filtrage</h3>
        </div>

        <form method="GET" class="filter-grid filter-grid-modal">
            <input type="hidden" name="action" value="diagnostic_filter">

            <div class="form-group">
                <label>Type Objet</label>
                <select name="typeObjet">
                    <option value="">Tous</option>
                    <?php foreach ($typeObjets as $type): ?>
                        <option value="<?= htmlspecialchars($type); ?>" <?= ($typeObjetFilter === $type) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($type); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Catégorie</label>
                <select name="categorieProbleme">
                    <option value="">Toutes</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat); ?>" <?= ($categorieFilter === $cat) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Urgence</label>
                <select name="urgence">
                    <option value="">Toutes</option>
                    <?php foreach ($urgences as $value => $label): ?>
                        <option value="<?= htmlspecialchars($value); ?>" <?= ($urgenceFilter === $value) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group filter-actions">
                <button class="btn-primary" type="submit">Filtrer</button>
                <a class="btn-outline" href="index.php?action=diagnostic_filter">Reset</a>
            </div>
        </form>
    </div>

    <div class="metier-title-row mt-6">
        <div>
            <span class="hero-badge">Résultat</span>
            <h2 class="section-title">Diagnostics filtrés</h2>
        </div>

        <span class="count-badge"><?= count($diagnostics); ?></span>
    </div>

    <?php if (!empty($diagnostics)): ?>
        <div class="products-grid">
            <?php foreach ($diagnostics as $d): ?>
                <?php $finalPhoto = diagnosticFilterPhotoPath($d['photo'] ?? ''); ?>

                <div class="product-card">
                    <div class="card-img-wrap">
                        <img src="<?= htmlspecialchars($finalPhoto); ?>" alt="Photo diagnostic">
                        <span class="card-cat-label"><?= htmlspecialchars($d['categorieProbleme']); ?></span>
                    </div>

                    <div class="card-body">
                        <div class="card-name">
                            <?= htmlspecialchars($d['typeObjet']); ?> - <?= htmlspecialchars($d['marque']); ?>
                        </div>

                        <div class="card-desc">
                            <?= htmlspecialchars($d['description']); ?>
                        </div>

                        <div class="card-footer">
                            <span class="card-price"><?= htmlspecialchars($d['urgence']); ?></span>

                            <a href="index.php?action=front_show&id=<?= htmlspecialchars($d['idDiagnostic']); ?>" class="btn-cart-mini">
                                Voir
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i>🔎</i>
            <p>Aucun diagnostic trouvé.</p>
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