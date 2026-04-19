<?php include __DIR__ . '/../layout/header.php'; ?>

<section class="hero-section">
    <div class="container">
        <h1>Souscrire à un abonnement</h1>
        <p>Choisissez votre formule et complétez votre inscription</p>
    </div>
</section>

<section class="offres-section">
    <div class="container">
        <div style="max-width: 600px; margin: 0 auto;">
            <div class="form-section">
                <h2>Complétez votre inscription</h2>
                
                <?php if (isset($erreur)): ?>
                    <div class="alert alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <div><?= $erreur ?></div>
                    </div>
                <?php endif; ?>

                <form method="post" action="index.php?page=souscrire" id="form-souscrire" novalidate>
                    <div class="form-group">
                        <label>Nom complet *</label>
                        <input type="text" name="nom_client" id="nom_client" placeholder="Jean Dupont">
                        <span class="error-msg" id="error-nom_client"></span>
                    </div>

                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" id="email" placeholder="jean@example.com">
                        <span class="error-msg" id="error-email"></span>
                    </div>

                    <div class="form-group">
                        <label>Type d'abonnement *</label>
                        <select name="type_abonnement" id="type_abonnement">
                            <option value="">-- Sélectionner --</option>
                            <option value="mensuel">Mensuel (29.90 DT/mois)</option>
                            <option value="annuel">Annuel (299 DT/an)</option>
                            <option value="ponctuel">Ponctuel (49.90 DT)</option>
                        </select>
                        <span class="error-msg" id="error-type_abonnement"></span>
                    </div>

                    <!-- Date de début caché (défini à aujourd'hui) -->
                    <input type="hidden" name="date_debut" id="date_debut" value="<?php echo date('Y-m-d'); ?>">
                    <input type="hidden" name="date_fin" id="date_fin">

                    <div class="form-actions">
                        <a href="index.php?page=offres" class="btn-secondary">Annuler</a>
                        <button type="submit" class="btn-primary">
                            <i class="fa-solid fa-check"></i> Souscrire
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
.error-msg {
    display: none;
    color: #e74c3c;
    font-size: 0.7rem;
    margin-top: 0.3rem;
    padding: 0.3rem 0.5rem;
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
.alert-error {
    background: rgba(231, 76, 60, 0.1);
    border: 1px solid rgba(231, 76, 60, 0.3);
    color: #e74c3c;
    padding: 12px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
// Fonction pour calculer la date de fin selon le type
function calculerDateFin(type, dateDebut) {
    const d = new Date(dateDebut);
    if (type === 'mensuel') {
        d.setMonth(d.getMonth() + 1);
    } else if (type === 'annuel') {
        d.setFullYear(d.getFullYear() + 1);
    } else if (type === 'ponctuel') {
        d.setDate(d.getDate() + 7);
    }
    return d.toISOString().split('T')[0];
}

// Mettre à jour la date de fin quand le type change
document.getElementById('type_abonnement').addEventListener('change', function() {
    const type = this.value;
    const dateDebut = document.getElementById('date_debut').value;
    if (type && dateDebut) {
        const dateFin = calculerDateFin(type, dateDebut);
        document.getElementById('date_fin').value = dateFin;
    }
});

// Initialiser la date de fin au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    const type = document.getElementById('type_abonnement').value;
    const dateDebut = document.getElementById('date_debut').value;
    if (type && dateDebut) {
        const dateFin = calculerDateFin(type, dateDebut);
        document.getElementById('date_fin').value = dateFin;
    }
});

// Validation du formulaire
document.getElementById('form-souscrire').addEventListener('submit', function(e) {
    e.preventDefault();
    
    let isValid = true;
    
    // Nettoyer les erreurs précédentes
    document.querySelectorAll('.error-msg').forEach(el => el.classList.remove('show'));
    document.querySelectorAll('input, select').forEach(el => el.classList.remove('error'));
    
    // Validation nom
    const nom = document.getElementById('nom_client');
    if (!nom.value.trim()) {
        document.getElementById('error-nom_client').textContent = 'Le nom complet est obligatoire';
        document.getElementById('error-nom_client').classList.add('show');
        nom.classList.add('error');
        isValid = false;
    }
    
    // Validation email
    const email = document.getElementById('email');
    const emailValue = email.value.trim();
    if (!emailValue) {
        document.getElementById('error-email').textContent = 'L\'email est obligatoire';
        document.getElementById('error-email').classList.add('show');
        email.classList.add('error');
        isValid = false;
    } else if (!emailValue.includes('@') || !emailValue.includes('.')) {
        document.getElementById('error-email').textContent = 'Veuillez entrer un email valide (exemple@domaine.com)';
        document.getElementById('error-email').classList.add('show');
        email.classList.add('error');
        isValid = false;
    }
    
    // Validation type abonnement
    const type = document.getElementById('type_abonnement');
    if (!type.value) {
        document.getElementById('error-type_abonnement').textContent = 'Veuillez sélectionner un type d\'abonnement';
        document.getElementById('error-type_abonnement').classList.add('show');
        type.classList.add('error');
        isValid = false;
    }
    
    if (isValid) {
        this.submit();
    }
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>