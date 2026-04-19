<?php include __DIR__ . '/../layout/header.php'; ?>
<?php include __DIR__ . '/../layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <div class="page-title">
            <h1><i class="fa-solid fa-plus-circle"></i> Ajouter un Type d'Abonnement</h1>
            <p>Créer un nouveau type d'abonnement</p>
        </div>
        <div class="user-info">
            <div class="user-avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <span>Admin</span>
        </div>
    </div>

    <div class="form-container">
        <form action="index.php?action=types-ajouter" method="POST" id="typeForm">
            <div class="form-grid">
                <div class="form-group">
                    <label for="nom_type"><i class="fa-solid fa-tag"></i> Nom du type *</label>
                    <input type="text" id="nom_type" name="nom_type" required
                           placeholder="Ex: mensuel, annuel, premium..."
                           value="<?= htmlspecialchars($_POST['nom_type'] ?? '') ?>">
                    <small class="form-help">Nom unique pour identifier ce type d'abonnement</small>
                </div>

                <div class="form-group">
                    <label for="prix_base"><i class="fa-solid fa-euro-sign"></i> Prix de base (DT) *</label>
                    <input type="number" id="prix_base" name="prix_base" step="0.01" min="0" required
                           placeholder="0.00"
                           value="<?= htmlspecialchars($_POST['prix_base'] ?? '') ?>">
                    <small class="form-help">Prix de base en dinars tunisiens</small>
                </div>

                <div class="form-group">
                    <label for="duree_jours"><i class="fa-solid fa-calendar"></i> Durée (jours) *</label>
                    <input type="number" id="duree_jours" name="duree_jours" min="1" required
                           placeholder="30"
                           value="<?= htmlspecialchars($_POST['duree_jours'] ?? '') ?>">
                    <small class="form-help">Durée de validité de l'abonnement en jours</small>
                </div>

                <div class="form-group full-width">
                    <label for="description"><i class="fa-solid fa-align-left"></i> Description</label>
                    <textarea id="description" name="description" rows="4"
                              placeholder="Description détaillée du type d'abonnement..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    <small class="form-help">Description optionnelle pour expliquer les avantages de ce type</small>
                </div>
            </div>

            <?php if (isset($erreur)): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-exclamation-triangle"></i>
                    <?= htmlspecialchars($erreur) ?>
                </div>
            <?php endif; ?>

            <div class="form-actions">
                <a href="index.php?action=types" class="btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-save"></i> Créer le type
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Validation du formulaire
document.getElementById('typeForm').addEventListener('submit', function(e) {
    const nomType = document.getElementById('nom_type').value.trim();
    const prixBase = parseFloat(document.getElementById('prix_base').value);
    const dureeJours = parseInt(document.getElementById('duree_jours').value);

    if (!nomType) {
        e.preventDefault();
        showToast('Le nom du type est obligatoire', 'error');
        return;
    }

    if (nomType.length < 2) {
        e.preventDefault();
        showToast('Le nom du type doit contenir au moins 2 caractères', 'error');
        return;
    }

    if (isNaN(prixBase) || prixBase < 0) {
        e.preventDefault();
        showToast('Le prix de base doit être un nombre positif', 'error');
        return;
    }

    if (isNaN(dureeJours) || dureeJours < 1) {
        e.preventDefault();
        showToast('La durée doit être d\'au moins 1 jour', 'error');
        return;
    }
});

// Calcul automatique de la date de fin basé sur la durée
document.getElementById('duree_jours').addEventListener('input', function() {
    const jours = parseInt(this.value);
    if (!isNaN(jours) && jours > 0) {
        const dateDebut = new Date();
        const dateFin = new Date(dateDebut);
        dateFin.setDate(dateDebut.getDate() + jours);

        // Mettre à jour un champ d'aperçu si nécessaire
        console.log('Date de fin estimée:', dateFin.toLocaleDateString('fr-FR'));
    }
});
</script>

<style>
.form-container {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    margin-top: 20px;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 30px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group label {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-group input,
.form-group textarea {
    padding: 12px 16px;
    border: 2px solid #e1e5e9;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--green);
    box-shadow: 0 0 0 3px rgba(0, 168, 107, 0.1);
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.form-help {
    color: #666;
    font-size: 12px;
    margin-top: 4px;
}

.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 20px;
    border-top: 1px solid #e1e5e9;
}

.btn-primary, .btn-secondary {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-primary {
    background: var(--green);
    color: white;
}

.btn-primary:hover {
    background: var(--green-dark);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 168, 107, 0.3);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
    transform: translateY(-2px);
}

.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-error {
    background: #fee;
    color: #c33;
    border: 1px solid #fcc;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
        gap: 10px;
    }

    .btn-primary, .btn-secondary {
        width: 100%;
        justify-content: center;
    }
}
</style>

<?php include __DIR__ . '/../layout/footer.php'; ?>