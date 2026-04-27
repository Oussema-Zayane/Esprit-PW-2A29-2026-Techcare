<div class="products-section">
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-edit" style="color: var(--green);"></i> Modifier un enregistrement de fidélité</h3>
        </div>
        <div class="admin-content">
            <?php if (isset($_SESSION['errors'])): ?>
                <div class="alert-error" style="background: #ef444420; border: 1px solid #ef4444; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <strong>❌ Erreurs de validation :</strong>
                    <ul style="margin-top: 10px; margin-left: 20px;">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>
            
            <form method="POST" action="index.php?controller=fidelite&action=update" id="fideliteForm">
                <input type="hidden" name="id" value="<?= $fidelite['idPoints'] ?>">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div class="form-group">
                        <label for="titre"><i class="fas fa-tag"></i> Titre *</label>
                        <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($fidelite['titre']) ?>" 
                               style="width:100%; padding:0.75rem; background:#1a2535; border:1px solid rgba(255,255,255,0.07); border-radius:8px; color:white;">
                    </div>
                    
                    <div class="form-group">
                        <label for="total"><i class="fas fa-star"></i> Points *</label>
                        <input type="number" id="total" name="total" value="<?= $fidelite['total'] ?>" 
                               style="width:100%; padding:0.75rem; background:#1a2535; border:1px solid rgba(255,255,255,0.07); border-radius:8px; color:white;">
                    </div>
                    
                    <div class="form-group">
                        <label for="dateGain"><i class="fas fa-calendar"></i> Date *</label>
                        <input type="date" id="dateGain" name="dateGain" value="<?= $fidelite['dateGain'] ?>" 
                               style="width:100%; padding:0.75rem; background:#1a2535; border:1px solid rgba(255,255,255,0.07); border-radius:8px; color:white;">
                    </div>
                    
                    <div class="form-group" style="grid-column: span 2;">
                        <label for="description"><i class="fas fa-align-left"></i> Description *</label>
                        <textarea id="description" name="description" rows="4" 
                                  style="width:100%; padding:0.75rem; background:#1a2535; border:1px solid rgba(255,255,255,0.07); border-radius:8px; color:white;"><?= htmlspecialchars($fidelite['description']) ?></textarea>
                        <small id="descCount" style="display: block; margin-top: 5px; font-size: 0.75rem; color: #8b9ab5; text-align: right;"><?= strlen($fidelite['description']) ?> / 500 caractères</small>
                    </div>
                </div>
                
                <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="submit" class="btn-primary">💾 Enregistrer</button>
                    <a href="index.php?controller=fidelite&action=index" class="btn-outline">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.error-border { border-color: #ef4444 !important; }
.error-message { color: #ef4444; font-size: 0.75rem; margin-top: 5px; display: block; }
.form-group { margin-bottom: 0.5rem; }
.form-group label { display: block; margin-bottom: 0.5rem; color: var(--green); font-weight: 500; }
</style>

<script src="/project2A29/assets/js/validation.js"></script>