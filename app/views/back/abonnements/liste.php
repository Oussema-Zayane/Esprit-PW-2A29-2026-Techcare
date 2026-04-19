<?php include __DIR__ . '/../layout/header.php'; ?>
<?php include __DIR__ . '/../layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <div class="page-title">
            <h1><i class="fa-solid fa-tags"></i> Gestion des Abonnements</h1>
            <p>Liste complète des abonnements</p>
        </div>
        <div class="user-info">
            <div class="user-avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <span>Admin</span>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-info">
                <h3><?= count($abonnements) ?></h3>
                <p>Total</p>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-tags"></i></div>
        </div>
        <div class="stat-card green">
            <div class="stat-info">
                <h3><?= count(array_filter($abonnements, fn($a) => $a['statut'] == 'actif')) ?></h3>
                <p>Actifs</p>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
        </div>
        <div class="stat-card orange">
            <div class="stat-info">
                <h3><?= count(array_filter($abonnements, fn($a) => $a['statut'] == 'suspendu')) ?></h3>
                <p>Suspendus</p>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-pause"></i></div>
        </div>
        <div class="stat-card red">
            <div class="stat-info">
                <h3><?= count(array_filter($abonnements, fn($a) => $a['statut'] == 'expiré' || $a['statut'] == 'annulé')) ?></h3>
                <p>Expirés/Annulés</p>
            </div>
            <div class="stat-icon"><i class="fa-solid fa-calendar-xmark"></i></div>
        </div>
    </div>

    <!-- Barre de recherche et bouton ajouter -->
    <div class="table-header">
        <div class="search-box">
            <i class="fa-solid fa-search"></i>
            <input type="text" id="searchInput" placeholder="Rechercher un client, email ou type...">
            <button id="clearSearch" class="clear-btn" style="display: none;"><i class="fa-solid fa-times"></i></button>
        </div>
        <a href="index.php?action=ajouter" class="btn-add">
            <i class="fa-solid fa-plus"></i> Ajouter un abonnement
        </a>
    </div>

    <!-- Tableau avec scroll horizontal -->
    <div class="table-wrapper">
        <div class="table-container">
            <table id="abonnementsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Prix</th>
                        <th>Date début</th>
                        <th>Date fin</th>
                        <th>Statut</th>
                        <th class="actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($abonnements)): ?>
                        <tr class="empty-row">
                            <td colspan="9" class="text-center">
                                <i class="fa-solid fa-inbox" style="font-size: 48px; opacity: 0.5; display: block; margin-bottom: 10px;"></i>
                                Aucun abonnement trouvé
                                <br>
                                <a href="index.php?action=ajouter" style="color: var(--green); margin-top: 10px; display: inline-block;">+ Ajouter un abonnement</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($abonnements as $a): ?>
                        <tr>
                            <td data-label="ID">#<?= $a['id'] ?></td>
                            <td data-label="Client"><strong><?= htmlspecialchars($a['nom_client']) ?></strong></td>
                            <td data-label="Email"><?= htmlspecialchars($a['email'] ?? 'Non renseigné') ?></td>
                            <td data-label="Type">
                                <span class="type-badge type-<?= $a['type_abonnement'] ?>">
                                    <?= ucfirst($a['type_abonnement']) ?>
                                </span>
                            </td>
                            <td data-label="Prix" class="price"><?= number_format($a['prix'], 2) ?> DT</td>
                            <td data-label="Date début"><?= date('d/m/Y', strtotime($a['date_debut'])) ?></td>
                            <td data-label="Date fin"><?= date('d/m/Y', strtotime($a['date_fin'])) ?></td>
                            <td data-label="Statut">
                                <span class="badge badge-<?= $a['statut'] ?>">
                                    <?= $a['statut'] ?>
                                </span>
                            </td>
                            <td data-label="Actions" class="actions-cell">
                                <div class="action-buttons">
                                    <a href="index.php?action=detail&id=<?= $a['id'] ?>" class="btn-view" title="Voir">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="index.php?action=modifier&id=<?= $a['id'] ?>" class="btn-edit" title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="index.php?action=supprimer&id=<?= $a['id'] ?>" 
                                       class="btn-delete" 
                                       title="Supprimer"
                                       onclick="return confirm('Supprimer cet abonnement ?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
/* Styles pour les statistiques */
.stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 25px;
}

.stat-card {
    background: var(--card);
    border-radius: 12px;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 1px solid var(--border);
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-3px);
}

.stat-info h3 {
    font-size: 28px;
    margin: 0;
    color: var(--txt);
}

.stat-info p {
    margin: 5px 0 0;
    color: var(--txt-muted);
    font-size: 13px;
}

.stat-icon {
    width: 48px;
    height: 48px;
    background: rgba(0,168,107,0.1);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: var(--green);
}

.stat-card.green .stat-icon { background: rgba(46,204,113,0.1); color: #2ecc71; }
.stat-card.orange .stat-icon { background: rgba(243,156,18,0.1); color: #f39c12; }
.stat-card.red .stat-icon { background: rgba(231,76,60,0.1); color: #e74c3c; }

/* Styles pour le tableau avec scroll */
.table-wrapper {
    overflow-x: auto;
    border-radius: 12px;
    margin-top: 20px;
}

.table-container {
    min-width: 1000px;
    background: var(--card);
    border-radius: 12px;
    border: 1px solid var(--border);
    overflow: hidden;
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 20px;
}

.search-box {
    display: flex;
    align-items: center;
    gap: 10px;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 10px 15px;
    min-width: 280px;
    position: relative;
}

.search-box i {
    color: var(--txt-dim);
}

.search-box input {
    background: none;
    border: none;
    outline: none;
    color: var(--txt);
    width: 100%;
    font-size: 14px;
}

.search-box input::placeholder {
    color: var(--txt-dim);
}

.clear-btn {
    background: none;
    border: none;
    color: var(--txt-dim);
    cursor: pointer;
    padding: 0 5px;
    transition: color 0.2s;
}

.clear-btn:hover {
    color: var(--danger);
}

.btn-add {
    background: var(--green);
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    transition: all 0.3s;
}

.btn-add:hover {
    background: var(--green-dk);
    transform: translateY(-2px);
}

/* Tableau */
#abonnementsTable {
    width: 100%;
    border-collapse: collapse;
}

#abonnementsTable th {
    padding: 14px 12px;
    text-align: left;
    background: rgba(0,0,0,0.2);
    color: var(--txt-muted);
    font-weight: 600;
    font-size: 13px;
    border-bottom: 1px solid var(--border);
}

#abonnementsTable td {
    padding: 12px;
    border-bottom: 1px solid var(--border);
    color: var(--txt);
    font-size: 14px;
}

#abonnementsTable tr:last-child td {
    border-bottom: none;
}

#abonnementsTable tbody tr:hover td {
    background: rgba(255,255,255,0.03);
}

/* Colonne actions */
.actions-col {
    width: 120px;
    text-align: center;
}

.actions-cell {
    text-align: center;
}

.action-buttons {
    display: flex;
    gap: 6px;
    justify-content: center;
    flex-wrap: wrap;
}

/* Boutons d'action */
.btn-view, .btn-edit, .btn-delete {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
    transition: all 0.2s;
    cursor: pointer;
    min-width: 36px;
}

.btn-view {
    background: rgba(52,152,219,0.15);
    color: #3498db;
    border: 1px solid rgba(52,152,219,0.3);
}

.btn-view:hover {
    background: #3498db;
    color: white;
    transform: translateY(-2px);
}

.btn-edit {
    background: rgba(243,156,18,0.15);
    color: #f39c12;
    border: 1px solid rgba(243,156,18,0.3);
}

.btn-edit:hover {
    background: #f39c12;
    color: white;
    transform: translateY(-2px);
}

.btn-delete {
    background: rgba(231,76,60,0.15);
    color: #e74c3c;
    border: 1px solid rgba(231,76,60,0.3);
}

.btn-delete:hover {
    background: #e74c3c;
    color: white;
    transform: translateY(-2px);
}

.btn-view:active, .btn-edit:active, .btn-delete:active {
    color: black !important;
    transform: scale(0.95);
}

/* Badges */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.badge-actif {
    background: rgba(46,204,113,0.15);
    color: #2ecc71;
    border: 1px solid rgba(46,204,113,0.3);
}

.badge-suspendu {
    background: rgba(243,156,18,0.15);
    color: #f39c12;
    border: 1px solid rgba(243,156,18,0.3);
}

.badge-expiré, .badge-annulé {
    background: rgba(231,76,60,0.15);
    color: #e74c3c;
    border: 1px solid rgba(231,76,60,0.3);
}

/* Type badges */
.type-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.type-mensuel {
    background: rgba(8,145,178,0.15);
    color: #22d3ee;
}

.type-annuel {
    background: rgba(124,58,237,0.15);
    color: #a78bfa;
}

.type-ponctuel {
    background: rgba(234,88,12,0.15);
    color: #fb923c;
}

.price {
    color: var(--green);
    font-weight: bold;
}

.text-center {
    text-align: center;
    padding: 40px !important;
    color: var(--txt-muted);
}

.empty-row td {
    text-align: center;
    padding: 40px !important;
}

/* Responsive pour mobile */
@media (max-width: 768px) {
    .stats-row {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .table-header {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-box {
        width: 100%;
        min-width: auto;
    }
    
    .btn-add {
        text-align: center;
        justify-content: center;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 5px;
    }
    
    .btn-view, .btn-edit, .btn-delete {
        width: 100%;
        justify-content: center;
    }
    
    .actions-col {
        width: 100px;
    }
}

@media (max-width: 480px) {
    .stats-row {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Recherche en temps réel
const searchInput = document.getElementById('searchInput');
const clearBtn = document.getElementById('clearSearch');

function filterTable() {
    let filter = searchInput.value.toLowerCase();
    let rows = document.querySelectorAll('#abonnementsTable tbody tr');
    let hasResults = false;
    
    rows.forEach(row => {
        if (row.classList.contains('empty-row')) return;
        let text = row.textContent.toLowerCase();
        let isVisible = text.includes(filter);
        row.style.display = isVisible ? '' : 'none';
        if (isVisible) hasResults = true;
    });
    
    // Afficher/masquer le bouton clear
    clearBtn.style.display = filter.length > 0 ? 'block' : 'none';
    
    // Afficher un message si aucun résultat
    let noResultRow = document.querySelector('#abonnementsTable tbody tr.no-result');
    if (!hasResults && !document.querySelector('.empty-row')) {
        if (!noResultRow) {
            const tbody = document.querySelector('#abonnementsTable tbody');
            const tr = document.createElement('tr');
            tr.className = 'no-result';
            tr.innerHTML = '<td colspan="9" class="text-center">Aucun résultat trouvé</td>';
            tbody.appendChild(tr);
        }
    } else if (noResultRow) {
        noResultRow.remove();
    }
}

searchInput.addEventListener('keyup', filterTable);

// Bouton clear
clearBtn.addEventListener('click', function() {
    searchInput.value = '';
    filterTable();
    searchInput.focus();
});

// Initialiser le bouton clear
if (searchInput.value.length > 0) {
    clearBtn.style.display = 'block';
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>