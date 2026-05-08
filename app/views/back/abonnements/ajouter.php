<?php include __DIR__ . '/../layout/header.php'; ?>
<?php include __DIR__ . '/../layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <div class="page-title">
            <h1><i class="fa-solid fa-plus-circle"></i> Ajouter un abonnement</h1>
            <p>Créer un nouvel abonnement dans le système</p>
        </div>
        <div class="user-info">
            <div class="user-avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <span>Admin</span>
        </div>
    </div>

    <!-- Formulaire style carte -->
    <div class="form-card">
        <div class="form-card-header">
            <i class="fa-solid fa-file-signature"></i>
            <h3>Informations abonnement</h3>
        </div>
        
        <form method="POST" action="index.php?action=ajouter" id="form-ajouter" novalidate>
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fa-solid fa-user"></i> Nom du client *</label>
                    <input type="text" name="nom_client" id="nom_client" placeholder="Jean Dupont">
                    <span class="error-msg" id="error-nom_client"></span>
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-at"></i> Email</label>
                    <input type="email" name="email" id="email" placeholder="client@example.com">
                    <span class="error-msg" id="error-email"></span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fa-solid fa-tag"></i> Type d'abonnement *</label>
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
                                        data-duree="<?= $type['duree_jours'] ?>">
                                    <?= $icon ?> <?= ucfirst($type['nom_type']) ?> - <?= number_format($type['prix_base'], 2) ?> DT
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>Aucun type disponible</option>
                        <?php endif; ?>
                    </select>
                    <span class="error-msg" id="error-type_abonnement"></span>
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-money-bill"></i> Prix (DT) *</label>
                    <input type="number" name="prix" id="prix" step="0.01" placeholder="0.00">
                    <span class="error-msg" id="error-prix"></span>
                </div>
            </div>

            <!-- AI Recommendations Section -->
            <div id="ai-recommendations" style="display: none;">
                <div class="ai-header">
                    <i class="fa-solid fa-robot ai-icon"></i>
                    <h4>Recommandations Intelligentes</h4>
                </div>
                <p class="ai-desc">Basé sur le profil du client et ce type d'abonnement, notre système recommande ces options :</p>
                <div class="ai-cards-container" id="ai-cards-container">
                    <!-- Cards will be injected here via JS -->
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fa-solid fa-calendar"></i> Date de début *</label>
                    <input type="date" name="date_debut" id="date_debut" value="<?php echo date('Y-m-d'); ?>">
                    <span class="error-msg" id="error-date_debut"></span>
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-calendar-xmark"></i> Date de fin *</label>
                    <input type="date" name="date_fin" id="date_fin">
                    <span class="error-msg" id="error-date_fin"></span>
                </div>
            </div>

            <div class="form-actions">
                <a href="index.php?action=liste" class="btn-secondary">
                    <i class="fa-solid fa-times"></i> Annuler
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-check"></i> Créer l'abonnement
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Style carte formulaire - comme les stats */
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
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-card-header i {
    font-size: 1.3rem;
    color: var(--green);
}

.form-card-header h3 {
    font-size: 1rem;
    color: var(--txt);
    margin: 0;
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

.form-group input,
.form-group select {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 12px 15px;
    color: var(--txt);
    font-size: 0.9rem;
    transition: all 0.3s;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--green);
    box-shadow: 0 0 0 3px rgba(0,168,107,0.1);
}

.form-group input::placeholder {
    color: var(--txt-dim);
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

input.error, select.error {
    border-color: #e74c3c !important;
    background-color: rgba(231, 76, 60, 0.05) !important;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
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

/* AI Recommendation Styles */
#ai-recommendations {
    margin: 20px 25px;
    padding: 20px;
    background: linear-gradient(145deg, #1a1c23 0%, #12141a 100%);
    border: 1px solid #2c303a;
    border-radius: 12px;
    position: relative;
    overflow: hidden;
    animation: fadeIn 0.4s ease-out;
}

#ai-recommendations::before {
    content: '';
    position: absolute;
    top: 0; left: 0; width: 100%; height: 3px;
    background: linear-gradient(90deg, #00d2ff 0%, #3a7bd5 100%);
    box-shadow: 0 0 10px rgba(0, 210, 255, 0.5);
}

.ai-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
}

.ai-icon {
    font-size: 1.4rem;
    color: #00d2ff;
    text-shadow: 0 0 8px rgba(0, 210, 255, 0.6);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); text-shadow: 0 0 8px rgba(0, 210, 255, 0.6); }
    50% { transform: scale(1.1); text-shadow: 0 0 15px rgba(0, 210, 255, 0.9); }
    100% { transform: scale(1); text-shadow: 0 0 8px rgba(0, 210, 255, 0.6); }
}

.ai-header h4 {
    margin: 0;
    color: #fff;
    font-size: 1.1rem;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.ai-desc {
    color: #a0a5b1;
    font-size: 0.85rem;
    margin-bottom: 15px;
}

.ai-cards-container {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.ai-card {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 15px;
    flex: 1;
    min-width: 200px;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}

.ai-card:hover {
    background: rgba(0, 210, 255, 0.05);
    border-color: rgba(0, 210, 255, 0.3);
    transform: translateY(-2px);
}

.ai-card.selected {
    background: rgba(0, 210, 255, 0.15);
    border-color: #00d2ff;
    box-shadow: 0 0 15px rgba(0, 210, 255, 0.3);
    transform: translateY(-2px);
}

.ai-card.selected::after {
    content: '\f00c';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    position: absolute;
    bottom: 10px;
    right: 15px;
    color: #00d2ff;
    font-size: 1.2rem;
}

.ai-card-score {
    position: absolute;
    top: -10px;
    right: 10px;
    background: #00d2ff;
    color: #000;
    font-size: 0.7rem;
    font-weight: 800;
    padding: 3px 8px;
    border-radius: 12px;
    box-shadow: 0 2px 5px rgba(0, 210, 255, 0.4);
}

.ai-card-title {
    color: var(--txt);
    font-size: 0.95rem;
    font-weight: 600;
    margin-bottom: 5px;
}

.ai-card-price {
    color: #00d2ff;
    font-size: 0.85rem;
    font-weight: 500;
    margin-bottom: 8px;
}

.ai-card-reason {
    color: var(--txt-muted);
    font-size: 0.75rem;
    font-style: italic;
    border-top: 1px solid var(--border);
    padding-top: 8px;
    margin-top: 8px;
}
</style>

<script>
// Fonction pour calculer la date de fin
function calculerDateFin(dateDebut, dureeJours) {
    if (!dateDebut || !dureeJours) return '';
    const d = new Date(dateDebut);
    d.setDate(d.getDate() + parseInt(dureeJours));
    return d.toISOString().split('T')[0];
}

// Auto-calcul du prix et date fin selon le type
document.getElementById('type_abonnement').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const selectedType = selectedOption.value;
    const prixInput = document.getElementById('prix');
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    
    if (selectedType) {
        const prixBase = selectedOption.getAttribute('data-prix');
        const dureeJours = selectedOption.getAttribute('data-duree');
        
        if (prixBase) {
            prixInput.value = parseFloat(prixBase).toFixed(2);
            // Si le type change, on recalcule le prix total (car les sélections IA vont être réinitialisées)
            recalculerPrixTotal();
        }
        
        if (dateDebut.value && dureeJours) {
            dateFin.value = calculerDateFin(dateDebut.value, dureeJours);
        }
        
        // Load AI Recommendations
        fetchRecommendations(selectedType);
        
    } else {
        prixInput.value = '';
        dateFin.value = '';
        document.getElementById('ai-recommendations').style.display = 'none';
    }
});

// Recalculer le prix en incluant les recommandations sélectionnées
function recalculerPrixTotal() {
    const typeSelect = document.getElementById('type_abonnement');
    const selectedOption = typeSelect.options[typeSelect.selectedIndex];
    const prixInput = document.getElementById('prix');
    
    if (selectedOption && selectedOption.value) {
        let basePrice = parseFloat(selectedOption.getAttribute('data-prix')) || 0;
        
        const selectedCards = document.querySelectorAll('.ai-card.selected');
        let addonsPrice = 0;
        selectedCards.forEach(card => {
            addonsPrice += parseFloat(card.getAttribute('data-prix-supp')) || 0;
        });
        
        prixInput.value = (basePrice + addonsPrice).toFixed(2);
    }
}

function fetchRecommendations(type) {
    const aiSection = document.getElementById('ai-recommendations');
    const container = document.getElementById('ai-cards-container');
    
    // Show a loading state if needed
    aiSection.style.display = 'block';
    container.innerHTML = '<div style="color:#a0a5b1; font-size:0.85rem;"><i class="fa-solid fa-spinner fa-spin"></i> Analyse en cours par l\'IA...</div>';
    
    fetch(`index.php?action=api_recommend_options&type=${encodeURIComponent(type)}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                let html = '';
                data.forEach(offre => {
                    html += `
                        <div class="ai-card" data-prix-supp="${offre.prix_supplementaire}" title="Cliquez pour ajouter l'option à votre abonnement">
                            <div class="ai-card-score">${offre.match_score}% Match</div>
                            <div class="ai-card-title">${offre.description}</div>
                            <div class="ai-card-price">+${parseFloat(offre.prix_supplementaire).toFixed(2)} DT</div>
                            <div class="ai-card-reason"><i class="fa-solid fa-sparkles" style="color:#00d2ff; font-size:0.7rem; margin-right:4px;"></i> ${offre.ai_reason}</div>
                        </div>
                    `;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div style="color:#a0a5b1; font-size:0.85rem;">Aucune recommandation trouvée pour ce type d\'abonnement.</div>';
            }
        })
        .catch(error => {
            console.error('Erreur IA:', error);
            aiSection.style.display = 'none';
        });
}

// Gestion du clic sur les recommandations IA
document.getElementById('ai-cards-container').addEventListener('click', function(e) {
    const card = e.target.closest('.ai-card');
    if (card) {
        card.classList.toggle('selected');
        recalculerPrixTotal();
    }
});

// Mise à jour date fin quand date début change
document.getElementById('date_debut').addEventListener('change', function() {
    const typeSelect = document.getElementById('type_abonnement');
    const selectedOption = typeSelect.options[typeSelect.selectedIndex];
    const dateFin = document.getElementById('date_fin');
    
    if (this.value && selectedOption && selectedOption.value) {
        const dureeJours = selectedOption.getAttribute('data-duree');
        if (dureeJours) {
            dateFin.value = calculerDateFin(this.value, dureeJours);
        }
    }
});

// Validation du formulaire
document.getElementById('form-ajouter').addEventListener('submit', function(e) {
    e.preventDefault();
    
    let isValid = true;
    
    document.querySelectorAll('.error-msg').forEach(el => el.classList.remove('show'));
    document.querySelectorAll('input, select').forEach(el => el.classList.remove('error'));
    
    const nom = document.getElementById('nom_client');
    if (!nom.value.trim()) {
        document.getElementById('error-nom_client').textContent = 'Le nom du client est obligatoire';
        document.getElementById('error-nom_client').classList.add('show');
        nom.classList.add('error');
        isValid = false;
    }
    
    const type = document.getElementById('type_abonnement');
    if (!type.value) {
        document.getElementById('error-type_abonnement').textContent = 'Veuillez sélectionner un type';
        document.getElementById('error-type_abonnement').classList.add('show');
        type.classList.add('error');
        isValid = false;
    }
    
    const prix = document.getElementById('prix');
    if (!prix.value || parseFloat(prix.value) <= 0) {
        document.getElementById('error-prix').textContent = 'Le prix doit être supérieur à 0';
        document.getElementById('error-prix').classList.add('show');
        prix.classList.add('error');
        isValid = false;
    }
    
    const dateDebut = document.getElementById('date_debut');
    if (!dateDebut.value) {
        document.getElementById('error-date_debut').textContent = 'La date de début est obligatoire';
        document.getElementById('error-date_debut').classList.add('show');
        dateDebut.classList.add('error');
        isValid = false;
    }
    
    const dateFin = document.getElementById('date_fin');
    if (!dateFin.value) {
        document.getElementById('error-date_fin').textContent = 'La date de fin est obligatoire';
        document.getElementById('error-date_fin').classList.add('show');
        dateFin.classList.add('error');
        isValid = false;
    }
    
    if (dateDebut.value && dateFin.value && dateFin.value < dateDebut.value) {
        document.getElementById('error-date_fin').textContent = 'La date de fin doit être après la date de début';
        document.getElementById('error-date_fin').classList.add('show');
        dateFin.classList.add('error');
        isValid = false;
    }
    
    if (isValid) {
        this.submit();
    }
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>