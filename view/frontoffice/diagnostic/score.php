<?php
$pageTitle = "TechCare - Calcul Intelligent";
$bodyClass = "front-body";

$diagnostics = $controller->getDiagnosticsWithIntelligentScore();

function scorePhotoPath($photoFile)
{
    $photoFile = !empty($photoFile) ? trim($photoFile) : '';
    $serverPhotoPath = __DIR__ . '/../../../uploads/diagnostic/' . $photoFile;
    $webPhotoPath = 'uploads/diagnostic/' . $photoFile;

    return (!empty($photoFile) && file_exists($serverPhotoPath)) ? $webPhotoPath : 'assets/images/logo.png';
}

function scoreColor($niveau)
{
    if ($niveau === 'Critique') return '#ff4d6d';
    if ($niveau === 'Important') return '#ffb703';
    return '#00e0a4';
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
        <span class="hero-badge">Métier 4</span>
        <h1 class="hero-title">Calcul <span class="gradient-text">intelligent</span></h1>
        <p class="hero-subtitle">
            Le système calcule automatiquement un score de priorité selon l'urgence, le type d'objet et la catégorie du problème.
        </p>
    </div>
    <div class="hero-visual"><div class="hero-main-icon">🧠</div></div>
</section>

<section class="products-section metier-page-wrap">
    <div class="metier-page-actions">
        <a class="btn-outline" href="index.php?action=front_show">← Retour aux diagnostics</a>
        <a class="metier-link-btn" href="index.php?action=diagnostic_stats"><span class="metier-icon">📊</span>Statistiques</a>
        <a class="metier-link-btn" href="index.php?action=diagnostic_filter"><span class="metier-icon">🔎</span>Filtrage intelligent</a>
        <a class="metier-link-btn" href="index.php?action=diagnostic_tri"><span class="metier-icon">↕</span>Tri</a>
        <a class="metier-link-btn active" href="index.php?action=diagnostic_score"><span class="metier-icon">🧠</span>Calcul intelligent</a>
        <a class="metier-link-btn" href="index.php?action=diagnostic_auto"><span class="metier-icon">🔁</span>Auto amélioration</a>
    </div>

    <div class="metier-title-row mt-6">
        <div>
            <span class="hero-badge">Résultat</span>
            <h2 class="section-title">Diagnostics classés par score</h2>
        </div>
        <span class="count-badge"><?= count($diagnostics); ?></span>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(260px,1fr)); gap:22px; margin-bottom:28px;">
        <div class="admin-card" style="padding:22px;">
            <h3 style="color:white;margin:0 0 10px;">Formule utilisée</h3>
            <p style="color:#8ea3c6;margin:0;line-height:1.7;">
                Score = urgence + catégorie du problème + type d'objet.<br>
                Plus le score est élevé, plus le diagnostic est prioritaire.
            </p>
        </div>
        <div class="admin-card" style="padding:22px;">
            <h3 style="color:white;margin:0 0 10px;">Niveaux</h3>
            <p style="color:#8ea3c6;margin:0;line-height:1.8;">
                <strong style="color:#ff4d6d;">Critique</strong> : score ≥ 75<br>
                <strong style="color:#ffb703;">Important</strong> : score ≥ 45<br>
                <strong style="color:#00e0a4;">Normal</strong> : score &lt; 45
            </p>
        </div>
    </div>

    <?php if (!empty($diagnostics)): ?>
        <div class="products-grid">
            <?php foreach ($diagnostics as $d): ?>
                <?php
                $finalPhoto = scorePhotoPath($d['photo'] ?? '');
                $niveau = $d['niveauRisque'];
                $color = scoreColor($niveau);
                ?>

                <div class="product-card" style="border-color:<?= htmlspecialchars($color); ?>55;">
                    <div class="card-img-wrap">
                        <img src="<?= htmlspecialchars($finalPhoto); ?>" alt="Photo diagnostic">
                        <span class="card-cat-label" style="background:<?= htmlspecialchars($color); ?>;color:#06101f;">
                            <?= htmlspecialchars($niveau); ?>
                        </span>
                    </div>

                    <div class="card-body">
                        <div class="card-name">
                            <?= htmlspecialchars($d['typeObjet']); ?> - <?= htmlspecialchars($d['marque']); ?>
                        </div>

                        <div style="margin:14px 0;">
                            <div style="display:flex;justify-content:space-between;color:white;font-weight:700;margin-bottom:8px;">
                                <span>Score intelligent</span>
                                <span><?= htmlspecialchars($d['scoreIntelligent']); ?>/100</span>
                            </div>
                            <div style="height:12px;border-radius:999px;background:#1b2a40;overflow:hidden;">
                                <div style="height:100%;width:<?= (int) $d['scoreIntelligent']; ?>%;background:<?= htmlspecialchars($color); ?>;border-radius:999px;"></div>
                            </div>
                        </div>

                        <div class="card-desc">
                            <?= htmlspecialchars($d['recommandationScore']); ?>
                        </div>

                        <div class="card-footer">
                            <span class="card-price"><?= htmlspecialchars($d['urgence']); ?></span>
                            <a href="index.php?action=front_show&id=<?= htmlspecialchars($d['idDiagnostic']); ?>" class="btn-cart-mini">Voir</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state"><i>🧠</i><p>Aucun diagnostic disponible.</p></div>
    <?php endif; ?>
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
