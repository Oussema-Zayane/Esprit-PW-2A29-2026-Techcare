<div class="products-section">
    <?php
    $avatars = [
        0 => ['icon' => '🥚', 'name' => 'Œuf', 'min' => 0, 'max' => 99],
        100 => ['icon' => '🐣', 'name' => 'Poussin', 'min' => 100, 'max' => 499],
        500 => ['icon' => '🦊', 'name' => 'Renard', 'min' => 500, 'max' => 999],
        1000 => ['icon' => '🐺', 'name' => 'Loup', 'min' => 1000, 'max' => 2499],
        2500 => ['icon' => '🦅', 'name' => 'Aigle', 'min' => 2500, 'max' => 4999],
        5000 => ['icon' => '🐉', 'name' => 'Dragon', 'min' => 5000, 'max' => 999999]
    ];
    $currentAvatar = $avatars[0];
    foreach ($avatars as $avatar) {
        if ($user['points'] >= $avatar['min'] && $user['points'] <= $avatar['max']) {
            $currentAvatar = $avatar;
            break;
        }
    }
    ?>
    <div class="dashboard-stats-row">
        <div class="stat-card c-green">
            <div class="stat-icon" style="font-size: 3rem; background: none;"><?= $currentAvatar['icon'] ?></div>
            <div>
                <span class="stat-value"><?= $currentAvatar['name'] ?> Écolo</span>
                <span class="stat-label">Votre avatar</span>
            </div>
        </div>
        <div class="stat-card c-purple">
            <div class="stat-icon"><i class="fas fa-star"></i></div>
            <div>
                <span class="stat-value"><?= count($userBadgeIds) ?>/<?= count($allBadges) ?></span>
                <span class="stat-label">Badges débloqués</span>
            </div>
        </div>
        <div class="stat-card c-teal">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div>
                <span class="stat-value"><?= number_format($user['points']) ?> pts</span>
                <span class="stat-label">Points</span>
            </div>
        </div>
    </div>
    
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-medal" style="color: var(--green);"></i> Collection de badges</h3>
        </div>
        <div class="admin-content">
            <div class="stats-grid" style="grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));">
                <?php foreach ($allBadges as $badge): 
                    $earned = in_array($badge['badge_id'], $userBadgeIds);
                ?>
                <div class="stat-card <?= $earned ? 'c-green' : 'c-dark' ?>" style="opacity: <?= $earned ? '1' : '0.6' ?>;">
                    <div class="stat-icon" style="font-size: 2.5rem; background: none;"><?= $badge['icon'] ?></div>
                    <div>
                        <span class="stat-value" style="font-size: 1rem;"><?= $badge['name'] ?></span>
                        <span class="stat-label"><?= $badge['description'] ?></span>
                        <span class="stat-label" style="color: var(--warning);">⭐ <?= $badge['points_required'] ?> pts</span>
                        <span class="stat-label"><?= $earned ? '✓ Débloqué' : '🔒 Verrouillé' ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>