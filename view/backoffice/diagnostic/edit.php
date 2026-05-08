<?php
$id = $_GET['id'] ?? null;
$diagnostic = $controller->getDiagnosticById($id);

if (!$diagnostic) {
    die("Diagnostic introuvable.");
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = $controller->updateDiagnostic($id, $_POST, $_FILES);

    if (empty($errors)) {
        header('Location: index.php?action=back_show');
        exit;
    }
}

$pageTitle = "Back Office - Modifier Diagnostic";
$bodyClass = "admin-body";
$typeObjets = ['Téléphone', 'PC', 'Ordinateur bureau', 'Machine à laver', 'Tablette', 'iPad', 'Smart watch', 'Imprimante', 'Autre'];

$data = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $diagnostic;

ob_start();
?>

<div class="admin-wrapper">
    <main class="admin-tab active-tab">
        <div class="admin-card form-card">
            <div class="card-head"><h3>Modifier un diagnostic</h3></div>

            <form method="POST" enctype="multipart/form-data" id="diagnosticFormBackEdit" novalidate>
                <div class="form-grid">
                    <div class="form-group"><label>Type Objet</label><select name="typeObjet" id="typeObjet">
                        <option value="">Choisir un type</option>
                        <?php foreach ($typeObjets as $type): ?>
                            <option value="<?= htmlspecialchars($type); ?>" <?= (($data['typeObjet'] ?? '') === $type) ? 'selected' : '' ?>><?= htmlspecialchars($type); ?></option>
                        <?php endforeach; ?>
                    </select><small class="err-msg <?= isset($errors['typeObjet']) ? 'show' : '' ?>"><?= $errors['typeObjet'] ?? '' ?></small></div>
                    <div class="form-group"><label>Marque</label><input type="text" name="marque" id="marque" value="<?= htmlspecialchars($data['marque']); ?>"><small class="err-msg <?= isset($errors['marque']) ? 'show' : '' ?>"><?= $errors['marque'] ?? '' ?></small></div>
                    <div class="form-group"><label>Modèle</label><input type="text" name="modele" id="modele" value="<?= htmlspecialchars($data['modele']); ?>"><small class="err-msg <?= isset($errors['modele']) ? 'show' : '' ?>"><?= $errors['modele'] ?? '' ?></small></div>
                    <div class="form-group"><label>Date Achat</label><input type="date" name="dateAchat" id="dateAchat" value="<?= $data['dateAchat']; ?>"><small class="err-msg <?= isset($errors['dateAchat']) ? 'show' : '' ?>"><?= $errors['dateAchat'] ?? '' ?></small></div>
                    <div class="form-group"><label>Statut Objet</label><input type="text" name="statutObjet" id="statutObjet" value="<?= htmlspecialchars($data['statutObjet']); ?>"><small class="err-msg <?= isset($errors['statutObjet']) ? 'show' : '' ?>"><?= $errors['statutObjet'] ?? '' ?></small></div>

                    <div class="form-group">
                        <label>Catégorie Problème</label>
                        <select name="categorieProbleme" id="categorieProbleme">
                            <option value="">Choisir</option>
                            <?php
                            $categories = ['Écran cassé', 'Batterie', 'Problème de charge', 'Appareil lent', 'Virus', 'Autre'];
                            foreach ($categories as $cat):
                            ?>
                                <option value="<?= $cat; ?>" <?= ($data['categorieProbleme'] === $cat) ? 'selected' : '' ?>><?= $cat; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="err-msg <?= isset($errors['categorieProbleme']) ? 'show' : '' ?>"><?= $errors['categorieProbleme'] ?? '' ?></small>
                    </div>

                    <div class="form-group span-2"><label>Description</label><textarea name="description" id="description"><?= htmlspecialchars($data['description']); ?></textarea><small class="err-msg <?= isset($errors['description']) ? 'show' : '' ?>"><?= $errors['description'] ?? '' ?></small></div>
                    <div class="form-group"><label>Urgence</label><input type="text" name="urgence" id="urgence" value="<?= htmlspecialchars($data['urgence']); ?>"><small class="err-msg <?= isset($errors['urgence']) ? 'show' : '' ?>"><?= $errors['urgence'] ?? '' ?></small></div>
                    <div class="form-group span-2"><label>Suggestion IA</label><textarea name="suggestionIA" id="suggestionIA"><?= htmlspecialchars($data['suggestionIA']); ?></textarea><small class="err-msg <?= isset($errors['suggestionIA']) ? 'show' : '' ?>"><?= $errors['suggestionIA'] ?? '' ?></small></div>
                    <div class="form-group span-2"><label>Nouvelle photo (optionnel)</label><input type="file" name="photo" id="photo" accept=".jpg,.jpeg,.png,.webp"><small class="err-msg <?= isset($errors['photo']) ? 'show' : '' ?>"><?= $errors['photo'] ?? '' ?></small></div>
                </div>

                <div class="form-actions">
                    <button class="btn-primary" type="submit">Modifier</button>
                    <a class="btn-outline" href="index.php?action=back_show">Annuler</a>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
(function () {
    const form = document.getElementById("diagnosticFormBackEdit");

    const fields = [
        {id: "typeObjet", msg: "Le type objet est obligatoire."},
        {id: "marque", msg: "La marque est obligatoire."},
        {id: "modele", msg: "Le modèle est obligatoire."},
        {id: "dateAchat", msg: "La date d'achat est obligatoire."},
        {id: "statutObjet", msg: "Le statut objet est obligatoire."},
        {id: "categorieProbleme", msg: "La catégorie est obligatoire."},
        {id: "description", msg: "La description est obligatoire."},
        {id: "urgence", msg: "L'urgence est obligatoire."},
        {id: "suggestionIA", msg: "La suggestion IA est obligatoire."}
    ];

    function showError(input, message) {
        const small = input.parentElement.querySelector(".err-msg");
        if (small) {
            small.textContent = message;
            small.classList.add("show");
        }
    }

    function clearError(input) {
        const small = input.parentElement.querySelector(".err-msg");
        if (small) {
            small.textContent = "";
            small.classList.remove("show");
        }
    }

    function validateDateAchat(input) {
        const value = input.value.trim();
        if (!value) {
            showError(input, "La date d'achat est obligatoire.");
            return false;
        }

        const today = new Date();
        today.setHours(0,0,0,0);

        const selectedDate = new Date(value);
        selectedDate.setHours(0,0,0,0);

        const minDate = new Date();
        minDate.setHours(0,0,0,0);
        minDate.setFullYear(minDate.getFullYear() - 7);

        if (selectedDate > today) {
            showError(input, "La date d'achat ne peut pas être dans le futur.");
            return false;
        }

        if (selectedDate < minDate) {
            showError(input, "La date d'achat ne peut pas dépasser 7 ans.");
            return false;
        }

        clearError(input);
        return true;
    }

    fields.forEach(field => {
        const input = document.getElementById(field.id);

        input.addEventListener("input", function () {
            if (input.id === "marque") {
                if (input.value.trim().length >= 2) {
                    clearError(input);
                }
            } else if (input.value.trim() !== "") {
                clearError(input);
            }
        });

        input.addEventListener("change", function () {
            if (input.id === "dateAchat") {
                validateDateAchat(input);
            } else if (input.value.trim() !== "") {
                clearError(input);
            }
        });
    });

    form.addEventListener("submit", function(e) {
        let valid = true;

        document.querySelectorAll(".err-msg").forEach(el => {
            el.classList.remove("show");
            el.textContent = "";
        });

        fields.forEach(field => {
            const input = document.getElementById(field.id);

            if (!input.value.trim()) {
                showError(input, field.msg);
                valid = false;
            }
        });

        const marque = document.getElementById("marque");
        if (marque.value.trim() && marque.value.trim().length < 2) {
            showError(marque, "La marque doit contenir au moins 2 lettres.");
            valid = false;
        }

        const dateAchat = document.getElementById("dateAchat");
        if (!validateDateAchat(dateAchat)) {
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
        }
    });
})();
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>