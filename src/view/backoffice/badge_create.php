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
                        <label for="nom">Nom du badge *</label>
                        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($_SESSION['old']['nom'] ?? '') ?>" placeholder="Ex: Éco-Guerrier">
                        <small id="nomError" class="error-message" style="display: none;"></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="icon">Icône (émoji)</label>
                        <input type="text" id="icon" name="icon" value="<?= htmlspecialchars($_SESSION['old']['icon'] ?? '🏅') ?>" placeholder="Ex: 🐺">
                    </div>
                    
                    <div class="form-group">
                        <label for="seuilPoints">Seuil de points *</label>
                        <input type="number" id="seuilPoints" name="seuilPoints" value="<?= $_SESSION['old']['seuilPoints'] ?? '' ?>" placeholder="Ex: 1000" min="1">
                        <small id="seuilError" class="error-message" style="display: none;"></small>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-robot"></i> Intelligence Artificielle</label>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="generate_ia" value="1" checked>
                            <span>🤖 Générer une image avec IA</span>
                        </label>
                    </div>
                    
                    <div class="form-group" style="grid-column: span 2;">
                        <label><i class="fas fa-clock"></i> Badge limité dans le temps</label>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; margin-bottom: 1rem;">
                            <input type="checkbox" name="est_limite" id="est_limite" value="1" onchange="toggleDates()">
                            <span>🏷️ Badge disponible seulement pendant une période spécifique</span>
                        </label>
                        <div id="datesContainer" style="display: none; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div>
                                <label for="date_debut">Date de début</label>
                                <input type="date" id="date_debut" name="date_debut">
                            </div>
                            <div>
                                <label for="date_fin">Date de fin</label>
                                <input type="date" id="date_fin" name="date_fin">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group" style="grid-column: span 2;">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" rows="4" placeholder="Décrivez ce badge..."><?= htmlspecialchars($_SESSION['old']['description'] ?? '') ?></textarea>
                        <small id="descError" class="error-message" style="display: none;"></small>
                        <small id="descCount" style="display: block; margin-top: 5px; font-size: 0.75rem; color: #8b9ab5;">0 / 200 caractères</small>
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
function toggleDates() {
    const container = document.getElementById('datesContainer');
    const checkbox = document.getElementById('est_limite');
    container.style.display = checkbox.checked ? 'grid' : 'none';
}

// Contrôle de saisie
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
        error.textContent = "Le nom du badge est obligatoire";
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
        alert('Veuillez corriger les erreurs dans le formulaire');
    }
});
</script>

<style>
.error-border { border-color: #ef4444 !important; }
.error-message { color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block; }
</style>