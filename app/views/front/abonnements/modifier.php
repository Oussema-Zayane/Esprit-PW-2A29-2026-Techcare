<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="container">
    <h1><i class="fa-solid fa-pen"></i> Modifier mon abonnement</h1>
    
    <form method="POST" action="index.php?page=modifier-abonnement&id=<?= $abonnement['id'] ?>" class="form-modifier">
        
        <div class="form-group">
            <label>Type d'abonnement</label>
            <select name="type_abonnement">
                <option value="mensuel" <?= $abonnement['type_abonnement'] == 'mensuel' ? 'selected' : '' ?>>Mensuel - 29.90 DT</option>
                <option value="annuel" <?= $abonnement['type_abonnement'] == 'annuel' ? 'selected' : '' ?>>Annuel - 299 DT</option>
                <option value="ponctuel" <?= $abonnement['type_abonnement'] == 'ponctuel' ? 'selected' : '' ?>>Ponctuel - 49.90 DT</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Date de début</label>
            <input type="date" name="date_debut" value="<?= $abonnement['date_debut'] ?>" required>
        </div>
        
        <div class="form-group">
            <label>Date de fin</label>
            <input type="date" name="date_fin" value="<?= $abonnement['date_fin'] ?>" required>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-save"></i> Enregistrer
            </button>
            <a href="index.php?page=mes-abonnements" class="btn-secondary">
                Annuler
            </a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>