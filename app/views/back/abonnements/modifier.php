<?php include __DIR__ . '/../layout/header.php'; ?>
<?php include __DIR__ . '/../layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <div class="page-title">
            <h1><i class="fa-solid fa-pen"></i> Modifier l'abonnement</h1>
            <p>Modifier les informations de l'abonnement #<?= $abonnement['id'] ?></p>
        </div>
        <div class="user-info">
            <div class="user-avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <span>Admin</span>
        </div>
    </div>

    <!-- Formulaire style carte - SANS required HTML5 -->
    <div class="form-card">
        <div class="form-card-header">
            <i class="fa-solid fa-pen-to-square"></i>
            <h3>Formulaire de modification</h3>
            <p class="form-subtitle">Tous les champs marqués d'un * sont obligatoires</p>
        </div>

        <form method="POST" action="index.php?action=modifier&id=<?= $abonnement['id'] ?>" id="form-modifier" novalidate>
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fa-solid fa-user"></i> Nom du client <span class="required">*</span></label>
                    <input type="text" name="nom_client" id="nom_client" value="<?= htmlspecialchars($abonnement['nom_client']) ?>" placeholder="Jean Dupont">
                    <span class="error-msg" id="error-nom_client"></span>
                    <span class="valid-msg" id="valid-nom_client"><i class="fa-solid fa-check-circle"></i> Valide</span>
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-at"></i> Email</label>
                    <input type="text" name="email" id="email" value="<?= htmlspecialchars($abonnement['email'] ?? '') ?>" placeholder="client@example.com">
                    <span class="error-msg" id="error-email"></span>
                    <span class="valid-msg" id="valid-email"><i class="fa-solid fa-check-circle"></i> Valide</span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fa-solid fa-tag"></i> Type d'abonnement <span class="required">*</span></label>
                    <select name="type_abonnement" id="type_abonnement">
                        <option value="">-- Sélectionner --</option>
                        <?php if (!empty($types)): ?>
                            <?php foreach ($types as $type): ?>
                                <?php 
                                    $icon = '📦';
                                    if (strtolower($type['nom_type']) === 'mensuel') $icon = '📆';
                                    elseif (strtolower($type['nom_type']) === 'annuel') $icon = '📅';
                                    elseif (strtolower($type['nom_type']) === 'ponctuel') $icon = '🎟️';
                                ?>
                                <option value="<?= htmlspecialchars($type['nom_type']) ?>" 
                                        data-prix="<?= $type['prix_base'] ?>"
                                        data-duree="<?= $type['duree_jours'] ?>"
                                        <?= strtolower($abonnement['type_abonnement']) == strtolower($type['nom_type']) ? 'selected' : '' ?>>
                                    <?= $icon ?> <?= ucfirst($type['nom_type']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>Aucun type disponible</option>
                        <?php endif; ?>
                    </select>
                    <span class="error-msg" id="error-type_abonnement"></span>
                    <span class="valid-msg" id="valid-type_abonnement"><i class="fa-solid fa-check-circle"></i> Valide</span>
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-money-bill"></i> Prix (DT) <span class="required">*</span></label>
                    <input type="text" name="prix" id="prix" step="0.01" value="<?= $abonnement['prix'] ?>" placeholder="0.00">
                    <span class="error-msg" id="error-prix"></span>
                    <span class="valid-msg" id="valid-prix"><i class="fa-solid fa-check-circle"></i> Valide</span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fa-solid fa-calendar"></i> Date de début <span class="required">*</span></label>
                    <input type="date" name="date_debut" id="date_debut" value="<?= $abonnement['date_debut'] ?>">
                    <span class="error-msg" id="error-date_debut"></span>
                    <span class="valid-msg" id="valid-date_debut"><i class="fa-solid fa-check-circle"></i> Valide</span>
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-calendar-xmark"></i> Date de fin <span class="required">*</span></label>
                    <input type="date" name="date_fin" id="date_fin" value="<?= $abonnement['date_fin'] ?>">
                    <span class="error-msg" id="error-date_fin"></span>
                    <span class="valid-msg" id="valid-date_fin"><i class="fa-solid fa-check-circle"></i> Valide</span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fa-solid fa-circle"></i> Statut</label>
                    <select name="statut" id="statut">
                        <option value="actif" <?= $abonnement['statut'] == 'actif' ? 'selected' : '' ?>>✅ Actif</option>
                        <option value="suspendu" <?= $abonnement['statut'] == 'suspendu' ? 'selected' : '' ?>>⏸️ Suspendu</option>
                        <option value="expiré" <?= $abonnement['statut'] == 'expiré' ? 'selected' : '' ?>>❌ Expiré</option>
                        <option value="annulé" <?= $abonnement['statut'] == 'annulé' ? 'selected' : '' ?>>🚫 Annulé</option>
                    </select>
                    <span class="error-msg" id="error-statut"></span>
                </div>
                <div class="form-group">
                    <!-- Espace vide pour alignement -->
                </div>
            </div>

            <div class="form-actions">
                <a href="index.php?action=liste" class="btn-secondary">
                    <i class="fa-solid fa-times"></i> Annuler
                </a>
                <button type="submit" class="btn-primary" id="submit-btn">
                    <i class="fa-solid fa-save"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.form-card {
    background: var(--card);
    border-radius: 16px;
    border: 1px solid var(--border);
    overflow: hidden;
    margin-top: 20px;
}

.form-card-header {
    background: linear-gradient(135deg, rgba(0,168,107,0.1), rgba(0,168,107,0.05));
    padding: 18px 25px;
    border-bottom: 1px solid var(--border);
}

.form-card-header i {
    font-size: 1.3rem;
    color: var(--green);
    margin-right: 8px;
}

.form-card-header h3 {
    font-size: 1rem;
    color: var(--txt);
    margin: 0;
    display: inline-block;
}

.form-subtitle {
    font-size: 0.75rem;
    color: var(--txt-muted);
    margin-top: 8px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    padding: 20px 25px 0 25px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 5px;
    position: relative;
}

.form-group label {
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--txt-muted);
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-group label i {
    color: var(--green);
    font-size: 0.8rem;
}

.required {
    color: #e74c3c;
    font-size: 1rem;
    margin-left: 2px;
}

.form-group input,
.form-group select {
    background: var(--surface2);
    border: 2px solid var(--border);
    border-radius: 10px;
    padding: 12px 15px;
    color: var(--txt);
    font-size: 0.9rem;
    transition: all 0.3s;
    outline: none;
}

.form-group input:focus,
.form-group select:focus {
    border-color: var(--green);
    box-shadow: 0 0 0 3px rgba(0,168,107,0.1);
}

/* État valide */
.form-group input.valid, .form-group select.valid {
    border-color: #2ecc71;
    background-color: rgba(46,204,113,0.05);
    padding-right: 35px;
}

/* État erreur */
.form-group input.error, .form-group select.error {
    border-color: #e74c3c;
    background-color: rgba(231,76,60,0.05);
}

/* Messages d'erreur */
.error-msg {
    display: none;
    color: #e74c3c;
    font-size: 0.7rem;
    margin-top: 5px;
    padding: 5px 10px;
    background: rgba(231, 76, 60, 0.1);
    border-radius: 6px;
}

.error-msg.show {
    display: block;
    animation: fadeIn 0.2s ease;
}

/* Messages de validation */
.valid-msg {
    display: none;
    color: #2ecc71;
    font-size: 0.7rem;
    margin-top: 5px;
    padding: 5px 10px;
    background: rgba(46,204,113,0.1);
    border-radius: 6px;
    align-items: center;
    gap: 5px;
}

.valid-msg.show {
    display: flex;
    animation: fadeIn 0.2s ease;
}

.valid-msg i {
    font-size: 0.7rem;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Icône de validation à l'intérieur du champ */
.form-group {
    position: relative;
}

.form-group input.valid, .form-group select.valid {
    background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%232ecc71"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>');
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 18px;
}

.form-group input.error, .form-group select.error {
    background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="%23e74c3c"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/></svg>');
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 18px;
}

/* Boutons d'action */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    padding: 20px 25px 25px 25px;
    border-top: 1px solid var(--border);
    margin-top: 15px;
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, var(--green), var(--green-dk));
    color: white;
    padding: 10px 25px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.btn-primary:hover {
    transform: translateY(-2px);
    filter: brightness(1.05);
    box-shadow: 0 5px 15px rgba(0,168,107,0.3);
}

.btn-primary:active {
    transform: translateY(0);
    color: black;
}

.btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--surface2);
    color: var(--txt-muted);
    padding: 10px 25px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.85rem;
    transition: all 0.3s;
    border: 1px solid var(--border);
    cursor: pointer;
}

.btn-secondary:hover {
    background: var(--surface3);
    color: var(--txt);
}

/* Responsive */
@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
        gap: 15px;
        padding: 15px 20px 0 20px;
    }
    .form-actions {
        flex-direction: column-reverse;
        padding: 15px 20px 20px 20px;
    }
    .btn-primary, .btn-secondary {
        justify-content: center;
        width: 100%;
    }
    .form-card-header {
        padding: 15px 20px;
    }
}
</style>

<script>
// Validation en temps réel avec CSS uniquement
const nomInput = document.getElementById('nom_client');
const emailInput = document.getElementById('email');
const typeSelect = document.getElementById('type_abonnement');
const prixInput = document.getElementById('prix');
const dateDebutInput = document.getElementById('date_debut');
const dateFinInput = document.getElementById('date_fin');

// Fonction pour valider un champ
function validateField(input, errorId, validId, validationFn, errorMsg) {
    if (validationFn(input.value)) {
        input.classList.remove('error');
        input.classList.add('valid');
        if (errorId) document.getElementById(errorId).classList.remove('show');
        if (validId) document.getElementById(validId).classList.add('show');
        return true;
    } else {
        input.classList.remove('valid');
        input.classList.add('error');
        if (errorId) {
            document.getElementById(errorId).textContent = errorMsg;
            document.getElementById(errorId).classList.add('show');
        }
        if (validId) document.getElementById(validId).classList.remove('show');
        return false;
    }
}

// Validations spécifiques
function validateNom(value) {
    return value.trim().length >= 2;
}

function validateEmail(value) {
    if (value === '') return true; // Email optionnel
    return /^[^\s@]+@([^\s@.,]+\.)+[^\s@.,]{2,}$/.test(value);
}

function validateType(value) {
    return value !== '';
}

function validatePrix(value) {
    return value !== '' && parseFloat(value) > 0;
}

function validateDate(value) {
    return value !== '';
}

function validateDateFin(value) {
    if (!dateDebutInput.value) return true;
    return value !== '' && value >= dateDebutInput.value;
}

// Événements de validation en temps réel
nomInput.addEventListener('input', function() {
    validateField(nomInput, 'error-nom_client', 'valid-nom_client', validateNom, 'Le nom doit contenir au moins 2 caractères');
});

emailInput.addEventListener('input', function() {
    validateField(emailInput, 'error-email', 'valid-email', validateEmail, 'Email invalide (exemple@domaine.com)');
});

typeSelect.addEventListener('change', function() {
    validateField(typeSelect, 'error-type_abonnement', 'valid-type_abonnement', validateType, 'Veuillez sélectionner un type');
});

prixInput.addEventListener('input', function() {
    validateField(prixInput, 'error-prix', 'valid-prix', validatePrix, 'Le prix doit être supérieur à 0');
});

dateDebutInput.addEventListener('change', function() {
    validateField(dateDebutInput, 'error-date_debut', 'valid-date_debut', validateDate, 'La date de début est obligatoire');
    if (dateFinInput.value) {
        validateField(dateFinInput, 'error-date_fin', 'valid-date_fin', validateDateFin, 'La date de fin doit être après la date de début');
    }
});

dateFinInput.addEventListener('change', function() {
    validateField(dateFinInput, 'error-date_fin', 'valid-date_fin', validateDateFin, 'La date de fin doit être après la date de début');
});

// Validation avant soumission
document.getElementById('form-modifier').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const isNomValid = validateField(nomInput, 'error-nom_client', 'valid-nom_client', validateNom, 'Le nom doit contenir au moins 2 caractères');
    const isTypeValid = validateField(typeSelect, 'error-type_abonnement', 'valid-type_abonnement', validateType, 'Veuillez sélectionner un type');
    const isPrixValid = validateField(prixInput, 'error-prix', 'valid-prix', validatePrix, 'Le prix doit être supérieur à 0');
    const isDateDebutValid = validateField(dateDebutInput, 'error-date_debut', 'valid-date_debut', validateDate, 'La date de début est obligatoire');
    const isDateFinValid = validateField(dateFinInput, 'error-date_fin', 'valid-date_fin', validateDateFin, 'La date de fin doit être après la date de début');
    const isEmailValid = validateField(emailInput, 'error-email', 'valid-email', validateEmail, 'Email invalide');
    
    if (isNomValid && isTypeValid && isPrixValid && isDateDebutValid && isDateFinValid && isEmailValid) {
        this.submit();
    }
});

function calculerDateFin(dateDebut, dureeJours) {
    if (!dateDebut || !dureeJours) return '';
    const d = new Date(dateDebut);
    d.setDate(d.getDate() + parseInt(dureeJours));
    return d.toISOString().split('T')[0];
}

// Auto-calcul du prix selon le type
document.getElementById('type_abonnement').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const selectedType = selectedOption.value;
    const prixInput = document.getElementById('prix');
    
    if (selectedType && (!prixInput.value || prixInput.value == 0)) {
        const prixBase = selectedOption.getAttribute('data-prix');
        if (prixBase) {
            prixInput.value = parseFloat(prixBase).toFixed(2);
            validateField(prixInput, 'error-prix', 'valid-prix', validatePrix, 'Le prix doit être supérieur à 0');
        }
    }
});

// Auto-calcul date fin
document.getElementById('type_abonnement').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const selectedType = selectedOption.value;
    const dateDebut = document.getElementById('date_debut').value;
    const dateFin = document.getElementById('date_fin');
    
    if (dateDebut && selectedType) {
        const dureeJours = selectedOption.getAttribute('data-duree');
        if (dureeJours) {
            dateFin.value = calculerDateFin(dateDebut, dureeJours);
            validateField(dateFin, 'error-date_fin', 'valid-date_fin', validateDateFin, 'La date de fin doit être après la date de début');
        }
    }
});

document.getElementById('date_debut').addEventListener('change', function() {
    const typeSelect = document.getElementById('type_abonnement');
    const selectedOption = typeSelect.options[typeSelect.selectedIndex];
    const dateFin = document.getElementById('date_fin');
    
    if (this.value && selectedOption && selectedOption.value) {
        const dureeJours = selectedOption.getAttribute('data-duree');
        if (dureeJours) {
            dateFin.value = calculerDateFin(this.value, dureeJours);
            validateField(dateFin, 'error-date_fin', 'valid-date_fin', validateDateFin, 'La date de fin doit être après la date de début');
        }
    }
});

// Validation initiale au chargement
document.addEventListener('DOMContentLoaded', function() {
    validateField(nomInput, 'error-nom_client', 'valid-nom_client', validateNom, 'Le nom doit contenir au moins 2 caractères');
    validateField(emailInput, 'error-email', 'valid-email', validateEmail, 'Email invalide');
    validateField(typeSelect, 'error-type_abonnement', 'valid-type_abonnement', validateType, 'Veuillez sélectionner un type');
    validateField(prixInput, 'error-prix', 'valid-prix', validatePrix, 'Le prix doit être supérieur à 0');
    validateField(dateDebutInput, 'error-date_debut', 'valid-date_debut', validateDate, 'La date de début est obligatoire');
    validateField(dateFinInput, 'error-date_fin', 'valid-date_fin', validateDateFin, 'La date de fin doit être après la date de début');
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>