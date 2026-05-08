<?php
$pageTitle = "TechCare - Historique Diagnostic";
$bodyClass = "front-body";

if (!$diagnostic) {
    header('Location: index.php?action=front_show');
    exit;
}

function historyDiagnosticPhotoPath($photoFile)
{
    $photoFile = !empty($photoFile) ? trim($photoFile) : '';
    $serverPhotoPath = __DIR__ . '/../../../uploads/diagnostic/' . $photoFile;
    $webPhotoPath = 'uploads/diagnostic/' . $photoFile;

    return (!empty($photoFile) && file_exists($serverPhotoPath)) ? $webPhotoPath : 'assets/images/logo.png';
}

function historyQrUrl($idDiagnostic)
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'), '/\\');
    $url = $scheme . '://' . $host . $basePath . '/index.php?action=diagnostic_history&id=' . urlencode($idDiagnostic);

    return 'https://api.qrserver.com/v1/create-qr-code/?size=170x170&data=' . urlencode($url);
}

$photo = historyDiagnosticPhotoPath($diagnostic['photo'] ?? '');
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
        <span class="hero-badge">QR Code de suivi</span>
        <h1 class="hero-title">
            Historique <span class="gradient-text">du diagnostic</span>
        </h1>
        <p class="hero-subtitle">
            Cette page est accessible directement par scan QR et regroupe les informations du diagnostic avec ses solutions liées.
        </p>
    </div>

    <div class="hero-visual">
        <div class="hero-main-icon">📱</div>
    </div>
</section>

<section class="products-section" style="max-width:1200px; margin:0 auto; padding:45px 20px;">
    <div class="metier-page-actions">
        <a class="btn-outline" href="index.php?action=front_show">← Retour aux diagnostics</a>
        <a class="btn-primary" href="index.php?action=front_show&id=<?= (int)$diagnostic['idDiagnostic']; ?>">Voir détail</a>
        <button class="btn-outline print-btn" onclick="window.print()">Enregistrer PDF</button>
    </div>

    <div class="history-hero-card">
        <div class="history-device-photo">
            <img src="<?= htmlspecialchars($photo); ?>" alt="Photo diagnostic">
        </div>

        <div class="history-info">
            <span class="hero-badge">Diagnostic #<?= (int)$diagnostic['idDiagnostic']; ?></span>
            <h2><?= htmlspecialchars($diagnostic['typeObjet']); ?> - <?= htmlspecialchars($diagnostic['marque']); ?></h2>
            <p><?= htmlspecialchars($diagnostic['description']); ?></p>

            <div class="history-meta-grid">
                <div><b>Modèle</b><span><?= htmlspecialchars($diagnostic['modele']); ?></span></div>
                <div><b>Date achat</b><span><?= htmlspecialchars($diagnostic['dateAchat']); ?></span></div>
                <div><b>Statut</b><span><?= htmlspecialchars($diagnostic['statutObjet']); ?></span></div>
                <div><b>Catégorie</b><span><?= htmlspecialchars($diagnostic['categorieProbleme']); ?></span></div>
                <div><b>Urgence</b><span><?= htmlspecialchars($diagnostic['urgence']); ?></span></div>
                <div><b>Solutions liées</b><span><?= count($solutions); ?></span></div>
            </div>
        </div>

        <div class="history-qr-panel">
            <img src="<?= htmlspecialchars(historyQrUrl($diagnostic['idDiagnostic'])); ?>" alt="QR code historique">
            <strong>QR de suivi</strong>
            <small>Scanner pour ouvrir cette page</small>
        </div>
    </div>

    <div class="admin-card mt-6">
        <div class="card-head">
            <h3>Suggestion IA du diagnostic</h3>
        </div>
        <p class="history-suggestion"><?= htmlspecialchars($diagnostic['suggestionIA'] ?? 'Aucune suggestion disponible.'); ?></p>
    </div>

    <div class="metier-title-row mt-6">
        <div>
            <span class="hero-badge">Historique solutions</span>
            <h2 class="section-title">Solutions associées</h2>
        </div>
        <span class="count-badge"><?= count($solutions); ?></span>
    </div>

    <?php if (!empty($solutions)): ?>
        <div class="solutions-history-grid">
            <?php foreach ($solutions as $s): ?>
                <div class="solution-history-card">
                    <div class="solution-history-head">
                        <span><?= htmlspecialchars($s['typeSolution'] ?? 'Solution'); ?></span>
                        <b><?= htmlspecialchars($s['niveauPriorite'] ?? ''); ?></b>
                    </div>
                    <h3><?= htmlspecialchars($s['contenu'] ?? ''); ?></h3>
                    <div class="solution-history-meta">
                        <span>Efficacité : <?= htmlspecialchars($s['efficaciteEstimee'] ?? ''); ?></span>
                        <span>ID solution : <?= (int)$s['idSolution']; ?></span>
                    </div>
                    <p><strong>Justification :</strong> <?= htmlspecialchars($s['justificationTechnique'] ?? ''); ?></p>
                    <p><strong>Suggestion IA :</strong> <?= htmlspecialchars($s['suggestionIA'] ?? ''); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i>🔧</i>
            <p>Aucune solution associée à ce diagnostic.</p>
            <button onclick="window.location.href='index.php?action=solution_front_create&idDiagnostic=<?= (int)$diagnostic['idDiagnostic']; ?>'">Créer une solution</button>
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
