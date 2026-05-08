<?php
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = $controller->createDiagnostic($_POST, $_FILES);

    if (empty($errors)) {
        header('Location: index.php?action=front_show');
        exit;
    }
}

$pageTitle = "Front Office - Créer Diagnostic";
$bodyClass = "front-body";

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

ob_start();
?>

<nav class="navbar">
    <a href="#" class="navbar-brand">
        <span class="brand-accent">TechCare</span>
    </a>

    <div class="navbar-menu">
        <a class="nav-link" href="index.php?action=front_show">Diagnostics</a>
        <a class="nav-link active" href="index.php?action=front_create">Créer</a>
    </div>
</nav>

<section class="products-section">
    <div class="admin-card form-card">
        <div class="card-head">
            <h3>Créer un diagnostic</h3>
        </div>

        <form method="POST" enctype="multipart/form-data" id="diagnosticFormFrontCreate" novalidate>
            <div class="form-grid">

                <div class="form-group">
                    <label>Type Objet</label>
                    <select name="typeObjet" id="typeObjet">
                        <option value="">Choisir un type</option>
                        <?php foreach ($typeObjets as $type): ?>
                            <option value="<?= htmlspecialchars($type); ?>" <?= (($_POST['typeObjet'] ?? '') === $type) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($type); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="err-msg <?= isset($errors['typeObjet']) ? 'show' : ''; ?>">
                        <?= $errors['typeObjet'] ?? ''; ?>
                    </small>
                </div>

                <div class="form-group">
                    <label>Marque</label>
                    <input type="text" name="marque" id="marque" value="<?= htmlspecialchars($_POST['marque'] ?? ''); ?>">
                    <small class="err-msg <?= isset($errors['marque']) ? 'show' : ''; ?>">
                        <?= $errors['marque'] ?? ''; ?>
                    </small>
                </div>

                <div class="form-group">
                    <label>Modèle</label>
                    <input type="text" name="modele" id="modele" value="<?= htmlspecialchars($_POST['modele'] ?? ''); ?>">
                    <small class="err-msg <?= isset($errors['modele']) ? 'show' : ''; ?>">
                        <?= $errors['modele'] ?? ''; ?>
                    </small>
                </div>

                <div class="form-group">
                    <label>Date Achat</label>
                    <input type="date" name="dateAchat" id="dateAchat" value="<?= htmlspecialchars($_POST['dateAchat'] ?? ''); ?>">
                    <small class="err-msg <?= isset($errors['dateAchat']) ? 'show' : ''; ?>">
                        <?= $errors['dateAchat'] ?? ''; ?>
                    </small>
                </div>

                <div class="form-group">
                    <label>Statut Objet</label>
                    <input type="text" name="statutObjet" id="statutObjet" value="<?= htmlspecialchars($_POST['statutObjet'] ?? ''); ?>">
                    <small class="err-msg <?= isset($errors['statutObjet']) ? 'show' : ''; ?>">
                        <?= $errors['statutObjet'] ?? ''; ?>
                    </small>
                </div>

                <div class="form-group">
                    <label>Catégorie Problème</label>
                    <select name="categorieProbleme" id="categorieProbleme">
                        <option value="">Choisir</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat); ?>" <?= (($_POST['categorieProbleme'] ?? '') === $cat) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="err-msg <?= isset($errors['categorieProbleme']) ? 'show' : ''; ?>">
                        <?= $errors['categorieProbleme'] ?? ''; ?>
                    </small>
                </div>

                <div class="form-group span-2">
                    <label>Description</label>
                    <textarea name="description" id="description"><?= htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    <small class="err-msg <?= isset($errors['description']) ? 'show' : ''; ?>">
                        <?= $errors['description'] ?? ''; ?>
                    </small>
                </div>

                <div class="form-group">
                    <label>Urgence</label>
                    <select name="urgence" id="urgence">
                        <option value="">Choisir</option>
                        <?php foreach ($urgences as $value => $label): ?>
                            <option value="<?= htmlspecialchars($value); ?>" <?= (($_POST['urgence'] ?? '') === $value) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="err-msg <?= isset($errors['urgence']) ? 'show' : ''; ?>">
                        <?= $errors['urgence'] ?? ''; ?>
                    </small>
                </div>

                <div class="form-group span-2">
                    <label>Suggestion IA</label>
                    <textarea name="suggestionIA" id="suggestionIA"><?= htmlspecialchars($_POST['suggestionIA'] ?? ''); ?></textarea>
                    <small class="err-msg <?= isset($errors['suggestionIA']) ? 'show' : ''; ?>">
                        <?= $errors['suggestionIA'] ?? ''; ?>
                    </small>
                </div>

                <div class="form-group span-2">
                    <label>Photo de l'appareil</label>
                    <input type="file" name="photo" id="photo" accept=".jpg,.jpeg,.png,.webp">
                    <small class="err-msg <?= isset($errors['photo']) ? 'show' : ''; ?>">
                        <?= $errors['photo'] ?? ''; ?>
                    </small>
                </div>

            </div>

            <div class="form-actions">
                <button class="btn-primary" type="submit">Créer</button>
                <a class="btn-outline" href="index.php?action=front_show">Annuler</a>
            </div>
        </form>
    </div>
</section>

<script>
(function () {
    const form = document.getElementById("diagnosticFormFrontCreate");

    function showError(field, message) {
        const group = field.closest(".form-group");
        const error = group ? group.querySelector(".err-msg") : null;

        if (error) {
            error.textContent = message;
            error.classList.add("show");
        }

        field.classList.add("input-error");
    }

    function clearError(field) {
        const group = field.closest(".form-group");
        const error = group ? group.querySelector(".err-msg") : null;

        if (error) {
            error.textContent = "";
            error.classList.remove("show");
        }

        field.classList.remove("input-error");
    }

    function isEmpty(field) {
        return field.value.trim() === "";
    }

    function validateField(field) {
        clearError(field);

        if (field.id === "typeObjet" && isEmpty(field)) {
            showError(field, "Veuillez choisir un type d'objet.");
            return false;
        }

        if (field.id === "marque") {
            if (isEmpty(field)) {
                showError(field, "La marque est obligatoire.");
                return false;
            }

            if (field.value.trim().length < 2) {
                showError(field, "La marque doit contenir au moins 2 caractères.");
                return false;
            }
        }

        if (field.id === "modele" && isEmpty(field)) {
            showError(field, "Le modèle est obligatoire.");
            return false;
        }

        if (field.id === "dateAchat") {
            if (isEmpty(field)) {
                showError(field, "La date d'achat est obligatoire.");
                return false;
            }

            const dateAchat = new Date(field.value);
            const today = new Date();
            const minDate = new Date();

            today.setHours(0, 0, 0, 0);
            dateAchat.setHours(0, 0, 0, 0);
            minDate.setFullYear(today.getFullYear() - 7);
            minDate.setHours(0, 0, 0, 0);

            if (dateAchat > today) {
                showError(field, "La date d'achat ne peut pas être dans le futur.");
                return false;
            }

            if (dateAchat < minDate) {
                showError(field, "La date d'achat ne doit pas dépasser 7 ans.");
                return false;
            }
        }

        if (field.id === "statutObjet" && isEmpty(field)) {
            showError(field, "Le statut de l'objet est obligatoire.");
            return false;
        }

        if (field.id === "categorieProbleme" && isEmpty(field)) {
            showError(field, "Veuillez choisir une catégorie.");
            return false;
        }

        if (field.id === "description" && isEmpty(field)) {
            showError(field, "La description est obligatoire.");
            return false;
        }

        if (field.id === "urgence" && isEmpty(field)) {
            showError(field, "Veuillez choisir une urgence.");
            return false;
        }

        if (field.id === "suggestionIA" && isEmpty(field)) {
            showError(field, "La suggestion IA est obligatoire.");
            return false;
        }

        if (field.id === "photo") {
            if (!field.files || field.files.length === 0) {
                showError(field, "La photo est obligatoire.");
                return false;
            }

            const file = field.files[0];
            const allowedTypes = ["image/jpeg", "image/png", "image/webp"];

            if (!allowedTypes.includes(file.type)) {
                showError(field, "Format autorisé : JPG, PNG ou WEBP.");
                return false;
            }

            if (file.size > 2 * 1024 * 1024) {
                showError(field, "La photo ne doit pas dépasser 2MB.");
                return false;
            }
        }

        return true;
    }

    function buildSuggestion() {
        const categorie = document.getElementById("categorieProbleme");
        const urgence = document.getElementById("urgence");
        const suggestion = document.getElementById("suggestionIA");

        const cat = categorie ? categorie.value : "";
        const urg = urgence ? urgence.value : "";
        let text = "";

        if (cat === "Écran cassé") {
            text = "Suggestion IA : vérifier l’état de l’écran et prévoir un remplacement si l’affichage ou le tactile est endommagé.";
        } else if (cat === "Batterie") {
            text = "Suggestion IA : contrôler l’état de la batterie, le chargeur et envisager un remplacement si l’autonomie est faible.";
        } else if (cat === "Problème de charge") {
            text = "Suggestion IA : nettoyer le port de charge, tester un autre câble et vérifier le connecteur.";
        } else if (cat === "Appareil lent") {
            text = "Suggestion IA : libérer l’espace de stockage, fermer les applications inutiles et effectuer une mise à jour système.";
        } else if (cat === "Virus") {
            text = "Suggestion IA : lancer une analyse antivirus complète, supprimer les applications suspectes et changer les mots de passe.";
        } else if (cat === "Autre") {
            text = "Suggestion IA : effectuer un diagnostic général et vérifier les composants principaux de l’appareil.";
        }

        if (text && urg === "eleve") {
            text += " Priorité élevée : intervention rapide recommandée.";
        }

        if (suggestion && text) {
            suggestion.value = text;
            validateField(suggestion);
        }
    }

    const fields = form.querySelectorAll("input, select, textarea");

    fields.forEach(function (field) {
        field.addEventListener("input", function () {
            validateField(field);
        });

        field.addEventListener("change", function () {
            validateField(field);

            if (field.id === "categorieProbleme" || field.id === "urgence") {
                buildSuggestion();
            }
        });
    });

    form.addEventListener("submit", function (event) {
        let valid = true;

        fields.forEach(function (field) {
            if (!validateField(field)) {
                valid = false;
            }
        });

        if (!valid) {
            event.preventDefault();
        }
    });
})();
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>
