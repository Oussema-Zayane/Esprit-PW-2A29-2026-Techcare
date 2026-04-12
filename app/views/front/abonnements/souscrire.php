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

                <form method="post" action="index.php?page=souscrire">
                    <div class="form-group">
                        <label>Nom complet *</label>
                        <input type="text" name="nom_client" required placeholder="Jean Dupont">
                    </div>

                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" required placeholder="jean@example.com">
                    </div>

                    <div class="form-group">
                        <label>Type d'abonnement *</label>
                        <select name="type_abonnement" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="mensuel">Mensuel (29.90 DT/mois)</option>
                            <option value="annuel">Annuel (299 DT/an)</option>
                        </select>
                    </div>

                    <!-- Date de début caché (défini à aujourd'hui) -->
                    <input type="hidden" name="date_debut" value="<?php echo date('Y-m-d'); ?>">

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

<?php include __DIR__ . '/../layout/footer.php'; ?>