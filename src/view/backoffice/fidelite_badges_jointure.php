<div class="products-section">
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-link" style="color: var(--green);"></i> Programme Fidélité</h3>
        </div>
        <div class="admin-content">
            
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Titre</th>
                            <th>Points</th>
                            <th>Date</th>
                            <th>Badges débloqués</th>
                            <th>Nb Badges</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($fidelites)): ?>
                            <tr><td colspan="8" style="text-align: center;">Aucune donnée</td></tr>
                        <?php else: ?>
                            <?php foreach ($fidelites as $f): ?>
                                <tr>
                                    <td><?= $f['idPoints'] ?></td>
                                    <td><strong><?= htmlspecialchars($f['user_nom'] ?? 'N/A') ?></strong></td>
                                    <td><?= htmlspecialchars($f['user_email'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($f['titre']) ?></td>
                                    <td class="tbl-price">⭐ <?= number_format($f['total']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($f['dateGain'])) ?></td>
                                    <td><?= htmlspecialchars($f['badges'] ?? 'Aucun badge') ?></td>
                                    <td>🏅 <?= $f['nb_badges'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="admin-section" style="margin-top: 2rem; background: #0f172a;">
                <div class="admin-header">
                    <h3><i class="fas fa-database"></i> Détail de la requête</h3>
                </div>
                <div class="admin-content">
                    <pre style="background: #0a0f1e; padding: 1rem; border-radius: 8px; overflow-x: auto; color: #f1f5f9;">
SELECT f.*, u.nom, u.email,
       GROUP_CONCAT(DISTINCT b.nom SEPARATOR ', ') as badges,
       COUNT(DISTINCT b.badge_id) as nb_badges
FROM fidelite f
LEFT JOIN utilisateur u ON f.user_id = u.id
LEFT JOIN user_badges ub ON u.id = ub.user_id
LEFT JOIN badge b ON ub.badge_id = b.badge_id
GROUP BY f.idPoints
ORDER BY f.dateGain DESC</pre>
                    <p style="margin-top: 1rem; color: #8b9ab5;">
                        <i class="fas fa-info-circle"></i> Cette vue affiche les offres de fidélité avec les informations de l'utilisateur 
                        et la liste des badges qu'il a débloqués.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>