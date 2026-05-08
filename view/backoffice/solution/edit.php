<?php
$pageTitle = "Back Office - Modifier Solution";
$bodyClass = "admin-body";

$id = $_GET['id'] ?? null;
$solution = $solutionController->getSolutionById($id);

if (!$solution) {
    die("Solution introuvable.");
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = $solutionController->updateSolution($id, $_POST);

    if (empty($errors)) {
        header("Location: index.php?action=solution_back_show");
        exit;
    }
}

$diagnostics = $solutionController->getAllDiagnostics();
$data = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $solution;

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
                <h3>Modifier une solution</h3>
            </div>

            <form method="POST" id="solutionBackEditForm" novalidate>
                <div class="form-grid">
                    <div class="form-group span-2">
                        <label>Contenu</label>
                        <textarea name="contenu" id="contenu"><?= htmlspecialchars($data['contenu'] ?? ''); ?></textarea>
                        <small class="err-msg <?= isset($errors['contenu']) ? 'show' : '' ?>"><?= $errors['contenu'] ?? '' ?></small>
                    </div>

                    <div class="form-group">
                        <label>Niveau priorité</label>
                        <select name="niveauPriorite" id="niveauPriorite">
                            <option value="">-- Choisir --</option>
                            <option value="faible" <?= (($data['niveauPriorite'] ?? '') === 'faible') ? 'selected' : ''; ?>>faible → attendre</option>
                            <option value="moyen" <?= (($data['niveauPriorite'] ?? '') === 'moyen') ? 'selected' : ''; ?>>moyen → à faire bientôt</option>
                            <option value="élevé" <?= (($data['niveauPriorite'] ?? '') === 'élevé') ? 'selected' : ''; ?>>élevé → à faire rapidement</option>
                        </select>
                        <small class="err-msg <?= isset($errors['niveauPriorite']) ? 'show' : '' ?>"><?= $errors['niveauPriorite'] ?? '' ?></small>
                    </div>

                    <div class="form-group">
                        <label>Efficacité estimée</label>
                        <select name="efficaciteEstimee" id="efficaciteEstimee">
                            <option value="">-- Choisir --</option>
                            <option value="faible" <?= (($data['efficaciteEstimee'] ?? '') === 'faible') ? 'selected' : ''; ?>>faible</option>
                            <option value="moyenne" <?= (($data['efficaciteEstimee'] ?? '') === 'moyenne') ? 'selected' : ''; ?>>moyenne</option>
                            <option value="élevée" <?= (($data['efficaciteEstimee'] ?? '') === 'élevée') ? 'selected' : ''; ?>>élevée</option>
                        </select>
                        <small class="err-msg <?= isset($errors['efficaciteEstimee']) ? 'show' : '' ?>"><?= $errors['efficaciteEstimee'] ?? '' ?></small>
                    </div>

                    <div class="form-group">
                        <label>Type Solution</label>
                        <select name="typeSolution" id="typeSolution">
                            <option value="">-- Choisir --</option>
                            <option value="matériel" <?= (($data['typeSolution'] ?? '') === 'matériel') ? 'selected' : ''; ?>>matériel</option>
                            <option value="logiciel" <?= (($data['typeSolution'] ?? '') === 'logiciel') ? 'selected' : ''; ?>>logiciel</option>
                            <option value="maintenance" <?= (($data['typeSolution'] ?? '') === 'maintenance') ? 'selected' : ''; ?>>maintenance</option>
                            <option value="conseil" <?= (($data['typeSolution'] ?? '') === 'conseil') ? 'selected' : ''; ?>>conseil</option>
                        </select>
                        <small class="err-msg <?= isset($errors['typeSolution']) ? 'show' : '' ?>"><?= $errors['typeSolution'] ?? '' ?></small>
                    </div>

                    <div class="form-group span-2">
                        <label>Justification technique</label>
                        <textarea name="justificationTechnique" id="justificationTechnique"><?= htmlspecialchars($data['justificationTechnique'] ?? ''); ?></textarea>
                        <small class="err-msg <?= isset($errors['justificationTechnique']) ? 'show' : '' ?>"><?= $errors['justificationTechnique'] ?? '' ?></small>
                    </div>

                    <div class="form-group span-2">
                        <label>Suggestion IA</label>
                        <textarea name="suggestionIA" id="suggestionIA"><?= htmlspecialchars($data['suggestionIA'] ?? ''); ?></textarea>
                        <small class="err-msg <?= isset($errors['suggestionIA']) ? 'show' : '' ?>"><?= $errors['suggestionIA'] ?? '' ?></small>
                    </div>

                    <div class="form-group">
                        <label>Diagnostic associé</label>
                        <select name="idDiagnostic" id="idDiagnostic">
                            <option value="">-- Choisir --</option>
                            <?php foreach ($diagnostics as $d): ?>
                                <option value="<?= $d['idDiagnostic']; ?>" <?= (($data['idDiagnostic'] ?? '') == $d['idDiagnostic']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($d['typeObjet']) . ' - ' . htmlspecialchars($d['marque']) . ' - ' . htmlspecialchars($d['categorieProbleme']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="err-msg <?= isset($errors['idDiagnostic']) ? 'show' : '' ?>"><?= $errors['idDiagnostic'] ?? '' ?></small>
                    </div>
                </div>

                <div class="form-actions">
                    <button class="btn-primary" type="submit">Modifier</button>
                    <a class="btn-outline" href="index.php?action=solution_back_show">Annuler</a>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
(function () {
    const form = document.getElementById("solutionBackEditForm");
    const fields = [
        {id: "contenu", msg: "Le contenu est obligatoire."},
        {id: "niveauPriorite", msg: "Le niveau de priorité est obligatoire."},
        {id: "efficaciteEstimee", msg: "L'efficacité estimée est obligatoire."},
        {id: "typeSolution", msg: "Le type de solution est obligatoire."},
        {id: "justificationTechnique", msg: "La justification technique est obligatoire."},
                {id: "idDiagnostic", msg: "Le diagnostic est obligatoire."}
    ];
    function showError(input, message) {
        const small = input.parentElement.querySelector(".err-msg");
        if (small) { small.textContent = message; small.classList.add("show"); }
    }
    function clearError(input) {
        const small = input.parentElement.querySelector(".err-msg");
        if (small) { small.textContent = ""; small.classList.remove("show"); }
    }
    fields.forEach(field => {
        const input = document.getElementById(field.id);
        input.addEventListener("input", () => { if (input.value.trim() !== "") clearError(input); });
        input.addEventListener("change", () => { if (input.value.trim() !== "") clearError(input); });
    });
    form.addEventListener("submit", function(e) {
        let valid = true;
        document.querySelectorAll(".err-msg").forEach(el => { el.classList.remove("show"); el.textContent = ""; });
        fields.forEach(field => {
            const input = document.getElementById(field.id);
            if (!input.value.trim()) { showError(input, field.msg); valid = false; }
        });
        if (!valid) e.preventDefault();
    });
})();
</script>
<script>
(function () {
    const type = document.getElementById("typeSolution");
    const priorite = document.getElementById("niveauPriorite");
    const efficacite = document.getElementById("efficaciteEstimee");
    const suggestion = document.getElementById("suggestionIA");

    function buildSolutionSuggestion() {
        const t = type ? type.value : "";
        const p = priorite ? priorite.value : "";
        const e = efficacite ? efficacite.value : "";
        let text = "";
        if (t === "logiciel") text = "Suggestion IA : commencer par une vérification logicielle, mise à jour système et nettoyage des fichiers inutiles.";
        else if (t === "matériel") text = "Suggestion IA : contrôler le composant matériel concerné et remplacer la pièce si le défaut est confirmé.";
        else if (t === "maintenance") text = "Suggestion IA : effectuer une maintenance complète, nettoyage, test et optimisation de l’appareil.";
        else if (t === "conseil") text = "Suggestion IA : appliquer les conseils proposés et surveiller l’évolution du problème.";
        if (text && p === "élevé") text += " Priorité élevée : traiter cette solution rapidement.";
        if (text && e === "élevée") text += " Efficacité estimée élevée : solution fortement recommandée.";
        if (suggestion && text) suggestion.value = text;
    }
    if (type) type.addEventListener("change", buildSolutionSuggestion);
    if (priorite) priorite.addEventListener("change", buildSolutionSuggestion);
    if (efficacite) efficacite.addEventListener("change", buildSolutionSuggestion);
})();
</script>


<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>
