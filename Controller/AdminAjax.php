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

// Statistiques des rôles pour le pie chart
if ($action == 'role_stats') {
    $conn = Config::getConnexion();
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM personne WHERE role = 'admin'");
    $adminCount = $stmt->fetch()['total'];
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM personne WHERE role = 'user'");
    $userCount = $stmt->fetch()['total'];
    
    header('Content-Type: application/json');
    echo json_encode([
        'adminCount' => (int)$adminCount,
        'userCount' => (int)$userCount
    ]);
}

if ($action == 'get_users') {
    $search = $_GET['search'] ?? '';
    if (!empty($search)) {
        $conn = Config::getConnexion();
        $stmt = $conn->prepare("SELECT * FROM personne WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ? OR telephone LIKE ? OR role LIKE ? ORDER BY id DESC");
        $term = "%" . $search . "%";
        $stmt->execute([$term, $term, $term, $term, $term]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $users = User::getAllUsers();
    }
    ?>
    <style>
        .admin-input-group {
            position: relative;
            margin-bottom: 0.5rem;
        }
        .admin-field-error {
            font-size: 0.7rem;
            color: #e74c3c;
            margin-top: 0.25rem;
            display: none;
        }
        .admin-field-error.show {
            display: block;
        }
        .admin-field-success {
            font-size: 0.7rem;
            color: #00A86B;
            margin-top: 0.25rem;
            display: none;
        }
        .admin-field-success.show {
            display: block;
        }
        .admin-input-group input.error, 
        .admin-input-group select.error {
            border-color: #e74c3c !important;
        }
        .admin-input-group input.valid, 
        .admin-input-group select.valid {
            border-color: #00A86B !important;
        }
        .password-strength-admin {
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .strength-bars-admin {
            display: flex;
            gap: 0.3rem;
            flex: 1;
        }
        .strength-bar-admin {
            height: 4px;
            flex: 1;
            background: var(--border);
            border-radius: 2px;
            transition: all 0.2s ease;
        }
        .strength-bar-admin.weak { background: #e74c3c; }
        .strength-bar-admin.medium { background: #f39c12; }
        .strength-bar-admin.strong { background: #00A86B; }
        .strength-text-admin { font-size: 0.7rem; font-weight: 600; min-width: 70px; text-align: right; }
        .strength-text-admin.weak { color: #e74c3c; }
        .strength-text-admin.medium { color: #f39c12; }
        .strength-text-admin.strong { color: #00A86B; }
        .admin-submit-btn {
            background: linear-gradient(135deg, #00A86B, #007a4e);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.7rem 1.5rem;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        .admin-submit-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .admin-submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }
    </style>

    <div style="margin-bottom: 2rem; padding: 1.5rem; background: var(--surface2); border-radius: var(--r-md);">
        <a href="../back/addUser.php" style="background: linear-gradient(135deg, #00A86B, #007a4e); color: white; border: none; border-radius: 8px; padding: 0.7rem 1.5rem; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block;">
            <i class="fas fa-plus"></i> Ajouter l'utilisateur
        </a>
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


    <?php
}
?>