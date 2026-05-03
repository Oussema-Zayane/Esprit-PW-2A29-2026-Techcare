<?php include __DIR__ . '/../layout/header.php'; ?>
<?php include __DIR__ . '/../layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <div class="page-title">
            <h1><i class="fa-solid fa-layer-group"></i> Gestion des Types d'Abonnement</h1>
            <p>Liste des types d'abonnement disponibles</p>
        </div>
        <div class="user-info">
            <div class="user-avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <span>Admin</span>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-check-circle"></i>
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- ═══ BANNIÈRE AVEC LIEN VERS OFFRES ═══ -->
    <div class="info-banner">
        <div class="info-banner-content">
            <i class="fa-solid fa-gift"></i>
            <span>Gérez également les <strong>options supplémentaires</strong> que vous pouvez ajouter aux abonnements.</span>
            <a href="index.php?action=offres" class="btn-sm-green">
                <i class="fa-solid fa-list-check"></i> Voir les offres
            </a>
            <a href="index.php?action=offres-ajouter" class="btn-sm-outline">
                <i class="fa-solid fa-plus"></i> Nouvelle offre
            </a>
        </div>
    </div>

    <div class="table-header">
        <div class="search-box">
            <i class="fa-solid fa-search"></i>
            <input type="text" id="searchInput" placeholder="Rechercher un type...">
        </div>
        <div class="header-buttons">
            <a href="index.php?action=types-ajouter" class="btn-add">
                <i class="fa-solid fa-plus"></i> Ajouter un type
            </a>
        </div>
    </div>

    <div class="table-wrapper">
        <div class="table-container">
            <table id="typesTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom du type</th>
                        <th>Prix de base</th>
                        <th>Durée (jours)</th>
                        <th>Icône</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($types)): ?>
                        <tr><td colspan="7" class="text-center">Aucun type trouvé</td><tr>
                    <?php else: ?>
                        <?php foreach ($types as $t): ?>
                            <tr>
                                <td>#<?= $t['id_type'] ?? $t['id'] ?? '-' ?></td>
                                <td><strong><?= htmlspecialchars($t['nom_type'] ?? '-') ?></strong></td>
                                <td><?= number_format($t['prix_base'] ?? 0, 2) ?> DT</td>
                                <td><?= $t['duree_jours'] ?? '-' ?> jours</td>
                                <td>
                                    <i class="fa-solid fa-<?= !empty($t['icone']) ? htmlspecialchars($t['icone']) : 'tag' ?>"></i>
                                    <?= htmlspecialchars($t['icone'] ?? 'tag') ?>
                                </td>
                                <td><?= htmlspecialchars(substr($t['description'] ?? '', 0, 40)) ?>...</td>
                                <td class="actions">
                                    <a href="index.php?action=types-detail&id=<?= $t['id_type'] ?? $t['id'] ?? 0 ?>" class="btn-view" title="Voir">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="index.php?action=types-modifier&id=<?= $t['id_type'] ?? $t['id'] ?? 0 ?>" class="btn-edit" title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="index.php?action=types-supprimer&id=<?= $t['id_type'] ?? $t['id'] ?? 0 ?>" class="btn-delete" title="Supprimer" onclick="return confirm('Supprimer ce type ?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
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
.alert-success {
    background: #d4edda;
    color: #155724;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.alert-error {
    background: #f8d7da;
    color: #721c24;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.table-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}
.header-buttons {
    display: flex;
    gap: 10px;
}
.search-box {
    display: flex;
    align-items: center;
    gap: 10px;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 8px 15px;
}
.search-box input {
    background: none;
    border: none;
    outline: none;
    color: var(--txt);
}
.btn-add {
    background: var(--green);
    color: white;
    padding: 8px 20px;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}
.btn-add:hover {
    transform: translateY(-2px);
    filter: brightness(1.05);
}
.table-wrapper {
    overflow-x: auto;
}
.table-container {
    min-width: 800px;
    background: var(--card);
    border-radius: 12px;
    border: 1px solid var(--border);
    overflow: hidden;
}
table {
    width: 100%;
    border-collapse: collapse;
}
th {
    padding: 12px;
    text-align: left;
    background: rgba(0,0,0,0.2);
    color: var(--txt-muted);
    font-size: 12px;
    font-weight: 600;
}
td {
    padding: 10px 12px;
    border-bottom: 1px solid var(--border);
    color: var(--txt);
}
.actions {
    display: flex;
    gap: 8px;
}
.btn-view, .btn-edit, .btn-delete {
    padding: 5px 10px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
    transition: all 0.2s;
}
.btn-view {
    background: rgba(52,152,219,0.15);
    color: #3498db;
}
.btn-view:hover {
    background: #3498db;
    color: white;
}
.btn-edit {
    background: rgba(243,156,18,0.15);
    color: #f39c12;
}
.btn-edit:hover {
    background: #f39c12;
    color: white;
}
.btn-delete {
    background: rgba(231,76,60,0.15);
    color: #e74c3c;
}
.btn-delete:hover {
    background: #e74c3c;
    color: white;
}
.text-center {
    text-align: center;
    padding: 40px;
    color: var(--txt-muted);
}
.info-banner {
    background: rgba(0,168,107,0.08);
    border: 1px solid rgba(0,168,107,0.2);
    border-radius: 12px;
    padding: 12px 20px;
    margin-bottom: 20px;
}
.info-banner-content {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}
.info-banner-content i {
    color: var(--green);
    font-size: 1.1rem;
}
.info-banner-content span {
    flex: 1;
    color: var(--txt-muted);
    font-size: 0.85rem;
}
.btn-sm-green {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(0,168,107,0.15);
    color: var(--green);
    border: 1px solid rgba(0,168,107,0.3);
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 0.75rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
}
.btn-sm-green:hover {
    background: var(--green);
    color: white;
    border-color: var(--green);
}
.btn-sm-outline {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: transparent;
    color: var(--txt-muted);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 0.75rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
}
.btn-sm-outline:hover {
    background: var(--surface3);
    color: var(--txt);
    border-color: var(--txt-muted);
}
@media (max-width: 768px) {
    .table-header {
        flex-direction: column;
    }
    .search-box {
        width: 100%;
    }
    .header-buttons {
        width: 100%;
    }
    .btn-add {
        flex: 1;
        justify-content: center;
    }
    .info-banner-content {
        flex-direction: column;
        text-align: center;
    }
    .info-banner-content span {
        width: 100%;
    }
}
</style>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#typesTable tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>