<?php include __DIR__ . '/../layout/header.php'; ?>

<section class="hero-section">
    <div class="container">
        <h1>Détail de mon abonnement</h1>
        <p>Informations complètes de votre abonnement</p>
    </div>
</section>

<section class="offres-section">
    <div class="container">
        <div class="form-card" style="max-width: 700px; margin: 0 auto;">
            <div style="text-align: right; margin-bottom: 1.5rem;">
                <span class="badge badge-<?= $abonnement['statut'] ?>">
                    <i class="fa-solid fa-circle"></i> <?= ucfirst($abonnement['statut']) ?>
                </span>
            </div>

            <!-- Informations -->
            <div class="form-row">
                <div class="form-group full">
                    <label><strong>📋 Numéro d'abonnement</strong></label>
                    <div style="padding: 0.75rem; background: rgba(0,168,107,0.05); border-radius: 8px; font-weight: bold; color: #333;">
                        #<?= $abonnement['id'] ?>
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><strong>💼 Type d'abonnement</strong></label>
                    <div style="padding: 0.75rem; background: rgba(0,168,107,0.05); border-radius: 8px; color: #333;">
                        <?= ucfirst($abonnement['type_abonnement']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label><strong>💰 Prix</strong></label>
                    <div style="padding: 0.75rem; background: rgba(0,168,107,0.05); border-radius: 8px; color: #00A86B; font-weight: bold; font-size: 1.2rem;">
                        <?= number_format($abonnement['prix'], 2) ?> DT
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><strong>📅 Date de début</strong></label>
                    <div style="padding: 0.75rem; background: rgba(0,168,107,0.05); border-radius: 8px; color: #333;">
                        <?= date('d/m/Y', strtotime($abonnement['date_debut'])) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label><strong>📅 Date de fin</strong></label>
                    <div style="padding: 0.75rem; background: rgba(0,168,107,0.05); border-radius: 8px; color: #333;">
                        <?= date('d/m/Y', strtotime($abonnement['date_fin'])) ?>
                    </div>
                </div>
            </div>

            <!-- Jours restants -->
            <?php 
                $dateEnd = new DateTime($abonnement['date_fin']);
                $dateNow = new DateTime();
                $interval = $dateNow->diff($dateEnd);
                $daysLeft = $interval->days;
            ?>
            <div class="form-row">
                <div class="form-group full">
                    <label><strong>⏱️ Jours restants</strong></label>
                    <div style="padding: 0.75rem; background: rgba(0,168,107,0.05); border-radius: 8px; color: #333;">
                        <strong style="color: #00A86B; font-size: 1.5rem;"><?= $daysLeft ?></strong> jours
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="form-actions" style="margin-top: 2rem; border-top: 1px solid var(--border); padding-top: 2rem;">
                <a href="index.php?page=mes-abonnements" class="btn-secondary" style="color: #0c0b0b;">
                    <i class="fa-solid fa-arrow-left"></i> Retour
                </a>
                <a href="index.php?page=modifier-abo&id=<?= $abonnement['id'] ?>" class="btn-primary">
                    <i class="fa-solid fa-pen"></i> Modifier
                </a>
                <a href="index.php?page=supprimer-abo&id=<?= $abonnement['id'] ?>" 
                   class="btn-danger"
                   onclick="return confirm('Voulez-vous vraiment résilier cet abonnement ?')"
                   style="background: #e74c3c; color: white; padding: 0.7rem 1.5rem; border-radius: 8px; font-size: 0.9rem; text-decoration: none; cursor: pointer; border: none;">
                    <i class="fa-solid fa-trash"></i> Résilier
                </a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../layout/footer.php'; ?>
