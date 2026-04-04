<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../front/auth.html");
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
}

if (isset($_GET['error'])) {
    if ($_GET['error'] == 'update') $error_message = "❌ Erreur lors de la modification !";
    if ($_GET['error'] == 'add') $error_message = "❌ Erreur lors de l'ajout !";
    if ($_GET['error'] == 'delete') $error_message = "❌ Erreur lors de la suppression !";
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
            padding: 5px;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Dashboard Admin 👑</h1>
        <p>Bienvenue, <?= htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?> !</p>
        
        <!-- Affichage des messages -->
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

        <!-- 🟢 AJOUT USER -->
        <h2>➕ Ajouter un utilisateur</h2>
        <div class="form-add">
            <form action="../../Controller/UserController.php?action=add" method="POST" onsubmit="return confirm('Ajouter cet utilisateur ?')">
                <input type="text" name="nom" placeholder="Nom" required>
                <input type="text" name="prenom" placeholder="Prénom" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="telephone" placeholder="Téléphone" required>
                <input type="password" name="password" placeholder="Mot de passe" required>
                <select name="role">
                    <option value="user">Utilisateur</option>
                    <option value="admin">Admin</option>
                </select>
                <button type="submit" class="btn btn-success">Ajouter</button>
            </form>
        </div>

        <hr>

        <!-- 🔵 TABLE USERS -->
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($users as $user): ?>
                    <tr>
                        <form action="../../Controller/UserController.php?action=update" method="POST" onsubmit="return confirm('Modifier cet utilisateur ?')">
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
                            <td class="action-buttons">
                                <button type="submit" class="btn btn-warning" style="padding: 5px 10px;">✏ Modifier</button>
                                <a href="../../Controller/UserController.php?action=delete&id=<?= $user['id'] ?>" 
                                   class="btn btn-danger" 
                                   style="padding: 5px 10px; text-decoration: none;"
                                   onclick="return confirm('Supprimer cet utilisateur ?')">
                                    🗑 Supprimer
                                </a>
                            </td>
                        </form>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>