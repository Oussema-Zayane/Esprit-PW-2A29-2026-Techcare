<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../front/auth.php");
    exit();
}

require_once __DIR__ . "/../../Model/User.php";
$users = User::getAllUsers();

// Gestion des messages
$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
    if ($_GET['success'] == 'update') $success_message = "✅ Utilisateur modifié avec succès !";
    if ($_GET['success'] == 'add') $success_message = "✅ Utilisateur ajouté avec succès !";
    if ($_GET['success'] == 'delete') $success_message = "✅ Utilisateur supprimé avec succès !";
    if ($_GET['success'] == 'status_updated') $success_message = "✅ Statut de l'utilisateur mis à jour avec succès !";
}

if (isset($_GET['error'])) {
    if ($_GET['error'] == 'update') $error_message = "❌ Erreur lors de la modification !";
    if ($_GET['error'] == 'add') $error_message = "❌ Erreur lors de l'ajout !";
    if ($_GET['error'] == 'delete') $error_message = "❌ Erreur lors de la suppression !";
    if ($_GET['error'] == 'status_updated') $error_message = "❌ Erreur lors de la mise à jour du statut !";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 2.5em;
        }

        h2 {
            color: #555;
            margin: 20px 0 15px 0;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5a67d8;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #e53e3e;
            color: white;
        }

        .btn-danger:hover {
            background: #c53030;
        }

        .btn-success {
            background: #48bb78;
            color: white;
        }

        .btn-success:hover {
            background: #38a169;
        }

        .btn-warning {
            background: #ed8936;
            color: white;
        }

        .btn-warning:hover {
            background: #dd6b20;
        }

        .form-add {
            background: #f7fafc;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .form-add input, .form-add select {
            padding: 10px;
            margin: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: calc(20% - 10px);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th {
            background: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
        }

        table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        table tr:hover {
            background: #f7fafc;
        }

        table input, table select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 3px;
            width: 100%;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        hr {
            margin: 20px 0;
            border: none;
            border-top: 2px solid #e2e8f0;
        }

        .header-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .message {
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            display: block;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .update-form {
            display: contents;
        }
        
        .form-add .wrapper-add {
            display: inline-block;
            width: calc(20% - 10px);
            margin: 5px;
            vertical-align: top;
        }
        .form-add .wrapper-add input, .form-add .wrapper-add select {
            width: 100% !important;
            margin: 0 !important;
        }
        .form-add .wrapper-add .field-error {
            font-size: 11px;
            color: #e74c3c;
            margin-top: 3px;
            display: none;
        }
        .form-add .wrapper-add .field-error.show { display: block; }
        .form-add .wrapper-add input.error { border-color: #e74c3c; box-shadow: 0 0 0 1px rgba(231,76,60,0.2); }
        .form-add .wrapper-add input.valid { border-color: #00A86B; }

        .password-strength {
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
            flex-wrap: wrap;
        }
        .strength-bars {
            display: flex;
            gap: 3px;
            flex: 1;
        }
        .strength-bar {
            height: 4px;
            flex: 1;
            background: #ddd;
            border-radius: 2px;
            transition: all 0.2s ease;
        }
        .strength-bar.weak { background: #e74c3c; }
        .strength-bar.medium { background: #f39c12; }
        .strength-bar.strong { background: #00A86B; }
        .strength-text {
            font-size: 11px;
            font-weight: 600;
            min-width: 45px;
            text-align: right;
        }
        .strength-text.weak { color: #e74c3c; }
        .strength-text.medium { color: #f39c12; }
        .strength-text.strong { color: #00A86B; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Dashboard Admin 👑</h1>
        <p>Bienvenue, <?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?> !</p>
        
        <?php if ($success_message): ?>
            <div class="message success"><?= $success_message ?></div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="message error"><?= $error_message ?></div>
        <?php endif; ?>
        
        <div class="header-buttons">
            <a href="../front/profile.php">
                <button class="btn btn-primary">⬅ Retour Accueil</button>
            </a>
            <a href="../../Controller/logout.php">
                <button class="btn btn-danger">Logout</button>
            </a>
        </div>

        <hr>

        <!-- BOUTON AJOUTER UTILISATEUR -->
        <div style="margin: 20px 0;">
            <a href="addUser.php" class="btn btn-success" style="font-size: 16px; font-weight: bold; width: 100%; text-align: center;">➕ Ajouter utilisateur</a>
        </div>

        <hr>

        <!-- TABLE USERS -->
        <h2>📋 Liste des utilisateurs</h2>
        
        <?php if (!$users || count($users) == 0): ?>
            <p>Aucun utilisateur trouvé.</p>
        <?php else: ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user): ?>
                    <tr>
                        <form method="POST" action="../../Controller/UserController.php?action=update" class="update-form" onsubmit="return confirm('Modifier cet utilisateur ?')">
                            <td><?= htmlspecialchars($user['id']) ?>
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                            </td>
                            <td><input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required></td>
                            <td><input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required></td>
                            <td><input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></td>
                            <td><input type="text" name="telephone" value="<?= htmlspecialchars($user['telephone']) ?>" required></td>
                            <td>
                                <select name="role">
                                    <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </td>
                            <td>
                                <span style="padding: 5px; border-radius: 5px; color: white; background: <?= ($user['status'] ?? 'actif') == 'actif' ? '#48bb78' : '#e53e3e' ?>;">
                                    <?= ucfirst($user['status'] ?? 'actif') ?>
                                </span>
                            </td>
                            <td class="action-buttons">
                                <button type="submit" class="btn btn-warning" style="padding: 5px 10px;">✏ Modifier</button>
                            </td>
                        </form>
                        <td class="action-buttons">
                            <a href="../../Controller/UserController.php?action=toggle_status&id=<?= $user['id'] ?>&status=<?= ($user['status'] ?? 'actif') == 'actif' ? 'inactif' : 'actif' ?>" 
                               class="btn <?= ($user['status'] ?? 'actif') == 'actif' ? 'btn-danger' : 'btn-success' ?>" 
                               style="padding: 5px 10px; text-decoration: none; display: inline-block;"
                               onclick="return confirm('<?= ($user['status'] ?? 'actif') == 'actif' ? 'Bloquer' : 'Débloquer' ?> cet utilisateur ?')">
                                <?= ($user['status'] ?? 'actif') == 'actif' ? '🚫 Bloquer' : '✅ Débloquer' ?>
                            </a>
                            <a href="../../Controller/UserController.php?action=delete&id=<?= $user['id'] ?>" 
                               class="btn btn-danger" 
                               style="padding: 5px 10px; text-decoration: none; display: inline-block;"
                               onclick="return confirm('Supprimer cet utilisateur ?')">
                                🗑 Supprimer
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>


</body>
</html>