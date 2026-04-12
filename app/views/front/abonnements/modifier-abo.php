<?php include __DIR__ . '/../layout/header.php'; ?>

<section class="hero-section">
    <div class="container">
        <h1>Modifier mon abonnement</h1>
        <p>Mettez à jour les informations de votre abonnement</p>
    </div>
</section>

<section class="offres-section">
    <div class="container">
        <div class="form-card" style="max-width: 700px; margin: 0 auto;">
            <?php if (isset($erreur)): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <div><?= $erreur ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=modifier-abo&id=<?= $abonnement['id'] ?>">
                
                <div class="form-row">
                    <div class="form-group full">
                        <label><i class="fa-solid fa-info"></i> ID: #<?= $abonnement['id'] ?></label>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fa-solid fa-tag"></i> Type d'abonnement *</label>
                        <select name="type_abonnement" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="mensuel" <?= ($abonnement['type_abonnement'] ?? '') === 'mensuel' ? 'selected' : '' ?>>Mensuel</option>
                            <option value="annuel" <?= ($abonnement['type_abonnement'] ?? '') === 'annuel' ? 'selected' : '' ?>>Annuel</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fa-solid fa-money-bill"></i> Prix (pour info) *</label>
                        <input 
                            type="text" 
                            value="<?= $abonnement['prix'] ?> DT"
                            disabled
                            style="background: rgba(0,168,107,0.05);"
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

                <div class="form-actions">
                    <a href="index.php?page=detail-abo&id=<?= $abonnement['id'] ?>" class="btn-secondary">
                        <i class="fa-solid fa-times"></i> Annuler
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-check"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../layout/footer.php'; ?>
