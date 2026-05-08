<?php
$pageTitle = "TechCare - Front Office";
$bodyClass = "front-body";

$keyword = $_GET['keyword'] ?? '';
$categorieFilter = $_GET['categorieProbleme'] ?? '';
$urgenceFilter = $_GET['urgence'] ?? '';
$ordreAlpha = $_GET['ordreAlpha'] ?? '';

$isFiltered = (!empty($keyword) || !empty($categorieFilter) || !empty($urgenceFilter) || !empty($ordreAlpha));
$diagnostics = $isFiltered
    ? $controller->filterDiagnostics($keyword, $categorieFilter, $urgenceFilter, $ordreAlpha)
    : $controller->getAllDiagnostics();

$allDiagnostics = $controller->getAllDiagnostics();
$statsDiagnostic = $controller->getDiagnosticStats();
$id = $_GET['id'] ?? null;
$selected = $id ? $controller->getDiagnosticById($id) : null;

function diagnosticPhotoPath($photoFile)
{
    $photoFile = !empty($photoFile) ? trim($photoFile) : '';
    $serverPhotoPath = __DIR__ . '/../../../uploads/diagnostic/' . $photoFile;
    $webPhotoPath = 'uploads/diagnostic/' . $photoFile;

    if (!empty($photoFile) && file_exists($serverPhotoPath)) {
        return $webPhotoPath;
    }

    return 'assets/images/logo.png';
}

function diagnosticSuggestionPreview($categorie, $urgence)
{
    $categorie = trim($categorie ?? '');
    $urgence = strtolower(trim($urgence ?? ''));

    switch ($categorie) {
        case 'Écran cassé':
            $suggestion = "Vérifier l'écran et prévoir un remplacement si l'affichage ou le tactile est endommagé.";
            break;
        case 'Batterie':
            $suggestion = "Contrôler la batterie, le chargeur et prévoir un remplacement si l'autonomie est faible.";
            break;
        case 'Problème de charge':
            $suggestion = "Nettoyer le port de charge, tester un autre câble et vérifier le connecteur.";
            break;
        case 'Appareil lent':
            $suggestion = "Libérer le stockage, fermer les applications inutiles et faire une mise à jour système.";
            break;
        case 'Virus':
            $suggestion = "Lancer une analyse antivirus, supprimer les applications suspectes et changer les mots de passe.";
            break;
        default:
            $suggestion = "Effectuer un diagnostic général et vérifier les composants principaux de l'appareil.";
            break;
    }

    if ($urgence === 'élevé' || $urgence === 'eleve' || $urgence === 'urgent') {
        $suggestion .= " Intervention rapide recommandée.";
    }

    return $suggestion;
}


function diagnosticQrUrl($idDiagnostic)
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'), '/\\');
    $url = $scheme . '://' . $host . $basePath . '/index.php?action=diagnostic_history&id=' . urlencode($idDiagnostic);

    return 'https://api.qrserver.com/v1/create-qr-code/?size=130x130&data=' . urlencode($url);
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
        <a class="nav-link active" href="index.php?action=front_show">Diagnostic</a>
        <a class="nav-link" href="index.php?action=solution_front_show">Solution</a>
        <a class="nav-link" href="#">Abonnement</a>
        <a class="nav-link" href="index.php?action=back_show">Administration</a>
    </div>
</nav>

<section class="hero">
    <div class="hero-content">
        <span class="hero-badge">Nouveautés 2025</span>
        <h1 class="hero-title">
            Découvrez la <span class="gradient-text">Tech de Demain</span>
        </h1>
        <p class="hero-subtitle">
            Suivez vos diagnostics et ajoutez facilement les problèmes de vos appareils.
        </p>
    </div>

    <div class="hero-visual">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <div class="hero-main-icon">🛠️</div>
    </div>
</section>

<section class="products-section" style="max-width:1200px; margin:0 auto;">

    <div class="metier-toolbar">
        <a class="btn-primary" href="index.php?action=front_create">Créer un diagnostic</a>
        <a class="btn-outline" href="index.php?action=solution_front_show">Voir les solutions</a>
        <button type="button" class="metier-link-btn" data-theme-toggle>☀️ Mode blanc</button>
        <a class="metier-link-btn" href="index.php?action=diagnostic_stats">
            <span class="metier-icon">📊</span>
            <span>Statistiques</span>
        </a>
        <a class="metier-link-btn" href="index.php?action=diagnostic_filter">
            <span class="metier-icon">🔎</span>
            <span>Filtrage intelligent</span>
        </a>
        <a class="metier-link-btn" href="index.php?action=diagnostic_tri">
            <span class="metier-icon">↕</span>
            <span>Tri</span>
        </a>
        <a class="metier-link-btn" href="index.php?action=diagnostic_score">
            <span class="metier-icon">🧠</span>
            <span>Calcul intelligent</span>
        </a>
        <a class="metier-link-btn" href="index.php?action=diagnostic_auto">
            <span class="metier-icon">🔁</span>
            <span>Auto amélioration</span>
        </a>
    </div>

    <?php if ($isFiltered): ?>
        <div class="filter-result-banner">
            <div>
                <strong>Résultats du filtrage intelligent</strong>
                <p><?= count($diagnostics); ?> diagnostic(s) trouvé(s).</p>
            </div>
            <a class="btn-outline" href="index.php?action=front_show">Annuler le filtre</a>
        </div>
    <?php endif; ?>

    <div class="metier-title-row">
        <div>
            <span class="hero-badge">Liste</span>
            <h2 class="section-title">Diagnostics</h2>
        </div>
        <span class="count-badge"><?= count($diagnostics); ?></span>
    </div>

    <?php if (!empty($diagnostics)): ?>
        <div class="products-grid">
            <?php foreach ($diagnostics as $d): ?>
                <?php $finalPhoto = diagnosticPhotoPath($d['photo'] ?? ''); ?>
                <div class="product-card">
                    <div class="card-img-wrap">
                        <img src="<?= htmlspecialchars($finalPhoto); ?>" alt="Photo diagnostic">
                        <div class="card-overlay">
                            <button class="overlay-view-btn" onclick="window.location.href='index.php?action=front_show&id=<?= $d['idDiagnostic']; ?>'">
                                Voir
                            </button>
                        </div>
                        <span class="card-cat-label"><?= htmlspecialchars($d['categorieProbleme']); ?></span>
                    </div>

                    <div class="card-body">
                        <div class="card-name">
                            <?= htmlspecialchars($d['typeObjet']); ?> - <?= htmlspecialchars($d['marque']); ?>
                        </div>

                        <div class="card-desc">
                            <?= htmlspecialchars($d['description']); ?>
                        </div>

                        <div class="qr-mini-box">
                            <img src="<?= htmlspecialchars(diagnosticQrUrl($d['idDiagnostic'])); ?>" alt="QR diagnostic">
                            <span>Scanner pour historique</span>
                        </div>

                        <div class="card-footer">
                            <span class="card-price"><?= htmlspecialchars($d['urgence']); ?></span>

                            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                <a href="index.php?action=front_show&id=<?= $d['idDiagnostic']; ?>" class="btn-cart-mini">Show</a>
                                <a href="index.php?action=diagnostic_history&id=<?= $d['idDiagnostic']; ?>" class="btn-cart-mini">Historique QR</a>
                                <a href="index.php?action=front_edit&id=<?= $d['idDiagnostic']; ?>" class="btn-cart-mini">Edit</a>
                                <a href="index.php?action=front_delete&id=<?= $d['idDiagnostic']; ?>" class="btn-cart-mini">Delete</a>
                                <a href="index.php?action=solution_front_create&idDiagnostic=<?= $d['idDiagnostic']; ?>" class="btn-cart-mini">Créer solution</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <i>📦</i>
            <p>Aucun diagnostic disponible.</p>
            <button onclick="window.location.href='index.php?action=front_create'">Créer un diagnostic</button>
        </div>
    <?php endif; ?>

    <?php if ($selected): ?>
        <?php $selectedFinalPhoto = diagnosticPhotoPath($selected['photo'] ?? ''); ?>

        <div class="admin-card form-card" style="margin-top:40px;">
            <div class="card-head">
                <h3>Détail du diagnostic</h3>
            </div>

            <div class="form-grid">
                <div class="form-group"><label>ID</label><input value="<?= $selected['idDiagnostic']; ?>" readonly></div>
                <div class="form-group"><label>Type Objet</label><input value="<?= htmlspecialchars($selected['typeObjet']); ?>" readonly></div>
                <div class="form-group"><label>Marque</label><input value="<?= htmlspecialchars($selected['marque']); ?>" readonly></div>
                <div class="form-group"><label>Modèle</label><input value="<?= htmlspecialchars($selected['modele']); ?>" readonly></div>
                <div class="form-group"><label>Date Achat</label><input value="<?= htmlspecialchars($selected['dateAchat']); ?>" readonly></div>
                <div class="form-group"><label>Statut Objet</label><input value="<?= htmlspecialchars($selected['statutObjet']); ?>" readonly></div>
                <div class="form-group"><label>Catégorie</label><input value="<?= htmlspecialchars($selected['categorieProbleme']); ?>" readonly></div>
                <div class="form-group"><label>Urgence</label><input value="<?= htmlspecialchars($selected['urgence']); ?>" readonly></div>
                <div class="form-group span-2"><label>Description</label><textarea readonly><?= htmlspecialchars($selected['description']); ?></textarea></div>
                <div class="form-group span-2"><label>Suggestion IA</label><textarea readonly><?= htmlspecialchars($selected['suggestionIA']); ?></textarea></div>
                <div class="form-group span-2">
                    <label>Photo</label>
                    <div style="width:220px;height:160px;border-radius:14px;overflow:hidden;border:1px solid rgba(255,255,255,0.07);background:#111827;">
                        <img src="<?= htmlspecialchars($selectedFinalPhoto); ?>" alt="Photo diagnostic" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                </div>
            </div>

            <div class="qr-detail-box">
                <div>
                    <h3>QR Code de suivi</h3>
                    <p>Scannez ce code pour ouvrir l'historique complet de ce diagnostic avec ses solutions liées.</p>
                    <a class="btn-primary" href="index.php?action=diagnostic_history&id=<?= $selected['idDiagnostic']; ?>">Ouvrir l'historique</a>
                </div>
                <img src="<?= htmlspecialchars(diagnosticQrUrl($selected['idDiagnostic'])); ?>" alt="QR diagnostic">
            </div>

            <div class="form-actions">
                <a class="btn-outline" href="index.php?action=front_show">Retour</a>
            </div>
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
