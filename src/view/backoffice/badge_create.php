<div class="products-section">
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-plus-circle" style="color: var(--green);"></i> Ajouter un badge</h3>
        </div>
        <div class="admin-content">
            <?php if (isset($_SESSION['errors'])): ?>
                <div class="alert-error" style="background: #ef444420; border: 1px solid #ef4444; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <strong>❌ Erreurs :</strong>
                    <ul>
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['ia_status'])): ?>
                <div class="alert-info" style="background: #3b82f620; border: 1px solid #3b82f6; padding: 15px; border-radius: 8px; margin-bottom: 20px; color: #3b82f6;">
                    <i class="fas fa-robot"></i> <?= $_SESSION['ia_status'] ?>
                </div>
                <?php unset($_SESSION['ia_status']); ?>
            <?php endif; ?>
            
            <form method="POST" action="index.php?controller=badge&action=store" id="badgeForm" enctype="multipart/form-data">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label for="nom"><i class="fas fa-tag"></i> Nom du badge *</label>
                        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($_SESSION['old']['nom'] ?? '') ?>" 
                               placeholder="Ex: Éco-Guerrier" required>
                        <small id="nomError" class="error-message" style="display: none;"></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="icon"><i class="fas fa-smile"></i> Icône (émoji)</label>
                        <input type="text" id="icon" name="icon" value="<?= htmlspecialchars($_SESSION['old']['icon'] ?? '🏅') ?>" 
                               placeholder="Ex: 🐺">
                        <small>Laissez vide pour utiliser 🏅 par défaut</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="seuilPoints"><i class="fas fa-star"></i> Seuil de points *</label>
                        <input type="number" id="seuilPoints" name="seuilPoints" value="<?= $_SESSION['old']['seuilPoints'] ?? '' ?>" 
                               placeholder="Ex: 1000" min="1" required>
                        <small id="seuilError" class="error-message" style="display: none;"></small>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-robot"></i> Intelligence Artificielle</label>
                        <div style="background: #1a2535; padding: 1rem; border-radius: 8px;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" name="generate_ia" id="generate_ia" value="1" checked>
                                <span>🤖 Générer une image avec IA (Hugging Face)</span>
                            </label>
                            <small style="color: #8b9ab5; display: block; margin-top: 0.5rem;">
                                L'IA va créer une image unique basée sur la description du badge.
                                Utilise le modèle FLUX.1-dev de Black Forest Labs.
                            </small>
                        </div>
                    </div>
                    
                    <div class="form-group" style="grid-column: span 2;">
                        <label for="description"><i class="fas fa-align-left"></i> Description *</label>
                        <textarea id="description" name="description" rows="4" 
                                  placeholder="Décrivez ce badge (plus la description est précise, meilleure sera l'image IA)..."
                                  required><?= htmlspecialchars($_SESSION['old']['description'] ?? '') ?></textarea>
                        <small id="descError" class="error-message" style="display: none;"></small>
                        <small id="descCount" style="display: block; margin-top: 5px; font-size: 0.75rem; color: #8b9ab5;">0 / 200 caractères</small>
                    </div>
                </div>
                
                <div id="iaPreview" style="margin-top: 1.5rem; padding: 1rem; background: #0f172a; border-radius: 12px; display: none;">
                    <h4 style="color: #00A86B; margin-bottom: 0.5rem;"><i class="fas fa-image"></i> Aperçu de l'image IA</h4>
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <div id="iaLoading" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i> Génération en cours...
                        </div>
                        <img id="iaPreviewImage" src="" alt="Aperçu IA" style="max-width: 150px; border-radius: 12px; display: none;">
                    </div>
                </div>
                
                <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="submit" class="btn-primary">💾 Enregistrer le badge</button>
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
const descCount = document.getElementById('descCount');
const generateIaCheckbox = document.getElementById('generate_ia');
const iaPreview = document.getElementById('iaPreview');
const iaPreviewImage = document.getElementById('iaPreviewImage');
const iaLoading = document.getElementById('iaLoading');

function validateNom() {
    const value = nomInput.value.trim();
    const error = document.getElementById('nomError');
    error.style.display = 'none';
    nomInput.classList.remove('error-border');
    
    if (value.length === 0) {
        error.textContent = "Le nom du badge est obligatoire";
        error.style.display = 'block';
        nomInput.classList.add('error-border');
        return false;
    } else if (value.length < 3) {
        error.textContent = "Le nom doit contenir au moins 3 caractères";
        error.style.display = 'block';
        nomInput.classList.add('error-border');
        return false;
    } else if (value.length > 50) {
        error.textContent = "Le nom ne doit pas dépasser 50 caractères";
        error.style.display = 'block';
        nomInput.classList.add('error-border');
        return false;
    }
    return true;
}

function validateDescription() {
    const value = descInput.value.trim();
    const length = value.length;
    descCount.textContent = length + ' / 200 caractères';
    const error = document.getElementById('descError');
    error.style.display = 'none';
    descInput.classList.remove('error-border');
    
    if (length === 0) {
        error.textContent = "La description est obligatoire";
        error.style.display = 'block';
        descInput.classList.add('error-border');
        return false;
    } else if (length < 10) {
        error.textContent = "La description doit contenir au moins 10 caractères";
        error.style.display = 'block';
        descInput.classList.add('error-border');
        return false;
    } else if (length > 200) {
        error.textContent = "La description ne doit pas dépasser 200 caractères";
        error.style.display = 'block';
        descInput.classList.add('error-border');
        return false;
    }
    return true;
}

function validateSeuil() {
    const value = seuilInput.value;
    const error = document.getElementById('seuilError');
    error.style.display = 'none';
    seuilInput.classList.remove('error-border');
    
    if (value === '' || isNaN(value) || parseInt(value) <= 0) {
        error.textContent = "Le seuil doit être un nombre positif";
        error.style.display = 'block';
        seuilInput.classList.add('error-border');
        return false;
    } else if (parseInt(value) > 10000) {
        error.textContent = "Le seuil ne peut pas dépasser 10 000 points";
        error.style.display = 'block';
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

// Aperçu IA (optionnel - simule la génération)
generateIaCheckbox.addEventListener('change', function() {
    if (this.checked) {
        iaPreview.style.display = 'block';
    } else {
        iaPreview.style.display = 'none';
    }
});
</script>

<style>
.error-border { border-color: #ef4444 !important; }
.error-message { color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block; }
.form-group { margin-bottom: 1rem; }
.form-group label { display: block; margin-bottom: 0.5rem; color: #00A86B; font-weight: 500; }
.form-group input, .form-group textarea { width: 100%; padding: 0.75rem; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: white; font-family: inherit; }
.form-group input:focus, .form-group textarea:focus { outline: none; border-color: #00A86B; }
</style>