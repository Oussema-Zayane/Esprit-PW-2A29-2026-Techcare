<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="page-header">
    <h1><i class="fa-solid fa-pen"></i> Modifier abonnement #<?= $abonnement['id'] ?></h1>
    <a href="index.php?admin&action=liste" class="btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Retour
    </a>
</div>

<div class="form-card">
    <?php if (isset($erreur)): ?>
        <div class="alert alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div><?= $erreur ?></div>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?admin&action=modifier&id=<?= $abonnement['id'] ?>">
        
        <div class="form-row">
            <div class="form-group">
                <label><i class="fa-solid fa-user"></i> Nom du client *</label>
                <input 
                    type="text" 
                    name="nom_client" 
                    value="<?= htmlspecialchars($abonnement['nom_client'] ?? '') ?>" 
                    required
                    placeholder="Jean Dupont"
                >
            </div>
            <div class="form-group">
                <label><i class="fa-solid fa-at"></i> Email</label>
                <input 
                    type="email" 
                    name="email" 
                    value="<?= htmlspecialchars($abonnement['email'] ?? '') ?>"
                    placeholder="client@example.com"
                >
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label><i class="fa-solid fa-tag"></i> Type d'abonnement *</label>
                <select name="type_abonnement" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="mensuel" <?= ($abonnement['type_abonnement'] ?? '') === 'mensuel' ? 'selected' : '' ?>>Mensuel</option>
                    <option value="annuel" <?= ($abonnement['type_abonnement'] ?? '') === 'annuel' ? 'selected' : '' ?>>Annuel</option>
                    <option value="ponctuel" <?= ($abonnement['type_abonnement'] ?? '') === 'ponctuel' ? 'selected' : '' ?>>Ponctuel</option>
                </select>
            </div>
            <div class="form-group">
                <label><i class="fa-solid fa-money-bill"></i> Prix (DT) *</label>
                <input 
                    type="number" 
                    name="prix" 
                    step="0.01" 
                    value="<?= $abonnement['prix'] ?? 0 ?>" 
                    required
                    placeholder="29.90"
                >
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label><i class="fa-solid fa-calendar"></i> Date de début *</label>
                <input 
                    type="date" 
                    name="date_debut" 
                    value="<?= $abonnement['date_debut'] ?? '' ?>" 
                    required
                >
            </div>
            <div class="form-group">
                <label><i class="fa-solid fa-calendar-x"></i> Date de fin *</label>
                <input 
                    type="date" 
                    name="date_fin" 
                    value="<?= $abonnement['date_fin'] ?? '' ?>" 
                    required
                >
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label><i class="fa-solid fa-circle-info"></i> Statut</label>
                <select name="statut">
                    <option value="actif" <?= ($abonnement['statut'] ?? '') === 'actif' ? 'selected' : '' ?>>Actif</option>
                    <option value="suspendu" <?= ($abonnement['statut'] ?? '') === 'suspendu' ? 'selected' : '' ?>>Suspendu</option>
                    <option value="expiré" <?= ($abonnement['statut'] ?? '') === 'expiré' ? 'selected' : '' ?>>Expiré</option>
                    <option value="annulé" <?= ($abonnement['statut'] ?? '') === 'annulé' ? 'selected' : '' ?>>Annulé</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <a href="index.php?admin&action=detail&id=<?= $abonnement['id'] ?>" class="btn-secondary">
                <i class="fa-solid fa-eye"></i> Voir
            </a>
            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-check"></i> Enregistrer les modifications
            </button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
