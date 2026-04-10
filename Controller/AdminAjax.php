<?php
session_start();
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../Model/User.php";

// Vérifier que c'est un admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    http_response_code(403);
    exit('Accès non autorisé');
}

$action = $_GET['action'] ?? '';

if ($action == 'dashboard_stats') {
    $conn = Config::getConnexion();
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM personne");
    $totalUsers = $stmt->fetch()['total'];
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM produits");
    $totalProducts = $stmt->fetch()['total'] ?? 0;
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM commandes");
    $totalOrders = $stmt->fetch()['total'] ?? 0;
    
    header('Content-Type: application/json');
    echo json_encode([
        'totalUsers' => $totalUsers,
        'totalProducts' => $totalProducts,
        'totalOrders' => $totalOrders
    ]);
}

if ($action == 'get_users') {
    $users = User::getAllUsers();
    ?>
    <!-- Formulaire d'ajout d'utilisateur -->
    <div class="admin-add-form" style="margin-bottom: 2rem; padding: 1.5rem; background: var(--surface2); border-radius: var(--r-md);">
        <h4 style="margin-bottom: 1rem; color: var(--green);"><i class="fas fa-user-plus"></i> Ajouter un utilisateur</h4>
        <form id="addUserForm" onsubmit="event.preventDefault(); submitAddUser();">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
                <input type="text" name="nom" placeholder="Nom" required style="padding: 0.7rem; border-radius: var(--r-sm); border: 1px solid var(--border); background: var(--surface); color: var(--txt);">
                <input type="text" name="prenom" placeholder="Prénom" required style="padding: 0.7rem; border-radius: var(--r-sm); border: 1px solid var(--border); background: var(--surface); color: var(--txt);">
                <input type="email" name="email" placeholder="Email" required style="padding: 0.7rem; border-radius: var(--r-sm); border: 1px solid var(--border); background: var(--surface); color: var(--txt);">
                <input type="text" name="telephone" placeholder="Téléphone" required style="padding: 0.7rem; border-radius: var(--r-sm); border: 1px solid var(--border); background: var(--surface); color: var(--txt);">
                <input type="password" name="password" placeholder="Mot de passe" required style="padding: 0.7rem; border-radius: var(--r-sm); border: 1px solid var(--border); background: var(--surface); color: var(--txt);">
                <select name="role" style="padding: 0.7rem; border-radius: var(--r-sm); border: 1px solid var(--border); background: var(--surface); color: var(--txt);">
                    <option value="user">Utilisateur</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" class="btn-primary" style="margin-top: 1rem;"><i class="fas fa-plus"></i> Ajouter l'utilisateur</button>
        </form>
    </div>
    
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr><th>ID</th><th>Nom</th><th>Prénom</th><th>Email</th><th>Téléphone</th><th>Rôle</th><th>Actions</th></tr>
            </thead>
            <tbody id="users-table-body">
                <?php foreach($users as $user): ?>
                <tr id="user-row-<?= $user['id'] ?>">
                    <td><?= $user['id'] ?></td>
                    <td><input type="text" id="nom_<?= $user['id'] ?>" value="<?= htmlspecialchars($user['nom']) ?>" class="edit-input"></td>
                    <td><input type="text" id="prenom_<?= $user['id'] ?>" value="<?= htmlspecialchars($user['prenom']) ?>" class="edit-input"></td>
                    <td><input type="email" id="email_<?= $user['id'] ?>" value="<?= htmlspecialchars($user['email']) ?>" class="edit-input"></td>
                    <td><input type="text" id="telephone_<?= $user['id'] ?>" value="<?= htmlspecialchars($user['telephone']) ?>" class="edit-input"></td>
                    <td>
                        <select id="role_<?= $user['id'] ?>" class="edit-select">
                            <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                            <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </td>
                    <td class="tbl-actions">
                        <button class="btn-edit" onclick="updateUser(<?= $user['id'] ?>)"><i class="fas fa-edit"></i> Modifier</button>
                        <button class="btn-del" onclick="deleteUser(<?= $user['id'] ?>)"><i class="fas fa-trash"></i> Supprimer</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <style>
        .edit-input, .edit-select {
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: var(--r-sm);
            padding: 0.5rem 0.7rem;
            color: var(--txt);
            font: 0.85rem var(--font);
            width: 100%;
        }
        .edit-input:focus, .edit-select:focus {
            border-color: var(--green);
            outline: none;
        }
        .admin-add-form input, .admin-add-form select {
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--txt);
        }
        .admin-add-form input:focus, .admin-add-form select:focus {
            border-color: var(--green);
            outline: none;
        }
    </style>
    <?php
}
?>