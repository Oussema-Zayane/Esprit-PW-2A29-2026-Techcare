<?php include __DIR__ . '/../layout/header.php'; ?>
<?php include __DIR__ . '/../layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <div class="page-title">
            <h1><i class="fa-solid fa-list-check"></i> Gestion des Options Supplémentaires</h1>
            <p>Liste des options disponibles pour les abonnements</p>
        </div>
        <div class="user-info">
            <div class="user-avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <span>Admin</span>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="table-header">
        <div class="search-box">
            <i class="fa-solid fa-search"></i>
            <input type="text" id="searchInput" placeholder="Rechercher une option...">
        </div>
        <a href="index.php?action=offres-ajouter" class="btn-add">
            <i class="fa-solid fa-plus"></i> Ajouter une option
        </a>
    </div>

    <div class="table-wrapper">
        <div class="table-container">
            <table id="offresTable">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Type d'abonnement</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($offres)): ?>
                        <tr><td colspan="3" class="text-center">Aucune option trouvée</td></tr>
                    <?php else: ?>
                        <?php foreach ($offres as $o): ?>
                            <tr>
                                <td class="description-cell"><?= htmlspecialchars($o['description']) ?></td>
                                <td>
                                    <?php if (!empty($o['types'])): ?>
                                        <div class="types-container">
                                        <?php 
                                            $noms = explode(', ', $o['type_nom']);
                                            foreach ($noms as $nom): 
                                        ?>
                                            <span class="type-badge type-<?= strtolower(trim($nom)) ?>">
                                                <?= ucfirst(trim($nom)) ?>
                                            </span>
                                        <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="non-lie">Non lié à un type</span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions">
                                    <a href="index.php?action=offres-detail&id=<?= $o['id'] ?>" class="btn-view" title="Voir">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="index.php?action=offres-modifier&id=<?= $o['id'] ?>" class="btn-edit" title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="index.php?action=offres-supprimer&id=<?= $o['id'] ?>" class="btn-delete" onclick="return confirm('Supprimer cette option ?')" title="Supprimer">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                <tr>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.alert-success { background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
.alert-error { background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
.table-header { display: flex; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; }
.search-box { display: flex; align-items: center; gap: 10px; background: var(--surface2); border: 1px solid var(--border); border-radius: 8px; padding: 8px 15px; }
.search-box input { background: none; border: none; outline: none; color: var(--txt); width: 250px; }
.search-box i { color: var(--txt-dim); }
.btn-add { background: var(--green); color: white; padding: 8px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; }
.btn-add:hover { transform: translateY(-2px); filter: brightness(1.05); }
.table-wrapper { overflow-x: auto; }
.table-container { min-width: 700px; background: var(--card); border-radius: 12px; border: 1px solid var(--border); overflow: hidden; }
table { width: 100%; border-collapse: collapse; }
th { padding: 12px; text-align: left; background: rgba(0,0,0,0.2); color: var(--txt-muted); font-size: 12px; font-weight: 600; }
td { padding: 10px 12px; border-bottom: 1px solid var(--border); color: var(--txt); }
.id-badge { font-family: monospace; font-size: 12px; color: var(--txt-muted); }
.description-cell { font-weight: 500; }
.types-container { display: flex; flex-wrap: wrap; gap: 6px; }
.type-badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.type-mensuel { background: #e3f2fd; color: #1565c0; border: 1px solid #bbdef5; }
.type-annuel { background: #f3e5f5; color: #6a1b9a; border: 1px solid #e1bee7; }
.type-ponctuel { background: #fff3e0; color: #e65100; border: 1px solid #ffe0b2; }
.non-lie { color: var(--txt-muted); font-style: italic; font-size: 12px; }
.actions { display: flex; gap: 8px; }
.btn-view, .btn-edit, .btn-delete { padding: 5px 10px; border-radius: 6px; text-decoration: none; font-size: 12px; transition: all 0.2s; }
.btn-view { background: rgba(52,152,219,0.15); color: #3498db; }
.btn-view:hover { background: #3498db; color: white; transform: translateY(-2px); }
.btn-edit { background: rgba(243,156,18,0.15); color: #f39c12; }
.btn-edit:hover { background: #f39c12; color: white; transform: translateY(-2px); }
.btn-delete { background: rgba(231,76,60,0.15); color: #e74c3c; }
.btn-delete:hover { background: #e74c3c; color: white; transform: translateY(-2px); }
.text-center { text-align: center; padding: 40px; color: var(--txt-muted); }
@media (max-width: 768px) {
    .table-header { flex-direction: column; align-items: stretch; }
    .search-box input { width: 100%; }
    .btn-add { text-align: center; justify-content: center; }
    .actions { flex-direction: column; gap: 5px; }
    .btn-view, .btn-edit, .btn-delete { text-align: center; }
}
</style>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#offresTable tbody tr');
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>