<div class="products-section">
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-plus-circle" style="color: var(--green);"></i> Ajouter un badge</h3>
        </div>
        <div class="admin-content">
            <?php if (isset($_SESSION['errors'])): ?>
                <div class="alert-error" style="background: #ef444420; border: 1px solid #ef4444; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <strong>❌ Erreurs :</strong>
                    <ul><?php foreach ($_SESSION['errors'] as $error): ?><li><?= $error ?></li><?php endforeach; ?></ul>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>
            
            <form method="POST" action="index.php?controller=badge&action=store" id="badgeForm">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label for="nom"><i class="fas fa-tag"></i> Nom du badge *</label>
                        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($_SESSION['old']['nom'] ?? '') ?>" placeholder="Ex: Éco-Poussin">
                        <small id="nomError" class="error-message" style="display: none;"></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="icon"><i class="fas fa-smile"></i> Icône (émoji)</label>
                        <input type="text" id="icon" name="icon" value="<?= htmlspecialchars($_SESSION['old']['icon'] ?? '🏅') ?>" placeholder="Ex: 🐣">
                        <small>Laissez vide pour utiliser 🏅 par défaut</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="seuilPoints"><i class="fas fa-star"></i> Seuil de points *</label>
                        <input type="number" id="seuilPoints" name="seuilPoints" value="<?= $_SESSION['old']['seuilPoints'] ?? '' ?>" placeholder="Ex: 100" min="1">
                        <small id="seuilError" class="error-message" style="display: none;"></small>
                    </div>
                    
                    <div class="form-group" style="grid-column: span 2;">
                        <label for="description"><i class="fas fa-align-left"></i> Description *</label>
                        <textarea id="description" name="description" rows="4" placeholder="Décrivez ce badge..."><?= htmlspecialchars($_SESSION['old']['description'] ?? '') ?></textarea>
                        <small id="descError" class="error-message" style="display: none;"></small>
                        <small id="descCount" style="display: block; margin-top: 5px; font-size: 0.75rem; color: #8b9ab5;">0 / 200 caractères</small>
                    </div>
                </div>
                
                <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="submit" class="btn-primary">💾 Enregistrer</button>
                    <a href="index.php?controller=badge&action=index" class="btn-outline">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// =====================================================
// CONTROLE DE SAISIE (JavaScript pur)
// =====================================================
const nomInput = document.getElementById('nom');
const descInput = document.getElementById('description');
const seuilInput = document.getElementById('seuilPoints');
const iconInput = document.getElementById('icon');

const nomError = document.getElementById('nomError');
const descError = document.getElementById('descError');
const seuilError = document.getElementById('seuilError');
const descCount = document.getElementById('descCount');

function validateNom() {
    const value = nomInput.value.trim();
    nomError.style.display = 'none';
    nomInput.classList.remove('error-border');
    
    if (value.length === 0) {
        nomError.textContent = "Le nom du badge est obligatoire";
        nomError.style.display = 'block';
        nomInput.classList.add('error-border');
        return false;
    } else if (value.length < 3) {
        nomError.textContent = "Le nom doit contenir au moins 3 caractères";
        nomError.style.display = 'block';
        nomInput.classList.add('error-border');
        return false;
    } else if (value.length > 50) {
        nomError.textContent = "Le nom ne doit pas dépasser 50 caractères";
        nomError.style.display = 'block';
        nomInput.classList.add('error-border');
        return false;
    } else if (!/^[a-zA-Z0-9\s\-\'éèêëàâäôöùûüç]+$/.test(value)) {
        nomError.textContent = "Caractères non autorisés";
        nomError.style.display = 'block';
        nomInput.classList.add('error-border');
        return false;
    }
    return true;
}

function validateDescription() {
    const value = descInput.value.trim();
    const length = value.length;
    descCount.textContent = length + ' / 200 caractères';
    descError.style.display = 'none';
    descInput.classList.remove('error-border');
    
    if (length === 0) {
        descError.textContent = "La description est obligatoire";
        descError.style.display = 'block';
        descInput.classList.add('error-border');
        return false;
    } else if (length < 10) {
        descError.textContent = "La description doit contenir au moins 10 caractères";
        descError.style.display = 'block';
        descInput.classList.add('error-border');
        return false;
    } else if (length > 200) {
        descError.textContent = "La description ne doit pas dépasser 200 caractères";
        descError.style.display = 'block';
        descInput.classList.add('error-border');
        return false;
    }
    return true;
}

function validateSeuil() {
    const value = seuilInput.value;
    seuilError.style.display = 'none';
    seuilInput.classList.remove('error-border');
    
    if (value === '') {
        seuilError.textContent = "Le seuil de points est obligatoire";
        seuilError.style.display = 'block';
        seuilInput.classList.add('error-border');
        return false;
    } else if (isNaN(value) || parseInt(value) <= 0) {
        seuilError.textContent = "Le seuil doit être un nombre positif";
        seuilError.style.display = 'block';
        seuilInput.classList.add('error-border');
        return false;
    } else if (parseInt(value) > 10000) {
        seuilError.textContent = "Le seuil ne peut pas dépasser 10 000 points";
        seuilError.style.display = 'block';
        seuilInput.classList.add('error-border');
        return false;
    }
    return true;
}

// Événements en temps réel
nomInput.addEventListener('input', validateNom);
nomInput.addEventListener('blur', validateNom);
descInput.addEventListener('input', validateDescription);
descInput.addEventListener('blur', validateDescription);
seuilInput.addEventListener('input', validateSeuil);
seuilInput.addEventListener('blur', validateSeuil);

// Validation avant soumission
document.getElementById('badgeForm').addEventListener('submit', function(e) {
    const isNomValid = validateNom();
    const isDescValid = validateDescription();
    const isSeuilValid = validateSeuil();
    
    if (!isNomValid || !isDescValid || !isSeuilValid) {
        e.preventDefault();
        alert('Veuillez corriger les erreurs dans le formulaire');
    }
});
</script>

<style>
.error-border { border-color: #ef4444 !important; }
.error-message { color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block; }
.form-group { margin-bottom: 1rem; }
.form-group label { display: block; margin-bottom: 0.5rem; color: #00A86B; font-weight: 500; }
.form-group input, .form-group textarea { width: 100%; padding: 0.75rem; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: white; }
.form-group input:focus, .form-group textarea:focus { outline: none; border-color: #00A86B; }
</style>