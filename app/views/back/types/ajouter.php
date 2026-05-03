<?php include __DIR__ . '/../layout/header.php'; ?>
<?php include __DIR__ . '/../layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <div class="page-title">
            <h1><i class="fa-solid fa-plus-circle"></i> Ajouter un type d'abonnement</h1>
            <p>Créer un nouveau type d'abonnement (Mensuel, Annuel, Ponctuel...)</p>
        </div>
        <div class="user-info">
            <div class="user-avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <span>Admin</span>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="form-card">
        <div class="form-card-header">
            <i class="fa-solid fa-layer-group"></i>
            <h3>Nouveau type d'abonnement</h3>
        </div>

        <form method="POST" action="index.php?action=types-ajouter" id="form-ajouter" novalidate>
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fa-solid fa-tag"></i> Nom du type *</label>
                    <input type="text" name="nom_type" id="nom_type" placeholder="Ex: Mensuel, Annuel, Ponctuel">
                    <span class="error-msg" id="error-nom_type"></span>
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-money-bill"></i> Prix de base (DT) *</label>
                    <input type="number" name="prix_base" id="prix_base" step="0.01" placeholder="0.00">
                    <span class="error-msg" id="error-prix_base"></span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fa-solid fa-clock"></i> Durée (jours) *</label>
                    <input type="number" name="duree_jours" id="duree_jours" placeholder="30">
                    <span class="error-msg" id="error-duree_jours"></span>
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-image"></i> Icône</label>
                    <input type="text" name="icone" id="icone" placeholder="calendar-week, calendar-year, ticket">
                    <span class="error-msg" id="error-icone"></span>
                    <small class="hint">Icône Font Awesome (ex: calendar-week, calendar-year, ticket)</small>
                </div>
            </div>

            <!-- ═══ SECTION OFFRES DISPONIBLES ═══ -->
            <div class="offres-section">
                <div class="offres-header">
                    <h3><i class="fa-solid fa-gift"></i> Options supplémentaires disponibles</h3>
                </div>
                
                <?php if (!empty($offres) && count($offres) > 0): ?>
                    <div class="offres-grid">
                        <?php foreach ($offres as $offre): ?>
                            <label class="offre-checkbox">
                                <input type="checkbox" name="offres_selected[]" value="<?= $offre['id'] ?>">
                                <div class="offre-info">
                                    <span class="offre-description"><?= htmlspecialchars($offre['description']) ?></span>
                                    <?php if ($offre['id_abonnement']): ?>
                                        <span class="offre-badge linked">
                                            <i class="fa-solid fa-link"></i> Lié à abo #<?= $offre['id_abonnement'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="offre-badge unlinked">
                                            <i class="fa-solid fa-unlink"></i> Non lié
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="offres-empty">
                        <i class="fa-solid fa-inbox"></i>
                        <p>Aucune offre disponible</p>
                        <a href="index.php?action=offres-ajouter" class="btn-sm-green">
                            <i class="fa-solid fa-plus"></i> Créer une offre
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <a href="index.php?action=types" class="btn-secondary">
                    <i class="fa-solid fa-times"></i> Annuler
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-save"></i> Créer le type
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
.form-group input {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 12px 15px;
    color: var(--txt);
    font-size: 0.9rem;
    transition: all 0.3s;
}
.form-group input:focus {
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
    display: flex;
    align-items: center;
    gap: 10px;
}
.btn-sm-green {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(0,168,107,0.15);
    color: var(--green);
    border: 1px solid rgba(0,168,107,0.3);
    border-radius: 8px;
    padding: 8px 16px;
    font-size: 0.8rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
}
.btn-sm-green:hover {
    background: var(--green);
    color: white;
}

/* SECTION OFFRES */
.offres-section {
    margin: 20px 25px;
    background: var(--surface2);
    border-radius: 12px;
    padding: 15px;
    border: 1px solid var(--border);
}
.offres-header {
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border);
}
.offres-header h3 {
    font-size: 0.9rem;
    color: var(--txt);
    display: flex;
    align-items: center;
    gap: 8px;
}
.offres-header h3 i {
    color: var(--green);
}
.offres-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 10px;
    max-height: 300px;
    overflow-y: auto;
}
.offre-checkbox {
    display: flex;
    align-items: center;
    gap: 12px;
    background: var(--card);
    border-radius: 10px;
    padding: 10px 12px;
    cursor: pointer;
    transition: all 0.2s;
    border: 1px solid var(--border);
}
.offre-checkbox:hover {
    border-color: rgba(0,168,107,0.3);
    background: rgba(0,168,107,0.05);
}
.offre-checkbox input {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: var(--green);
    flex-shrink: 0;
}
.offre-info {
    flex: 1;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
}
.offre-description {
    font-size: 0.85rem;
    color: var(--txt);
    font-weight: 500;
}
.offre-badge {
    font-size: 0.65rem;
    padding: 2px 8px;
    border-radius: 20px;
}
.offre-badge.linked {
    background: rgba(0,168,107,0.15);
    color: var(--green-lt);
}
.offre-badge.unlinked {
    background: rgba(108,117,125,0.15);
    color: var(--txt-muted);
}
.offres-empty {
    text-align: center;
    padding: 30px;
    color: var(--txt-muted);
}
.offres-empty i {
    font-size: 2rem;
    margin-bottom: 10px;
    opacity: 0.5;
}
@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    .form-actions {
        flex-direction: column;
    }
    .btn-primary, .btn-secondary {
        justify-content: center;
        width: 100%;
    }
    .offres-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Validation formulaire
document.getElementById('form-ajouter').addEventListener('submit', function(e) {
    let isValid = true;
    
    document.querySelectorAll('.error-msg').forEach(el => el.classList.remove('show'));
    document.querySelectorAll('input').forEach(el => el.classList.remove('error'));
    
    const nom = document.getElementById('nom_type');
    if (!nom.value.trim()) {
        document.getElementById('error-nom_type').textContent = 'Le nom du type est obligatoire';
        document.getElementById('error-nom_type').classList.add('show');
        nom.classList.add('error');
        isValid = false;
    }
    
    const prix = document.getElementById('prix_base');
    if (!prix.value || parseFloat(prix.value) <= 0) {
        document.getElementById('error-prix_base').textContent = 'Le prix doit être supérieur à 0';
        document.getElementById('error-prix_base').classList.add('show');
        prix.classList.add('error');
        isValid = false;
    }
    
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