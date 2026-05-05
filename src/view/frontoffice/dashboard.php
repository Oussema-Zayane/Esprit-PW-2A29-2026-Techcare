<div class="products-section">
    <!-- ===================================================== -->
    <!-- STATISTIQUES PRINCIPALES -->
    <!-- ===================================================== -->
    <div class="dashboard-stats-row">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-star"></i></div>
            <div>
                <span class="stat-value" id="userPoints"><?= number_format($user['points']) ?></span>
                <span class="stat-label">Points</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div>
                <span class="stat-value" id="userLevel">Niveau <?= $user['level'] ?></span>
                <span class="stat-label">Niveau</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-medal"></i></div>
            <div>
                <span class="stat-value" id="badgesCount"><?= count($userBadges) ?></span>
                <span class="stat-label">Badges</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-leaf"></i></div>
            <div>
                <span class="stat-value" id="actionsCount"><?= $user['total_actions'] ?></span>
                <span class="stat-label">Actions</span>
            </div>
        </div>
    </div>
    
    <!-- ===================================================== -->
    <!-- CALENDRIER DE CONNEXION (STREAK) -->
    <!-- ===================================================== -->
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-calendar-check" style="color: var(--green);"></i> Calendrier de connexion</h3>
            <form method="POST" action="index.php?controller=gamification&action=enregistrerConnexion">
                <button type="submit" class="btn-primary"><i class="fas fa-check-circle"></i> Pointer ma connexion du jour</button>
            </form>
        </div>
        <div class="admin-content">
            <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
                <div style="text-align: center;">
                    <div style="font-size: 3rem; font-weight: bold; color: #00A86B;" id="streakDisplay"><?= $streak ?></div>
                    <div>jours consécutifs</div>
                </div>
                <div>
                    <strong>🎁 Bonus à débloquer :</strong>
                    <ul style="margin-top: 0.5rem; list-style: none;">
                        <li>🔥 3 jours ➜ +50 pts</li>
                        <li>🔥 7 jours ➜ +150 pts</li>
                        <li>🔥 14 jours ➜ +300 pts</li>
                        <li>🔥 30 jours ➜ +500 pts</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ===================================================== -->
    <!-- SYSTÈME DE PARRAINAGE -->
    <!-- ===================================================== -->
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-users" style="color: var(--green);"></i> Parrainage</h3>
            <a href="index.php?controller=gamification&action=parrainage" class="btn-outline">Gérer mes parrainages</a>
        </div>
        <div class="admin-content">
            <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
                <div>
                    <strong>Votre code de parrainage :</strong>
                    <div style="font-size: 1.5rem; font-family: monospace; background: #0f172a; padding: 0.5rem 1rem; border-radius: 8px; margin-top: 0.5rem;">
                        <?= $codeInfo['code'] ?>
                    </div>
                    <small>Partagez ce code à vos amis !</small>
                </div>
                <div>
                    <strong>📊 Vos statistiques :</strong>
                    <div style="margin-top: 0.5rem;">
                        <span style="font-size: 1.2rem; color: #00A86B;">👥 <?= $statsParrainage['nb_parrainages'] ?? 0 ?></span> parrainages
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- ===================================================== -->
    <!-- BOUTONS POUR GAGNER DES POINTS -->
    <!-- ===================================================== -->
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-bolt" style="color: var(--green);"></i> Gagnez des points</h3>
        </div>
        <div class="admin-content">
            <form method="POST" action="index.php?controller=gamification&action=addPoints" style="display: inline-block; margin: 5px;">
                <input type="hidden" name="points" value="50">
                <input type="hidden" name="action" value="🔧 Réparer un appareil">
                <button type="submit" class="btn-primary">🔧 Réparer (+50)</button>
            </form>
            <form method="POST" action="index.php?controller=gamification&action=addPoints" style="display: inline-block; margin: 5px;">
                <input type="hidden" name="points" value="100">
                <input type="hidden" name="action" value="♻️ Recycler un produit">
                <button type="submit" class="btn-primary">♻️ Recycler (+100)</button>
            </form>
            <form method="POST" action="index.php?controller=gamification&action=addPoints" style="display: inline-block; margin: 5px;">
                <input type="hidden" name="points" value="25">
                <input type="hidden" name="action" value="📝 Donner un avis">
                <button type="submit" class="btn-primary">📝 Avis (+25)</button>
            </form>
            <form method="POST" action="index.php?controller=gamification&action=addPoints" style="display: inline-block; margin: 5px;">
                <input type="hidden" name="points" value="200">
                <input type="hidden" name="action" value="👥 Parrainer un ami">
                <button type="submit" class="btn-primary">👥 Parrainer (+200)</button>
            </form>
        </div>
    </div>
    
    <!-- ===================================================== -->
    <!-- PROGRESSION VERS LE PROCHAIN BADGE -->
    <!-- ===================================================== -->
    <?php if ($nextBadge): ?>
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-chart-line" style="color: var(--green);"></i> Prochain badge</h3>
        </div>
        <div class="admin-content">
            <p><strong><?= $nextBadge['icon'] ?> <?= $nextBadge['name'] ?></strong> - <?= number_format($nextBadge['points_required']) ?> points requis</p>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill" style="width: <?= $progress ?>%"></div>
            </div>
            <p id="progressText"><?= number_format($user['points']) ?>/<?= number_format($nextBadge['points_required']) ?> points (<?= number_format($progress, 1) ?>%)</p>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- ===================================================== -->
    <!-- HISTORIQUE RÉCENT -->
    <!-- ===================================================== -->
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-history" style="color: var(--green);"></i> Historique récent</h3>
            <a href="index.php?controller=gamification&action=points" class="btn-primary" style="padding: 0.3rem 0.8rem; font-size: 0.8rem;">Voir tout</a>
        </div>
        <div class="admin-content">
            <div class="table-scroll">
                <table class="data-table">
                    <thead><tr><th>Date</th><th>Action</th><th>Points</th></tr></thead>
                    <tbody id="historyBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
fetch('index.php?controller=gamification&action=getUserData')
    .then(response => response.json())
    .then(data => {
        document.getElementById('userPoints').innerHTML = data.points.toLocaleString();
        document.getElementById('userLevel').innerHTML = `Niveau ${data.level}`;
        document.getElementById('badgesCount').innerHTML = data.badges.length;
        document.getElementById('actionsCount').innerHTML = data.actions;
        
        let progress = Math.min(100, (data.points / 1000) * 100);
        document.getElementById('progressFill').style.width = progress + '%';
        document.getElementById('progressText').innerHTML = `${data.points.toLocaleString()}/1000 points (${progress.toFixed(1)}%)`;
        
        let html = '';
        for(let h of data.history.slice(0, 5)) {
            html += `<tr><td>${h.date}</td><td>${h.action}</td><td class="tbl-price">+${h.points}</td></tr>`;
        }
        document.getElementById('historyBody').innerHTML = html || '<tr><td colspan="3">Aucun historique</td></tr>';
    })
    .catch(error => console.error('Erreur:', error));
</script>