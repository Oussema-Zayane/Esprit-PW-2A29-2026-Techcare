<div class="products-section">
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-edit" style="color: var(--green);"></i> Modifier un badge</h3>
        </div>
        <div class="admin-content">
            <?php if (isset($_SESSION['errors'])): ?>
                <div class="alert-error" style="background: #ef444420; border: 1px solid #ef4444; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <strong>❌ Erreurs :</strong>
                    <ul><?php foreach ($_SESSION['errors'] as $error): ?><li><?= $error ?></li><?php endforeach; ?></ul>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>
            
            <form method="POST" action="index.php?controller=badge&action=update" id="badgeForm">
                <input type="hidden" name="id" value="<?= $badge['badge_id'] ?>">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label for="nom">Nom du badge *</label>
                        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($badge['nom']) ?>">
                        <small id="nomError" class="error-message" style="display: none;"></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="icon">Icône (émoji)</label>
                        <input type="text" id="icon" name="icon" value="<?= htmlspecialchars($badge['icon'] ?? '🏅') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="seuilPoints">Seuil de points *</label>
                        <input type="number" id="seuilPoints" name="seuilPoints" value="<?= $badge['seuilPoints'] ?>" min="1">
                        <small id="seuilError" class="error-message" style="display: none;"></small>
                    </div>
                    
                    <div class="form-group" style="grid-column: span 2;">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" rows="4"><?= htmlspecialchars($badge['description'] ?? '') ?></textarea>
                        <small id="descError" class="error-message" style="display: none;"></small>
                        <small id="descCount" style="display: block; margin-top: 5px; font-size: 0.75rem; color: #8b9ab5;"><?= strlen($badge['description'] ?? '') ?> / 200 caractères</small>
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
const nomInput = document.getElementById('nom');
const descInput = document.getElementById('description');
const seuilInput = document.getElementById('seuilPoints');
const descCount = document.getElementById('descCount');

function validateNom() {
    const value = nomInput.value.trim();
    const error = document.getElementById('nomError');
    error.style.display = 'none';
    nomInput.classList.remove('error-border');
    
    if (value.length === 0) {
        error.textContent = "Le nom est obligatoire";
        error.style.display = 'block';
        nomInput.classList.add('error-border');
        return false;
    } else if (value.length < 3) {
        error.textContent = "Le nom doit contenir au moins 3 caractères";
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
    }
    return true;
}

nomInput.addEventListener('input', validateNom);
descInput.addEventListener('input', validateDescription);
seuilInput.addEventListener('input', validateSeuil);

document.getElementById('badgeForm').addEventListener('submit', function(e) {
    if (!validateNom() || !validateDescription() || !validateSeuil()) {
        e.preventDefault();
        alert('Veuillez corriger les erreurs');
    }
});
</script>