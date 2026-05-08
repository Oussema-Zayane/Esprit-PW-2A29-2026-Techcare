<div class="products-section">
    <!-- Statistiques des badges -->
    <div class="stats-badges" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-medal"></i></div>
            <div>
                <span class="stat-value" id="totalBadges"><?= count($allBadges) ?></span>
                <span class="stat-label">Badges disponibles</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div>
                <span class="stat-value" id="unlockedBadges"><?= count($userBadgeIds) ?></span>
                <span class="stat-label">Badges débloqués</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div>
                <span class="stat-value" id="completionRate"><?= round((count($userBadgeIds) / max(1, count($allBadges))) * 100) ?>%</span>
                <span class="stat-label">Taux de complétion</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-star"></i></div>
            <div>
                <span class="stat-value" id="nextBadgePoints"><?php
                    $nextPoints = 0;
                    foreach ($allBadges as $b) {
                        if (!in_array($b['badge_id'], $userBadgeIds)) {
                            $nextPoints = $b['points_required'] - $user['points'];
                            break;
                        }
                    }
                    echo max(0, $nextPoints);
                ?></span>
                <span class="stat-label">Points avant prochain badge</span>
            </div>
        </div>
    </div>
    
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-medal" style="color: var(--green);"></i> Collection de badges</h3>
        </div>
        <div class="admin-content">
            <div class="badges-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1.5rem;">
                <?php foreach ($allBadges as $badge): 
                    $earned = in_array($badge['badge_id'], $userBadgeIds);
                    $imageIa = $badge['image_ia'] ?? null;
                    $hasImage = !empty($imageIa) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/Esprit-PW-2A29-2526-ProjetWeb/' . $imageIa);
                ?>
                <div class="badge-card <?= $earned ? 'earned' : 'locked' ?>" style="background: #1e293b; border-radius: 16px; padding: 1.5rem; text-align: center; border: 2px solid <?= $earned ? '#00A86B' : '#334155' ?>; opacity: <?= $earned ? '1' : '0.7' ?>; transition: all 0.3s ease;">
                    
                    <div class="badge-image" style="margin-bottom: 1rem; min-height: 80px;">
                        <?php if ($hasImage): ?>
                            <img src="/Esprit-PW-2A29-2526-ProjetWeb/<?= $imageIa ?>" 
                                 alt="<?= htmlspecialchars($badge['name']) ?>" 
                                 style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; border: 3px solid #00A86B; box-shadow: 0 0 10px rgba(0,168,107,0.3);">
                        <?php else: ?>
                            <div style="font-size: 3rem;"><?= $badge['icon'] ?? '🏅' ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <h3 style="color: #00A86B; margin-bottom: 0.5rem; font-size: 1.1rem;"><?= htmlspecialchars($badge['name']) ?></h3>
                    <p style="color: #94a3b8; font-size: 0.8rem; margin-bottom: 0.5rem; line-height: 1.4;"><?= htmlspecialchars($badge['description']) ?></p>
                    <div style="color: #f59e0b; font-weight: bold; margin-bottom: 0.5rem; font-size: 0.9rem;">⭐ <?= number_format($badge['points_required']) ?> pts</div>
                    <div style="margin-top: 0.5rem; padding: 0.25rem 0.75rem; border-radius: 20px; display: inline-block; background: <?= $earned ? '#00A86B' : '#334155' ?>; color: <?= $earned ? '#000' : '#94a3b8' ?>; font-size: 0.7rem; font-weight: bold;">
                        <?= $earned ? '✓ DÉBLOQUÉ' : '🔒 VERROUILLÉ' ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
.badge-card {
    transition: all 0.3s ease;
}
.badge-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,168,107,0.2);
}
.badge-card.earned {
    border-color: #00A86B;
}
.badge-card.locked {
    opacity: 0.7;
}
</style>

<script>
fetch('index.php?controller=gamification&action=getUserData')
    .then(response => response.json())
    .then(data => {
        document.getElementById('unlockedBadges').innerHTML = data.badges.length;
        const completionRate = Math.round((data.badges.length / <?= count($allBadges) ?>) * 100);
        document.getElementById('completionRate').innerHTML = completionRate + '%';
        
        // Mettre à jour les points restants
        let nextPoints = 0;
        <?php
        $nextPointsCalc = 0;
        foreach ($allBadges as $b) {
            if (!in_array($b['badge_id'], $userBadgeIds)) {
                $nextPointsCalc = $b['points_required'] - $user['points'];
                break;
            }
        }
        ?>
        const remaining = Math.max(0, <?= $nextPointsCalc ?> - (data.points - <?= $user['points'] ?>));
        document.getElementById('nextBadgePoints').innerHTML = remaining;
    })
    .catch(error => console.error('Erreur:', error));
</script>