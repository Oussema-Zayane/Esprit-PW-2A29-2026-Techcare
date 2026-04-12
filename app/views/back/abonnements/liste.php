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

    <div class="table-container">
        <div class="table-header">
            <div class="search-box">
                <i class="fa-solid fa-search"></i>
                <input type="text" id="searchInput" placeholder="Rechercher un client...">
            </div>
            <a href="index.php?action=ajouter" class="btn-add">
                <i class="fa-solid fa-plus"></i> Ajouter un abonnement
            </a>
        </div>

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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($abonnements as $a): ?>
                <tr>
                    <td>#<?= $a['id'] ?></td>
                    <td><strong><?= htmlspecialchars($a['nom_client']) ?></strong></td>
                    <td><?= htmlspecialchars($a['email'] ?? 'Non renseigné') ?></td>
                    <td>
                        <span class="badge" style="background:#e8f5e9;color:#2e7d32;">
                            <?= ucfirst($a['type_abonnement']) ?>
                        </span>
                    </td>
                    <td><strong><?= number_format($a['prix'], 2) ?> DT</strong></td>
                    <td><?= date('d/m/Y', strtotime($a['date_debut'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($a['date_fin'])) ?></td>
                    <td>
                        <span class="badge badge-<?= $a['statut'] ?>">
                            <?= $a['statut'] ?>
                        </span>
                    </td>
                    <td class="actions">
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
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#abonnementsTable tbody tr');
    
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>