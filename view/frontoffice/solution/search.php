<?php
$pageTitle = "Recherche des solutions par diagnostic";
$bodyClass = "front-body";

$diagnostics = $solutionController->getAllDiagnostics();
$list = [];
$selectedDiagnostic = $_POST['idDiagnostic'] ?? ($_GET['idDiagnostic'] ?? null);

if ($selectedDiagnostic !== null && $selectedDiagnostic !== '') {
    $list = $solutionController->getSolutionsByDiagnostic($selectedDiagnostic);
}

function searchSolutionPhotoPath($photoFile)
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

<section class="products-section">
    <div class="admin-card form-card">
        <div class="card-head">
            <h3>Recherche des solutions par diagnostic</h3>
        </div>

        <form method="POST" id="searchSolutionForm" novalidate>
            <div class="form-grid">
                <div class="form-group span-2">
                    <label for="idDiagnostic">Sélectionnez un diagnostic</label>
                    <select name="idDiagnostic" id="idDiagnostic">
                        <option value="">-- Choisir un diagnostic --</option>
                        <?php foreach ($diagnostics as $d): ?>
                            <option value="<?= $d['idDiagnostic']; ?>" <?= ($selectedDiagnostic == $d['idDiagnostic']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($d['typeObjet']); ?> - <?= htmlspecialchars($d['marque']); ?> - <?= htmlspecialchars($d['categorieProbleme']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="err-msg" id="err_diag"></small>
                </div>
            </div>

            <div class="form-actions">
                <button class="btn-primary" type="submit">Rechercher</button>
                <a class="btn-outline" href="index.php?action=solution_front_show">Voir toutes les solutions</a>
            </div>
        </form>
    </div>

    <?php if ($selectedDiagnostic !== null && $selectedDiagnostic !== ''): ?>
        <div style="margin-top:30px;">
            <div class="section-header">
                <h2 class="section-title">Solutions du diagnostic sélectionné</h2>
                <span class="count-badge"><?= count($list); ?></span>
            </div>

            <?php if (!empty($list)): ?>
                <div class="products-grid">
                    <?php foreach ($list as $s): ?>
                        <?php $finalPhoto = searchSolutionPhotoPath($s['photo'] ?? ''); ?>
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

                                <div class="card-desc">
                                    <strong>Justification :</strong> <?= htmlspecialchars($s['justificationTechnique'] ?? ''); ?>
                                </div>

                                <div class="card-desc">
                                    <strong>Suggestion IA :</strong> <?= htmlspecialchars($s['suggestionIA'] ?? ''); ?>
                                </div>

                                <div class="card-footer">
                                    <span class="card-price">Solution #<?= htmlspecialchars($s['idSolution'] ?? ''); ?></span>
                                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                        <a class="btn-cart-mini" href="index.php?action=solution_front_edit&id=<?= $s['idSolution']; ?>">Edit</a>
                                        <a class="btn-cart-mini" href="index.php?action=solution_front_delete&id=<?= $s['idSolution']; ?>">Delete</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="table-empty">
                    <i>📭</i>
                    <p>Aucune solution trouvée pour ce diagnostic.</p>
                    <a class="btn-primary" href="index.php?action=solution_front_create&idDiagnostic=<?= htmlspecialchars($selectedDiagnostic); ?>">Créer une solution pour ce diagnostic</a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>

<script>
const form = document.getElementById("searchSolutionForm");
const selectDiag = document.getElementById("idDiagnostic");
const errDiag = document.getElementById("err_diag");

form.addEventListener("submit", function(e) {
    errDiag.textContent = "";
    errDiag.classList.remove("show");

    if (selectDiag.value.trim() === "") {
        e.preventDefault();
        errDiag.textContent = "Veuillez choisir un diagnostic.";
        errDiag.classList.add("show");
    }
});

selectDiag.addEventListener("change", function() {
    if (selectDiag.value.trim() !== "") {
        errDiag.textContent = "";
        errDiag.classList.remove("show");
    }
});

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
