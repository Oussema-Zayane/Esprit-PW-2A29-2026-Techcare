<?php include __DIR__ . '/../layout/header.php'; ?>
<?php include __DIR__ . '/../layout/sidebar.php'; ?>

<div class="main-content">
    <div class="topbar">
        <div class="page-title">
            <h1><i class="fa-solid fa-list-check"></i> Gestion des Options</h1>
            <p>Liste des options supplémentaires</p>
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
                        <th>ID</th>
                        <th>Description</th>
                        <th>Abonnement lié</th>
                        <th>Client</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($offres)): ?>
                        <tr><td colspan="5" class="text-center">Aucune option trouvée</td></tr>
                    <?php else: ?>
                        <?php foreach ($offres as $o): ?>
                        <tr>
                            <td>#<?= $o['id'] ?></td>
                            <td><?= htmlspecialchars($o['description']) ?></td>
                            <td>Abonnement #<?= $o['id_abonnement'] ?> - <?= ucfirst($o['type_abonnement'] ?? '?') ?></td>
                            <td><?= htmlspecialchars($o['nom_client'] ?? '-') ?></td>
                            <td class="actions">
                                <a href="index.php?action=offres-detail&id=<?= $o['id'] ?>" class="btn-view"><i class="fa-solid fa-eye"></i></a>
                                <a href="index.php?action=offres-modifier&id=<?= $o['id'] ?>" class="btn-edit"><i class="fa-solid fa-pen"></i></a>
                                <a href="index.php?action=offres-supprimer&id=<?= $o['id'] ?>" class="btn-delete" onclick="return confirm('Supprimer cette option ?')"><i class="fa-solid fa-trash"></i></a>
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
.alert-success { background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
.alert-error { background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 20px; }
.table-header { display: flex; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 15px; }
.search-box { display: flex; align-items: center; gap: 10px; background: var(--surface2); border: 1px solid var(--border); border-radius: 8px; padding: 8px 15px; }
.search-box input { background: none; border: none; outline: none; color: var(--txt); }
.btn-add { background: var(--green); color: white; padding: 8px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
.table-wrapper { overflow-x: auto; }
.table-container { min-width: 700px; background: var(--card); border-radius: 12px; border: 1px solid var(--border); overflow: hidden; }
table { width: 100%; border-collapse: collapse; }
th { padding: 12px; text-align: left; background: rgba(0,0,0,0.2); color: var(--txt-muted); font-size: 12px; }
td { padding: 10px 12px; border-bottom: 1px solid var(--border); color: var(--txt); }
.actions { display: flex; gap: 8px; }
.btn-view, .btn-edit, .btn-delete { padding: 5px 10px; border-radius: 6px; text-decoration: none; font-size: 12px; }
.btn-view { background: rgba(52,152,219,0.15); color: #3498db; }
.btn-edit { background: rgba(243,156,18,0.15); color: #f39c12; }
.btn-delete { background: rgba(231,76,60,0.15); color: #e74c3c; }
.text-center { text-align: center; padding: 40px; color: var(--txt-muted); }
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