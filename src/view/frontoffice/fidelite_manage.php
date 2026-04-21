<div class="products-section">
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-edit" style="color: var(--green);"></i> Gérer ma fidélité</h3>
        </div>
        <div class="admin-content">
            
            <!-- CREATE - Formulaire d'ajout -->
            <div class="form-section" style="margin-bottom: 2rem; padding: 1.5rem; background: #1a2535; border-radius: 12px;">
                <h4 style="color: #00A86B; margin-bottom: 1rem;">➕ Nouvelle demande de fidélité</h4>
                <form method="POST" action="index.php?controller=fidelite&action=storeFront" id="fideliteFrontForm">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="titreFront">Titre *</label>
                            <input type="text" id="titreFront" name="titre" required>
                            <small id="titreFrontError" class="error-message" style="display: none;"></small>
                        </div>
                        <div class="form-group">
                            <label for="totalFront">Points *</label>
                            <input type="number" id="totalFront" name="total" min="0" required>
                            <small id="totalFrontError" class="error-message" style="display: none;"></small>
                        </div>
                        <div class="form-group" style="grid-column: span 2;">
                            <label for="descFront">Description *</label>
                            <textarea id="descFront" name="description" rows="3" required></textarea>
                            <small id="descFrontError" class="error-message" style="display: none;"></small>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary" style="margin-top: 1rem;">➕ Ajouter</button>
                </form>
            </div>
            
            <!-- READ - Liste des fidélités -->
            <h4 style="color: #00A86B; margin-bottom: 1rem;">📋 Mes demandes de fidélité</h4>
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr><th>Titre</th><th>Description</th><th>Points</th><th>Date</th><th>Actions</th></tr>
                    </thead>
                    <tbody id="fideliteListBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// =====================================================
// CONTROLE DE SAISIE (JavaScript pur - pas HTML5)
// =====================================================
const titreFront = document.getElementById('titreFront');
const descFront = document.getElementById('descFront');
const totalFront = document.getElementById('totalFront');
const titreFrontError = document.getElementById('titreFrontError');
const descFrontError = document.getElementById('descFrontError');
const totalFrontError = document.getElementById('totalFrontError');

function validateTitre() {
    const value = titreFront.value.trim();
    if (value.length === 0) {
        titreFrontError.textContent = "Le titre est obligatoire";
        titreFrontError.style.display = 'block';
        titreFront.classList.add('error-border');
        return false;
    } else if (value.length < 3) {
        titreFrontError.textContent = "Le titre doit contenir au moins 3 caractères";
        titreFrontError.style.display = 'block';
        titreFront.classList.add('error-border');
        return false;
    } else {
        titreFrontError.style.display = 'none';
        titreFront.classList.remove('error-border');
        return true;
    }
}

function validateDescription() {
    const value = descFront.value.trim();
    if (value.length === 0) {
        descFrontError.textContent = "La description est obligatoire";
        descFrontError.style.display = 'block';
        descFront.classList.add('error-border');
        return false;
    } else if (value.length < 10) {
        descFrontError.textContent = "La description doit contenir au moins 10 caractères";
        descFrontError.style.display = 'block';
        descFront.classList.add('error-border');
        return false;
    } else {
        descFrontError.style.display = 'none';
        descFront.classList.remove('error-border');
        return true;
    }
}

function validateTotal() {
    const value = totalFront.value;
    if (value === '') {
        totalFrontError.textContent = "Le nombre de points est obligatoire";
        totalFrontError.style.display = 'block';
        totalFront.classList.add('error-border');
        return false;
    } else if (isNaN(value) || parseInt(value) < 0) {
        totalFrontError.textContent = "Le nombre de points doit être un nombre positif";
        totalFrontError.style.display = 'block';
        totalFront.classList.add('error-border');
        return false;
    } else {
        totalFrontError.style.display = 'none';
        totalFront.classList.remove('error-border');
        return true;
    }
}

titreFront.addEventListener('input', validateTitre);
titreFront.addEventListener('blur', validateTitre);
descFront.addEventListener('input', validateDescription);
descFront.addEventListener('blur', validateDescription);
totalFront.addEventListener('input', validateTotal);
totalFront.addEventListener('blur', validateTotal);

document.getElementById('fideliteFrontForm').addEventListener('submit', function(e) {
    const isTitreValid = validateTitre();
    const isDescValid = validateDescription();
    const isTotalValid = validateTotal();
    
    if (!isTitreValid || !isDescValid || !isTotalValid) {
        e.preventDefault();
        alert('Veuillez corriger les erreurs dans le formulaire');
    }
});

// Charger la liste des fidélités
function loadFideliteList() {
    fetch('index.php?controller=fidelite&action=getUserFidelites')
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('fideliteListBody');
            tbody.innerHTML = '';
            if (data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Aucune demande de fidélité</td></tr>';
            } else {
                data.forEach(f => {
                    tbody.innerHTML += `
                        <tr>
                            <td><strong>${escapeHtml(f.titre)}</strong></td>
                            <td>${escapeHtml(f.description.substring(0, 80))}${f.description.length > 80 ? '...' : ''}</td>
                            <td>⭐ ${f.total}</td>
                            <td>${f.dateGain}</td>
                            <td>
                                <a href="index.php?controller=fidelite&action=editFront&id=${f.idPoints}" class="btn-edit">✏️ Modifier</a>
                                <button class="btn-del" onclick="deleteFidelite(${f.idPoints})">🗑️ Supprimer</button>
                            </td>
                        </tr>
                    `;
                });
            }
        });
}

function deleteFidelite(id) {
    if (confirm('Supprimer cette demande de fidélité ?')) {
        fetch(`index.php?controller=fidelite&action=deleteFront&id=${id}`)
            .then(() => loadFideliteList());
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

loadFideliteList();
</script>

<style>
.form-section { background: #1a2535; padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; }
.form-section h4 { color: #00A86B; margin-bottom: 1rem; }
.error-border { border-color: #ef4444 !important; }
.error-message { color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem; display: block; }
</style>