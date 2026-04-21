<div class="products-section">
    <div class="dashboard-stats-row">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-star"></i></div>
            <div>
                <span class="stat-value" id="userPoints"><?= $points ?? 1250 ?></span>
                <span class="stat-label">Points</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div>
                <span class="stat-value" id="userLevel">Niveau <?= $level ?? 2 ?></span>
                <span class="stat-label">Niveau</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-medal"></i></div>
            <div>
                <span class="stat-value" id="badgesCount"><?= $badgesCount ?? 3 ?></span>
                <span class="stat-label">Badges</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-leaf"></i></div>
            <div>
                <span class="stat-value" id="actionsCount"><?= $actionsCount ?? 15 ?></span>
                <span class="stat-label">Actions</span>
            </div>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-bolt" style="color: #00A86B;"></i> Gagnez des points</h3>
        </div>
        <div class="admin-content">
            <form method="POST" action="index.php?controller=gamification&action=addPoints" style="display: inline-block; margin: 5px;">
                <input type="hidden" name="points" value="50">
                <button type="submit" class="btn-primary">🔧 Réparer (+50)</button>
            </form>
            <form method="POST" action="index.php?controller=gamification&action=addPoints" style="display: inline-block; margin: 5px;">
                <input type="hidden" name="points" value="100">
                <button type="submit" class="btn-primary">♻️ Recycler (+100)</button>
            </form>
            <form method="POST" action="index.php?controller=gamification&action=addPoints" style="display: inline-block; margin: 5px;">
                <input type="hidden" name="points" value="25">
                <button type="submit" class="btn-primary">📝 Avis (+25)</button>
            </form>
            <form method="POST" action="index.php?controller=gamification&action=addPoints" style="display: inline-block; margin: 5px;">
                <input type="hidden" name="points" value="200">
                <button type="submit" class="btn-primary">👥 Parrainer (+200)</button>
            </form>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-chart-line" style="color: #00A86B;"></i> Prochain badge</h3>
        </div>
        <div class="admin-content">
            <p id="nextBadgeText">Ambassadeur - 1000 points</p>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill" style="width: <?= $progress ?? 75 ?>%"></div>
            </div>
            <p id="progressText"><?= $points ?? 1250 ?>/1000 points (<?= $progress ?? 75 ?>%)</p>
        </div>
    </div>

    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-history" style="color: #00A86B;"></i> Historique récent</h3>
            <a href="index.php?controller=gamification&action=points" class="btn-primary" style="padding: 0.3rem 0.8rem; font-size: 0.8rem;">Voir tout</a>
        </div>
        <div class="admin-content">
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr><th>Date</th><th>Action</th><th>Points</th></tr>
                    </thead>
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
        document.getElementById('userPoints').innerHTML = data.points;
        document.getElementById('userLevel').innerHTML = `Niveau ${data.level}`;
        document.getElementById('badgesCount').innerHTML = data.badges.length;
        document.getElementById('actionsCount').innerHTML = data.actions;
        
        let progress = Math.min(100, (data.points / 1000) * 100);
        document.getElementById('progressFill').style.width = progress + '%';
        document.getElementById('progressText').innerHTML = `${data.points}/1000 points (${progress.toFixed(1)}%)`;
        
        let html = '';
        for(let h of data.history) {
            html += `<tr><td>${h.date}</td><td>${h.action}</td><td class="tbl-price">+${h.points}</td></tr>`;
        }
        document.getElementById('historyBody').innerHTML = html || '<tr><td colspan="3">Aucun historique</td></tr>';
    });
</script>