<?php include __DIR__ . '/layout/header.php'; ?>
<?php include __DIR__ . '/layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <div class="page-title">
            <h1><i class="fa-solid fa-magnifying-glass"></i> Recherche Avancée</h1>
            <p>Recherche d'abonnements par critères multiples</p>
        </div>
        <div class="user-info">
            <div class="user-avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <span>Admin</span>
        </div>
    </div>

    <!-- Formulaire de recherche avancée -->
    <div class="search-card">
        <div class="search-card-header">
            <i class="fa-solid fa-sliders-h"></i>
            <h3>Filtres de recherche</h3>
        </div>
        
        <form id="formRecherche" class="search-form" action="index.php?action=recherche" method="get" novalidate>
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fa-solid fa-tag"></i> Type d'abonnement</label>
                    <select name="type_abonnement" id="type_abonnement">
                        <option value="">-- Tous les types --</option>
                        <?php if (!empty($types)): ?>
                            <?php foreach ($types as $type): ?>
                                <option value="<?= htmlspecialchars($type['nom_type']) ?>" 
                                        data-prix="<?= $type['prix_base'] ?>"
                                        data-duree="<?= $type['duree_jours'] ?>"
                                        data-description="<?= htmlspecialchars($type['description']) ?>"
                                        <?= (isset($_GET['type_abonnement']) && $_GET['type_abonnement'] == $type['nom_type']) ? 'selected' : '' ?>>
                                    <?= ucfirst($type['nom_type']) ?> - <?= number_format($type['prix_base'], 2) ?> DT
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>Aucun type disponible</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-user"></i> Nom du client</label>
                    <input type="text" name="nom_client" id="nom_client" placeholder="Rechercher par nom..." value="<?= htmlspecialchars($_GET['nom_client'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fa-solid fa-envelope"></i> Email</label>
                    <input type="text" name="email" id="email" placeholder="Rechercher par email..." value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-calendar"></i> Date début</label>
                    <input type="date" name="date_debut" id="date_debut" value="<?= htmlspecialchars($_GET['date_debut'] ?? '') ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label><i class="fa-solid fa-coins"></i> Prix (DT)</label>
                    <input type="number" name="prix" id="prix" step="0.01" placeholder="Prix" value="<?= htmlspecialchars($_GET['prix'] ?? '') ?>">
                    <small class="hint">Le prix se remplit automatiquement selon le type sélectionné</small>
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-calendar-week"></i> Date fin</label>
                    <input type="date" name="date_fin" id="date_fin" value="<?= htmlspecialchars($_GET['date_fin'] ?? '') ?>">
                    <small class="hint">La date de fin se calcule automatiquement</small>
                </div>
            </div>

            <div class="form-actions">
                <a href="index.php?action=recherche" class="btn-secondary">
                    <i class="fa-solid fa-eraser"></i> Réinitialiser
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-search"></i> Rechercher
                </button>
            </div>
        </form>
    </div>

    <!-- Carte d'information du type sélectionné -->
    <div id="typeInfo" class="type-info-card" style="display: none;">
        <div class="type-info-header">
            <i class="fa-solid fa-circle-info"></i>
            <span>Informations du type sélectionné</span>
        </div>
        <div class="type-info-content">
            <div class="info-item">
                <span class="info-label">📦 Type :</span>
                <span class="info-value" id="infoType">-</span>
            </div>
            <div class="info-item">
                <span class="info-label">💰 Prix :</span>
                <span class="info-value" id="infoPrixBase">-</span>
            </div>
            <div class="info-item">
                <span class="info-label">⏱️ Durée :</span>
                <span class="info-value" id="infoDuree">-</span>
            </div>
            <div class="info-item">
                <span class="info-label">📅 Date fin estimée :</span>
                <span class="info-value" id="infoDateFin">-</span>
            </div>
            <div class="info-item">
                <span class="info-label">📝 Description :</span>
                <span class="info-value" id="infoDescription">-</span>
            </div>
        </div>
    </div>

    <!-- ===== RÉSULTATS ===== -->
    <div class="results-card">
        <div class="results-header">
            <div class="results-header-left">
                <h3><i class="fa-solid fa-list"></i> Résultats de la recherche</h3>
                <p>
                    <?php 
                        $nbResultats = isset($abonnements) ? count($abonnements) : 0;
                        $hasFilters = !empty($_GET['type_abonnement']) || !empty($_GET['nom_client']) || 
                                      !empty($_GET['email']) || !empty($_GET['date_debut']) || 
                                      !empty($_GET['prix']) || !empty($_GET['date_fin']);
                    ?>
                    <?php if ($hasFilters): ?>
                        <span class="badge-count"><?= $nbResultats ?></span> résultat(s) pour votre recherche
                    <?php else: ?>
                        <span class="badge-count"><?= $nbResultats ?></span> abonnement(s) au total
                    <?php endif; ?>
                </p>
            </div>
            <?php if ($hasFilters): ?>
            <a href="index.php?action=recherche" class="btn-reset-sm">
                <i class="fa-solid fa-xmark"></i> Effacer les filtres
            </a>
            <?php endif; ?>
        </div>
        
        <div class="table-wrapper">
            <div class="table-container">
                <table class="results-table">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th><i class="fa-solid fa-user"></i> Client</th>
                            <th><i class="fa-solid fa-envelope"></i> Email</th>
                            <th><i class="fa-solid fa-tag"></i> Type</th>
                            <th><i class="fa-solid fa-coins"></i> Prix</th>
                            <th><i class="fa-solid fa-calendar-plus"></i> Début</th>
                            <th><i class="fa-solid fa-calendar-minus"></i> Fin</th>
                            <th><i class="fa-solid fa-circle-dot"></i> Statut</th>
                            <th><i class="fa-solid fa-list-check"></i> Options</th>
                            <th><i class="fa-solid fa-gear"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($abonnements) && !empty($abonnements)): ?>
                            <?php foreach ($abonnements as $index => $a): ?>
                            <tr>
                                <td class="id-badge">#<?= $a['id'] ?></td>
                                <td>
                                    <div class="client-cell">
                                        <div class="client-avatar"><?= strtoupper(substr($a['nom_client'], 0, 1)) ?></div>
                                        <strong><?= htmlspecialchars($a['nom_client']) ?></strong>
                                    </div>
                                </td>
                                <td class="email-text"><?= htmlspecialchars($a['email'] ?? 'Non renseigné') ?></td>
                                <td>
                                    <span class="type-badge type-<?= strtolower($a['type_abonnement']) ?>">
                                        <?= ucfirst($a['type_abonnement']) ?>
                                    </span>
                                </td>
                                <td class="price-text"><?= number_format($a['prix'], 2) ?> DT</td>
                                <td><?= date('d/m/Y', strtotime($a['date_debut'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($a['date_fin'])) ?></td>
                                <td>
                                    <span class="badge badge-<?= $a['statut'] ?>">
                                        <?= ucfirst($a['statut']) ?>
                                    </span>
                                </td>
                                <!-- Colonne OPTIONS -->
                                <td class="options-cell">
                                    <?php if (isset($a['options']) && !empty($a['options'])): ?>
                                        <ul class="options-list">
                                            <?php foreach ($a['options'] as $opt): ?>
                                                <li title="<?= htmlspecialchars($opt['description']) ?>">
                                                    <i class="fa-solid fa-plus-circle"></i>
                                                    <?= htmlspecialchars(mb_substr($opt['description'], 0, 30)) ?>
                                                    <?php if (mb_strlen($opt['description']) > 30): ?>...<?php endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <span class="no-options">Aucune option</span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions-cell">
                                    <div class="action-buttons">
                                        <a href="index.php?action=detail&id=<?= $a['id'] ?>" class="btn-view" title="Voir">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="index.php?action=modifier&id=<?= $a['id'] ?>" class="btn-edit" title="Modifier">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="empty-state">
                                    <i class="fa-solid fa-magnifying-glass" style="font-size: 40px; display: block; margin-bottom: 12px;"></i>
                                    <strong>Aucun abonnement trouvé</strong>
                                    <?php if ($hasFilters): ?>
                                        <br><small>Essayez d'élargir vos critères de recherche</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if ($nbResultats > 0): ?>
        <div class="results-footer">
            <span><i class="fa-solid fa-circle-info"></i> Affichage de <?= $nbResultats ?> enregistrement(s)</span>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* ===== FORMULAIRE DE RECHERCHE (styles existants) ===== */
.search-card {
    background: var(--card);
    border-radius: 16px;
    border: 1px solid var(--border);
    overflow: hidden;
    margin-bottom: 25px;
}

.search-card-header {
    background: linear-gradient(135deg, rgba(0,168,107,0.1), rgba(0,168,107,0.05));
    padding: 15px 20px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 10px;
}

.search-card-header i {
    font-size: 1.2rem;
    color: var(--green);
}

.search-card-header h3 {
    font-size: 0.95rem;
    color: var(--txt);
    margin: 0;
}

.search-form {
    padding: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 15px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.form-group label {
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--txt-muted);
    display: flex;
    align-items: center;
    gap: 6px;
}

.form-group label i {
    color: var(--green);
    font-size: 0.75rem;
}

.form-group input, .form-group select {
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 10px 12px;
    color: var(--txt);
    font-size: 0.85rem;
    transition: all 0.3s;
}

.form-group input:focus, .form-group select:focus {
    outline: none;
    border-color: var(--green);
    box-shadow: 0 0 0 2px rgba(0,168,107,0.1);
}

.hint {
    font-size: 0.7rem;
    color: var(--txt-dim);
    margin-top: 4px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid var(--border);
}

.btn-primary {
    background: linear-gradient(135deg, var(--green), var(--green-dk));
    color: white;
    padding: 10px 25px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
    font-size: 0.85rem;
    font-weight: 600;
}

.btn-primary:hover {
    transform: translateY(-2px);
    filter: brightness(1.05);
}

.btn-secondary {
    background: var(--surface2);
    color: var(--txt-muted);
    padding: 10px 25px;
    border-radius: 8px;
    border: 1px solid var(--border);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
    text-decoration: none;
    font-size: 0.85rem;
}

.btn-secondary:hover {
    background: var(--surface3);
    color: var(--txt);
}

/* ===== CARTE D'INFO TYPE ===== */
.type-info-card {
    background: #ffffff;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 15px 20px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    animation: slideDown 0.3s ease;
}

.type-info-header {
    font-weight: 600;
    margin-bottom: 12px;
    color: #00A86B;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
}

.type-info-content {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
    font-size: 0.8rem;
}

.info-label {
    color: #6c757d;
    font-size: 0.7rem;
    font-weight: 500;
}

.info-value {
    font-weight: 600;
    color: #212529;
    word-break: break-word;
}

/* ===== RÉSULTATS ===== */
.results-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e0e0e0;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.results-header {
    background: #f8f9fa;
    padding: 16px 22px;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}

.results-header-left h3 {
    font-size: 0.95rem;
    color: #333333;
    margin: 0 0 4px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.results-header-left h3 i {
    color: #00A86B;
}

.results-header-left p {
    font-size: 0.8rem;
    color: #666666;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}

.badge-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #00A86B, #008f5a);
    color: #fff;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 20px;
    min-width: 24px;
}

.btn-reset-sm {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    padding: 6px 14px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.78rem;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
    white-space: nowrap;
}

.btn-reset-sm:hover {
    background: #e74c3c;
    color: #fff;
    border-color: #e74c3c;
}

/* TABLE */
.table-wrapper {
    overflow-x: auto;
}

.table-container {
    min-width: 1000px;
}

.results-table {
    width: 100%;
    border-collapse: collapse;
}

.results-table thead tr {
    background: #f1f3f5;
}

.results-table th {
    padding: 13px 14px;
    text-align: left;
    color: #555555;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #dee2e6;
    white-space: nowrap;
}

.results-table th i {
    margin-right: 4px;
    color: #00A86B;
    font-size: 10px;
}

.results-table tbody tr {
    transition: background 0.2s;
}

.results-table tbody tr:hover {
    background: #f8f9fa;
}

.results-table td {
    padding: 11px 14px;
    border-bottom: 1px solid #e9ecef;
    color: #212529;
    font-size: 13px;
    vertical-align: middle;
}

/* ID badge */
.id-badge {
    font-family: 'Courier New', monospace;
    font-size: 12px;
    color: #6c757d;
    font-weight: 600;
}

/* Client cell */
.client-cell {
    display: flex;
    align-items: center;
    gap: 9px;
}

.client-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: linear-gradient(135deg, #00A86B, #008f5a);
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

/* Email */
.email-text {
    font-size: 12px;
    color: #6c757d;
}

/* Prix */
.price-text {
    color: #28a745;
    font-weight: 700;
    font-size: 13px;
}

/* Type badges */
.type-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.3px;
}
.type-mensuel  { background: #e3f2fd; color: #1565c0; border: 1px solid #bbdef5; }
.type-annuel   { background: #f3e5f5; color: #6a1b9a; border: 1px solid #e1bee7; }
.type-ponctuel { background: #fff3e0; color: #e65100; border: 1px solid #ffe0b2; }

/* Statut badges */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}
.badge-actif    { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.badge-suspendu { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
.badge-expiré,
.badge-annulé   { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

/* Options styles */
.options-cell {
    max-width: 200px;
}
.options-list {
    list-style: none;
    margin: 0;
    padding: 0;
}
.options-list li {
    font-size: 0.7rem;
    padding: 3px 0;
    color: #555;
    display: flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.options-list li i {
    color: #00A86B;
    font-size: 0.65rem;
    flex-shrink: 0;
}
.no-options {
    font-size: 0.7rem;
    color: #999;
    font-style: italic;
}

/* Actions */
.actions-cell { text-align: center; }

.action-buttons {
    display: flex;
    gap: 6px;
    justify-content: center;
}

.btn-view, .btn-edit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 7px;
    text-decoration: none;
    font-size: 12px;
    transition: all 0.2s;
}
.btn-view {
    background: #e3f2fd;
    color: #1976d2;
    border: 1px solid #bbdef5;
}
.btn-view:hover { background: #1976d2; color: #fff; }

.btn-edit {
    background: #fff3e0;
    color: #f57c00;
    border: 1px solid #ffe0b2;
}
.btn-edit:hover { background: #f57c00; color: #fff; }

/* Empty state */
.empty-state {
    text-align: center;
    padding: 50px 20px !important;
    color: #6c757d !important;
    font-size: 14px;
}

/* Footer */
.results-footer {
    padding: 12px 22px;
    border-top: 1px solid #e0e0e0;
    font-size: 0.75rem;
    color: #6c757d;
    display: flex;
    align-items: center;
    gap: 6px;
}

.results-footer i {
    color: #00A86B;
    font-size: 11px;
}

/* Animations */
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* Responsive */
@media (max-width: 768px) {
    .form-row { grid-template-columns: 1fr; }
    .form-actions { flex-direction: column; }
    .btn-primary, .btn-secondary { justify-content: center; width: 100%; }
    .type-info-content { grid-template-columns: 1fr; }
    .results-header { flex-direction: column; align-items: flex-start; }
    .options-list li { white-space: normal; }
}
</style>

<script>
// Données des types depuis PHP
const typesData = <?php 
    $typesArray = [];
    foreach ($types as $type) {
        $typesArray[htmlspecialchars($type['nom_type'])] = [
            'nom_type'    => $type['nom_type'],
            'prix_base'   => $type['prix_base'],
            'duree_jours' => $type['duree_jours'],
            'description' => $type['description'] ?? ''
        ];
    }
    echo json_encode($typesArray);
?>;

function calculerDateFin(dateDebut, dureeJours) {
    if (!dateDebut) return '';
    const date = new Date(dateDebut);
    date.setDate(date.getDate() + parseInt(dureeJours));
    return date.toISOString().split('T')[0];
}

function autoFillFields() {
    const typeSelect  = document.getElementById('type_abonnement');
    const selectedType = typeSelect.value;
    const prixInput   = document.getElementById('prix');
    const dateFinInput = document.getElementById('date_fin');
    const dateDebut   = document.getElementById('date_debut');
    const typeInfoDiv = document.getElementById('typeInfo');
    
    if (selectedType && typesData[selectedType]) {
        const type       = typesData[selectedType];
        const prixBase   = type.prix_base;
        const dureeJours = type.duree_jours;
        
        prixInput.value = prixBase;
        prixInput.style.borderColor = '#00A86B';
        
        if (!dateDebut.value) {
            dateDebut.value = new Date().toISOString().split('T')[0];
        }
        
        const dateFin = calculerDateFin(dateDebut.value, dureeJours);
        if (dateFin) {
            dateFinInput.value = dateFin;
            dateFinInput.style.borderColor = '#00A86B';
        }
        
        document.getElementById('infoType').textContent        = type.nom_type;
        document.getElementById('infoPrixBase').textContent    = parseFloat(prixBase).toFixed(2) + ' DT';
        document.getElementById('infoDuree').textContent       = dureeJours + ' jours';
        document.getElementById('infoDateFin').textContent     = dateFin || '-';
        document.getElementById('infoDescription').textContent = type.description || 'Aucune description';
        typeInfoDiv.style.display = 'block';
    } else {
        typeInfoDiv.style.display  = 'none';
        prixInput.style.borderColor = '';
        dateFinInput.style.borderColor = '';
    }
}

document.getElementById('date_debut').addEventListener('change', function () {
    const selectedType = document.getElementById('type_abonnement').value;
    if (selectedType && typesData[selectedType] && this.value) {
        const dateFin = calculerDateFin(this.value, typesData[selectedType].duree_jours);
        document.getElementById('date_fin').value = dateFin;
        document.getElementById('infoDateFin').textContent = dateFin;
    }
});

document.getElementById('type_abonnement').addEventListener('change', autoFillFields);

document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('type_abonnement').value) {
        autoFillFields();
    }
});
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>