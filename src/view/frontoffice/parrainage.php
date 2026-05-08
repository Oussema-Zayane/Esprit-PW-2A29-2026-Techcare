<div class="products-section">
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-users" style="color: var(--green);"></i> Programme de parrainage</h3>
        </div>
        <div class="admin-content">
            
            <!-- Afficher le code de parrainage -->
            <div style="background: #1a2535; padding: 1.5rem; border-radius: 16px; margin-bottom: 2rem; text-align: center;">
                <h4 style="color: #00A86B; margin-bottom: 0.5rem;">Votre code unique</h4>
                <div style="font-size: 2rem; font-family: monospace; background: #0f172a; padding: 1rem; border-radius: 12px; letter-spacing: 2px;">
                    <?= $codeInfo['code'] ?>
                </div>
                <p style="margin-top: 1rem; color: #8b9ab5;">
                    Partagez ce code avec vos amis !<br>
                    Chaque ami qui s'inscrit avec votre code vous rapporte <strong style="color: #00A86B;">200 points</strong> et lui rapporte <strong style="color: #00A86B;">100 points</strong>.
                </p>
                <button class="btn-primary" onclick="copierCode('<?= $codeInfo['code'] ?>')" style="margin-top: 1rem;">
                    <i class="fas fa-copy"></i> Copier le code
                </button>
                <a href="index.php?controller=gamification&action=genererCode" class="btn-outline" style="margin-top: 1rem; margin-left: 0.5rem;">
                    <i class="fas fa-sync-alt"></i> Générer un nouveau code
                </a>
            </div>
            
            <!-- Formulaire pour utiliser un code -->
            <div style="background: #1a2535; padding: 1.5rem; border-radius: 16px; margin-bottom: 2rem;">
                <h4 style="color: #00A86B; margin-bottom: 1rem;">Vous avez un code de parrainage ?</h4>
                <form method="POST" action="index.php?controller=gamification&action=utiliserCode">
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <input type="text" name="code_parrainage" placeholder="Entrez le code de parrainage" 
                               style="flex: 2; padding: 0.75rem; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: white;">
                        <button type="submit" class="btn-primary">Valider le code</button>
                    </div>
                </form>
            </div>
            
            <!-- Statistiques de parrainage -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div>
                        <span class="stat-value"><?= $statsParrainage['nb_parrainages'] ?? 0 ?></span>
                        <span class="stat-label">Personnes parrainées</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-star"></i></div>
                    <div>
                        <span class="stat-value"><?= number_format($statsParrainage['total_points'] ?? 0) ?></span>
                        <span class="stat-label">Points gagnés</span>
                    </div>
                </div>
            </div>
            
            <!-- Explication des gains -->
            <div class="admin-section" style="margin-top: 1rem; background: #0f172a;">
                <div class="admin-header">
                    <h3><i class="fas fa-info-circle"></i> Comment ça marche ?</h3>
                </div>
                <div class="admin-content">
                    <ul style="margin-left: 1.5rem; color: #8b9ab5; line-height: 1.8;">
                        <li>📤 Partagez votre code unique avec vos amis</li>
                        <li>📝 Votre ami s'inscrit en utilisant votre code</li>
                        <li>🎁 Vous gagnez <strong style="color: #00A86B;">200 points</strong> par parrainage</li>
                        <li>🎁 Votre ami gagne <strong style="color: #00A86B;">100 points</strong> bonus à l'inscription</li>
                        <li>🏆 Pas de limite de parrainage ! Plus vous parrainez, plus vous gagnez</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copierCode(code) {
    navigator.clipboard.writeText(code);
    showToast('Code copié dans le presse-papier !', 'success');
}
</script>