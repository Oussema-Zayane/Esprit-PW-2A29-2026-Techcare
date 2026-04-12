<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <h1><i class="fa-solid fa-plus-circle"></i> Nouvel abonnement</h1>
    <a href="index.php?admin&action=liste" class="btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Retour
    </a>
</div>

<div class="form-card">
    <form method="POST" action="index.php?admin&action=ajouter">
        
        <div class="form-row">
            <div class="form-group">
                <label><i class="fa-solid fa-user"></i> Nom du client *</label>
                <input type="text" name="nom_client" required placeholder="Jean Dupont">
            </div>
            <div class="form-group">
                <label><i class="fa-solid fa-at"></i> Email</label>
                <input type="email" name="email" placeholder="client@example.com">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label><i class="fa-solid fa-tag"></i> Type d'abonnement *</label>
                <select name="type_abonnement" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="mensuel">Mensuel</option>
                    <option value="annuel">Annuel</option>
                    <option value="ponctuel">Ponctuel</option>
                </select>
            </div>
            <div class="form-group">
                <label><i class="fa-solid fa-money-bill"></i> Prix (DT) *</label>
                <input type="number" name="prix" step="0.01" required placeholder="29.90">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label><i class="fa-solid fa-calendar"></i> Date de début *</label>
                <input type="date" name="date_debut" required value="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="form-group">
                <label><i class="fa-solid fa-calendar-x"></i> Date de fin *</label>
                <input type="date" name="date_fin" required>
            </div>
        </div>

        <div class="form-actions">
            <a href="index.php?admin&action=liste" class="btn-secondary">
                <i class="fa-solid fa-times"></i> Annuler
            </a>
            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-check"></i> Créer l'abonnement
            </button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>