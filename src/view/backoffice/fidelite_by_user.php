<div class="products-section">
    <div class="admin-section">
        <div class="admin-header">
            <h3><i class="fas fa-link" style="color: var(--green);"></i> Fidélité par utilisateur - Jointure SQL</h3>
        </div>
        <div class="admin-content">
            
            <!-- Formulaire de sélection -->
            <div class="form-search" style="background: #1a2535; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
                <form method="GET" action="index.php">
                    <input type="hidden" name="controller" value="fidelite">
                    <input type="hidden" name="action" value="showByUser">
                    
                    <div style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
                        <div style="flex: 2; min-width: 200px;">
                            <label for="userId" style="display: block; margin-bottom: 0.5rem; color: #00A86B;">
                                <i class="fas fa-user"></i> Sélectionner un utilisateur :
                            </label>
                            <select name="user_id" id="userId" class="form-control" style="width: 100%; padding: 0.75rem; background: #0f172a; border: 1px solid #334155; border-radius: 8px; color: white;">
                                <option value="">-- Tous les utilisateurs --</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user['id'] ?>" <?= ($selectedUserId == $user['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($user['nom']) ?> (<?= htmlspecialchars($user['email']) ?>) - <?= $user['nbPoints'] ?> points
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="btn-primary"><i class="fas fa-search"></i> Afficher</button>
                        </div>
                        <?php if ($selectedUserId): ?>
                            <div>
                                <a href="index.php?controller=fidelite&action=showByUser" class="btn-outline"><i class="fas fa-undo"></i> Réinitialiser</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <!-- Résultats de la jointure -->
            <div class="table-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Titre</th>
                            <th>Description</th>
                            <th>Points</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($fidelites)): ?>
                            <tr><td colspan="7" style="text-align: center; padding: 40px;">
                                <i class="fas fa-database"></i> Aucune donnée trouvée
                            </td></tr>
                        <?php else: ?>
                            <?php foreach ($fidelites as $f): ?>
                                <tr>
                                    <td><?= $f['idPoints'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($f['user_nom'] ?? 'N/A') ?></strong>
                                        <?php if ($f['user_id'] == ($_SESSION['user_id'] ?? 0)): ?>
                                            <span style="color: #00A86B; font-size: 0.7rem;">(Vous)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($f['user_email'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($f['titre']) ?></td>
                                    <td><?= htmlspecialchars(substr($f['description'], 0, 60)) ?>...</td>
                                    <td class="tbl-price">⭐ <?= number_format($f['total']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($f['dateGain'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Statistiques GROUP BY -->
            <div class="admin-section" style="margin-top: 2rem;">
                <div class="admin-header">
                    <h3><i class="fas fa-chart-bar" style="color: var(--green);"></i> Statistiques par utilisateur (GROUP BY)</h3>
                </div>
                <div class="admin-content">
                    <div class="table-scroll">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Utilisateur</th>
                                    <th>Email</th>
                                    <th>Nombre d'offres</th>
                                    <th>Points totaux offerts</th>
                                    <th>Dernière offre</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($statsByUser as $stat): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($stat['nom']) ?></strong></td>
                                        <td><?= htmlspecialchars($stat['email']) ?></td>
                                        <td><?= $stat['total_fidelites'] ?></td>
                                        <td class="tbl-price">⭐ <?= number_format($stat['total_points_offerts'] ?? 0) ?></td>
                                        <td><?= $stat['derniere_date'] ? date('d/m/Y', strtotime($stat['derniere_date'])) : 'Jamais' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Explication SQL -->
            <div class="admin-section" style="margin-top: 2rem; background: #0f172a;">
                <div class="admin-header">
                    <h3><i class="fas fa-database" style="color: var(--green);"></i> Explication de la jointure SQL</h3>
                </div>
                <div class="admin-content">
                    <div style="background: #0a0f1e; padding: 1rem; border-radius: 8px; overflow-x: auto;">
                        <pre style="color: #f1f5f9; margin: 0; font-family: monospace;">
<span style="color: #00A86B;">SELECT</span> f.*, u.nom <span style="color: #f59e0b;">as</span> user_nom, u.email <span style="color: #f59e0b;">as</span> user_email
<span style="color: #00A86B;">FROM</span> fidelite f
<span style="color: #00A86B;">LEFT JOIN</span> utilisateur u <span style="color: #00A86B;">ON</span> f.user_id = u.id
<span style="color: #00A86B;">ORDER BY</span> f.dateGain <span style="color: #00A86B;">DESC</span></pre>
                    </div>
                    <div style="margin-top: 1rem; padding: 1rem; background: #1a2535; border-radius: 8px;">
                        <h4 style="color: #00A86B; margin-bottom: 0.5rem;"><i class="fas fa-info-circle"></i> Explication</h4>
                        <p style="color: #8b9ab5; line-height: 1.6;">
                            Cette requête affiche toutes les offres de fidélité avec les informations de l'utilisateur associé 
                            (nom et email) grâce à une <strong>jointure LEFT JOIN</strong> entre les tables 
                            <code style="background: #0f172a; padding: 0.2rem 0.4rem; border-radius: 4px;">fidelite</code> et 
                            <code style="background: #0f172a; padding: 0.2rem 0.4rem; border-radius: 4px;">utilisateur</code>.
                        </p>
                        <ul style="margin-top: 0.5rem; color: #8b9ab5; margin-left: 1.5rem;">
                            <li><code>f.user_id = u.id</code> : Condition de jointure (clé étrangère)</li>
                            <li><code>LEFT JOIN</code> : Conserve toutes les offres même sans utilisateur</li>
                            <li><code>ORDER BY</code> : Trie par date décroissante</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-control:focus {
    outline: none;
    border-color: #00A86B;
    box-shadow: 0 0 0 2px rgba(0,168,107,0.2);
}
.btn-outline {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    background: transparent;
    color: #8b9ab5;
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font: 500 0.85rem 'Inter', sans-serif;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
}
.btn-outline:hover {
    border-color: #00A86B;
    color: #00A86B;
}
</style>