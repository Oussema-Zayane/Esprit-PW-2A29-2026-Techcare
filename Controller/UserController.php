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
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $password = $_POST['password'];

    // === VALIDATIONS CÔTÉ SERVEUR ===
    $errors = [];
    
    // Validation nom et prénom
    if (empty($nom) || empty($prenom)) {
        $errors[] = "nom_prenom_requis";
    }
    
    // Validation email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "email_invalide";
    }
    
    // Validation téléphone (8 chiffres exactement)
    $telephoneClean = preg_replace('/\D/', '', $telephone);
    if (strlen($telephoneClean) != 8) {
        $errors[] = "telephone_invalide";
    }
    
    // Validation mot de passe (minimum 8 caractères)
    if (strlen($password) < 8) {
        $errors[] = "password_court";
    }
    
    // Vérifier si l'email existe déjà
    $existingUser = User::findByEmail($email);
    if ($existingUser) {
        $errors[] = "email_exists";
    }
    
    // Si des erreurs, rediriger avec l'erreur
    if (!empty($errors)) {
        $errorCode = $errors[0];
        header("Location: ../View/front/auth.html?error=" . $errorCode);
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $result = User::register($nom, $prenom, $email, $telephoneClean, $hashedPassword, 'user');
    
    if ($result) {
        header("Location: ../View/front/auth.html?success=register");
    } else {
        header("Location: ../View/front/auth.html?error=register");
    }
    exit();
}

/* ================= ADD USER (ADMIN) ================= */
if ($action == "add") {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
        header("Location: ../View/front/auth.html");
        exit();
    }

    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $password = $_POST['password'];
    $role = $_POST['role'] ?? 'user';

    // === VALIDATIONS CÔTÉ SERVEUR ===
    $errors = [];
    
    // Validation nom et prénom
    if (empty($nom) || empty($prenom)) {
        $errors[] = "nom_prenom_requis";
    }
    
    // Validation email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "email_invalide";
    }
    
    // Validation téléphone (8 chiffres exactement)
    $telephoneClean = preg_replace('/\D/', '', $telephone);
    if (strlen($telephoneClean) != 8) {
        $errors[] = "telephone_invalide";
    }
    
    // Validation mot de passe (minimum 8 caractères)
    if (strlen($password) < 8) {
        $errors[] = "password_court";
    }
    
    // Vérifier si l'email existe déjà
    $existingUser = User::findByEmail($email);
    if ($existingUser) {
        $errors[] = "email_exists";
    }
    
    // S'il y a des erreurs, rediriger avec message d'erreur
    if (!empty($errors)) {
        $errorMsg = implode(',', $errors);
        header("Location: ../View/front/profile.php?admin_action=1&error=" . $errorMsg);
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $result = User::register($nom, $prenom, $email, $telephoneClean, $hashedPassword, $role);
    
    if ($result) {
        header("Location: ../View/front/profile.php?admin_action=1&success=add");
    } else {
        header("Location: ../View/front/profile.php?admin_action=1&error=add_failed");
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
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $role = $_POST['role'] ?? 'user';

    // === VALIDATIONS CÔTÉ SERVEUR ===
    $errors = [];
    
    // Validation nom et prénom
    if (empty($nom) || empty($prenom)) {
        $errors[] = "nom_prenom_requis";
    }
    
    // Validation email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "email_invalide";
    }
    
    // Validation téléphone (8 chiffres exactement)
    $telephoneClean = preg_replace('/\D/', '', $telephone);
    if (strlen($telephoneClean) != 8) {
        $errors[] = "telephone_invalide";
    }
    
    // S'il y a des erreurs, rediriger avec message d'erreur
    if (!empty($errors)) {
        $errorMsg = implode(',', $errors);
        header("Location: ../View/back/dashboard.php?error=" . $errorMsg);
        exit();
    }

    error_log("Update User ID: $id, Nom: $nom, Email: $email, Role: $role, Telephone: $telephoneClean");
    
    $result = User::updateUser($id, $nom, $prenom, $email, $telephoneClean, $role);
    
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