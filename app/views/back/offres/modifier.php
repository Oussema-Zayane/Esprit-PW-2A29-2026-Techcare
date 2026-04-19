<?php include __DIR__ . '/../layout/header.php'; ?>
<?php include __DIR__ . '/../layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <div class="page-title">
            <h1><i class="fa-solid fa-pen"></i> Modifier l'option</h1>
            <p>Modifier l'option #<?= $offre['id'] ?></p>
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
            <h3>Modification de l'option</h3>
        </div>

        <form method="POST" action="index.php?action=offres-modifier&id=<?= $offre['id'] ?>" id="form-modifier" novalidate>
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fa-solid fa-align-left"></i> Description *</label>
                    <input type="text" name="description" id="description" value="<?= htmlspecialchars($offre['description']) ?>" required>
                    <span class="error-msg" id="error-description"></span>
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-tag"></i> Abonnement lié</label>
                    <select name="id_abonnement" id="id_abonnement">
                        <option value="">-- Aucun --</option>
                        <?php foreach ($abonnements as $a): ?>
                            <option value="<?= $a['id'] ?>" <?= ($offre['id_abonnement'] == $a['id']) ? 'selected' : '' ?>>
                                #<?= $a['id'] ?> - <?= htmlspecialchars($a['nom_client']) ?> (<?= $a['type_abonnement'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <a href="index.php?action=offres" class="btn-secondary">
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
.form-group input, .form-group select {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 12px 15px;
    color: var(--txt);
    font-size: 0.9rem;
    transition: all 0.3s;
}
.form-group input:focus, .form-group select:focus {
    outline: none;
    border-color: var(--green);
    box-shadow: 0 0 0 3px rgba(0,168,107,0.1);
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
.alert-error {
    background: rgba(231,76,60,0.1);
    border: 1px solid rgba(231,76,60,0.3);
    color: #e74c3c;
    padding: 12px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
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
}
</style>

<script>
document.getElementById('form-modifier').addEventListener('submit', function(e) {
    let isValid = true;
    const description = document.getElementById('description');
    const errorDesc = document.getElementById('error-description');
    
    if (!description.value.trim()) {
        errorDesc.textContent = 'La description est obligatoire';
        errorDesc.classList.add('show');
        description.classList.add('error');
        isValid = false;
    } else {
        errorDesc.classList.remove('show');
        description.classList.remove('error');
    }
    
    if (!isValid) {
        e.preventDefault();
    }
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>