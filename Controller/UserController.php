<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../Model/User.php";

session_start();

$action = $_GET['action'] ?? '';

/* ================= LOGIN ================= */
if ($action == "login") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = User::findByEmail($email);

    if (!$user) {
        header("Location: ../View/front/auth.html?error=login");
        exit();
    } else {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['role'] = $user['role'];

            // Rediriger vers profile.php dans le dossier front
            header("Location: ../View/front/profile.php");
            exit();
        } else {
            header("Location: ../View/front/auth.html?error=login");
            exit();
        }
    }
}

/* ================= REGISTER ================= */
if ($action == "register") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $password = $_POST['password'];

    if (strlen($password) < 8) {
        header("Location: ../View/front/auth.html?error=password");
        exit();
    }

    $user = User::findByEmail($email);

    if ($user) {
        header("Location: ../View/front/auth.html?error=email_exists");
        exit();
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $result = User::register($nom, $prenom, $email, $telephone, $hashedPassword, 'user');
        
        if ($result) {
            header("Location: ../View/front/auth.html?success=register");
        } else {
            header("Location: ../View/front/auth.html?error=register");
        }
        exit();
    }
}

/* ================= ADD USER (ADMIN) ================= */
if ($action == "add") {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
        header("Location: ../View/front/auth.html");
        exit();
    }

    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'] ?? 'user';

    $result = User::register($nom, $prenom, $email, $telephone, $password, $role);
    
    if ($result) {
        header("Location: ../View/back/dashboard.php?success=add");
    } else {
        header("Location: ../View/back/dashboard.php?error=add");
    }
    exit();
}

/* ================= UPDATE USER ================= */
if ($action == "update") {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
        header("Location: ../View/front/auth.html");
        exit();
    }

    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $role = $_POST['role'] ?? 'user';

    // Debug: Afficher les données reçues
    error_log("Update User ID: $id, Nom: $nom, Email: $email, Role: $role");
    
    $result = User::updateUser($id, $nom, $prenom, $email, $telephone, $role);
    
    // Debug: Vérifier le résultat
    error_log("Update result: " . ($result ? "true" : "false"));
    
    if ($result) {
        header("Location: ../View/back/dashboard.php?success=update");
    } else {
        header("Location: ../View/back/dashboard.php?error=update");
    }
    exit();
}

/* ================= DELETE USER ================= */
if ($action == "delete") {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
        header("Location: ../View/front/auth.html");
        exit();
    }

    $id = $_GET['id'];
    $result = User::deleteUser($id);
    
    if ($result) {
        header("Location: ../View/back/dashboard.php?success=delete");
    } else {
        header("Location: ../View/back/dashboard.php?error=delete");
    }
    exit();
}
?>