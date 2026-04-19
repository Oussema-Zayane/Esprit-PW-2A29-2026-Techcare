<?php include __DIR__ . '/../layout/header.php'; ?>
<?php include __DIR__ . '/../layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <div class="page-title">
            <h1><i class="fa-solid fa-pen"></i> Modifier un Type d'Abonnement</h1>
            <p>Modifier les informations du type #<?= isset($type) ? $type['id_type'] : '' ?></p>
        </div>
        <div class="user-info">
            <div class="user-avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <span>Admin</span>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="form-card">
        <div class="form-card-header">
            <i class="fa-solid fa-pen-to-square"></i>
            <h3>Formulaire de modification</h3>
        </div>

        <form method="POST" action="index.php?action=types-modifier&id=<?= isset($type) ? $type['id_type'] : '' ?>" id="typeForm" novalidate>
            
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fa-solid fa-tag"></i> Nom du type *</label>
                    <input type="text" name="nom_type" id="nom_type" value="<?= isset($type) ? htmlspecialchars($type['nom_type']) : '' ?>" placeholder="Ex: Mensuel, Annuel, Ponctuel">
                    <span class="error-msg" id="error-nom_type"></span>
                    <small class="hint">Nom unique pour identifier ce type d'abonnement</small>
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-money-bill"></i> Prix de base (DT) *</label>
                    <input type="number" name="prix_base" id="prix_base" step="0.01" value="<?= isset($type) ? $type['prix_base'] : '' ?>" placeholder="0.00">
                    <span class="error-msg" id="error-prix_base"></span>
                    <small class="hint">Prix de base en dinars tunisiens</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fa-solid fa-clock"></i> Durée (jours) *</label>
                    <input type="number" name="duree_jours" id="duree_jours" value="<?= isset($type) ? $type['duree_jours'] : '' ?>" placeholder="30">
                    <span class="error-msg" id="error-duree_jours"></span>
                    <small class="hint">Durée de validité de l'abonnement en jours</small>
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-image"></i> Icône</label>
                    <input type="text" name="icone" id="icone" value="<?= isset($type) ? htmlspecialchars($type['icone'] ?? 'tag') : 'tag' ?>" placeholder="calendar-week, calendar-year, ticket">
                    <span class="error-msg" id="error-icone"></span>
                    <small class="hint">Icône Font Awesome (ex: calendar-week, calendar-year, ticket)</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group span-2">
                    <label><i class="fa-solid fa-align-left"></i> Description</label>
                    <textarea name="description" id="description" rows="4" placeholder="Description des avantages de ce type d'abonnement..."><?= isset($type) ? htmlspecialchars($type['description'] ?? '') : '' ?></textarea>
                    <span class="error-msg" id="error-description"></span>
                    <small class="hint">Description optionnelle pour expliquer les avantages de ce type</small>
                </div>
            </div>

            <div class="form-actions">
                <a href="index.php?action=types" class="btn-secondary">
                    <i class="fa-solid fa-times"></i> Annuler
                </a>
                <button type="submit" class="btn-primary">
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
.form-group.span-2 {
    grid-column: span 2;
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
.form-group input, .form-group select, .form-group textarea {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 12px 15px;
    color: var(--txt);
    font-size: 0.9rem;
    transition: all 0.3s;
}
.form-group input:focus, .form-group select:focus, .form-group textarea:focus {
    outline: none;
    border-color: var(--green);
    box-shadow: 0 0 0 3px rgba(0,168,107,0.1);
}
.hint {
    font-size: 0.7rem;
    color: var(--txt-dim);
    margin-top: 4px;
}
.error-msg {
    display: none;
    color: #e74c3c;
    font-size: 0.7rem;
    margin-top: 5px;
    padding: 5px 10px;
    background: rgba(231,76,60,0.1);
    border-radius: 6px;
}
.error-msg.show {
    display: block;
}
input.error, select.error, textarea.error {
    border-color: #e74c3c !important;
    background-color: rgba(231,76,60,0.05) !important;
}
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
.alert-error {
    background: rgba(231,76,60,0.1);
    border: 1px solid rgba(231,76,60,0.3);
    color: #e74c3c;
    padding: 12px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}
@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    .form-group.span-2 {
        grid-column: span 1;
    }
    .form-actions {
        flex-direction: column;
    }
    .btn-primary, .btn-secondary {
        justify-content: center;
        width: 100%;
    }
}
</style>

<script>
document.getElementById('typeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    let isValid = true;
    
    // Nettoyer les erreurs précédentes
    document.querySelectorAll('.error-msg').forEach(el => el.classList.remove('show'));
    document.querySelectorAll('input, textarea').forEach(el => el.classList.remove('error'));
    
    // Validation nom
    const nom = document.getElementById('nom_type');
    if (!nom.value.trim()) {
        document.getElementById('error-nom_type').textContent = 'Le nom du type est obligatoire';
        document.getElementById('error-nom_type').classList.add('show');
        nom.classList.add('error');
        isValid = false;
    }
    
    // Validation prix
    const prix = document.getElementById('prix_base');
    if (!prix.value || parseFloat(prix.value) <= 0) {
        document.getElementById('error-prix_base').textContent = 'Le prix doit être supérieur à 0';
        document.getElementById('error-prix_base').classList.add('show');
        prix.classList.add('error');
        isValid = false;
    }
    
    // Validation durée
    const duree = document.getElementById('duree_jours');
    if (!duree.value || parseInt(duree.value) <= 0) {
        document.getElementById('error-duree_jours').textContent = 'La durée doit être supérieure à 0';
        document.getElementById('error-duree_jours').classList.add('show');
        duree.classList.add('error');
        isValid = false;
    }
    
    if (isValid) {
        this.submit();
    }
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>