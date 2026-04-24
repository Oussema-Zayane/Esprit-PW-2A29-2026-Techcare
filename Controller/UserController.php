<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../Model/User.php";

session_start();

function verifyRecaptcha($response) {
    if (empty($response)) return false;
    $secret = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe";
    $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$response;
    $verifyResponse = file_get_contents($verifyUrl);
    $responseData = json_decode($verifyResponse);
    return $responseData->success;
}

$action = $_GET['action'] ?? '';

if ($action == "login") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
    
    $user = User::findByEmail($email);
    
    // Si l'utilisateur existe, on vérifie d'abord son statut et le lockout
    if ($user) {
        if (isset($user['status']) && $user['status'] === 'inactif') {
            header("Location: ../View/front/auth.html?error=banned");
            exit();
        }

        // Vérification du lockout (is_locked vient de notre requête SQL NOW())
        if (isset($user['is_locked']) && $user['is_locked']) {
            $seconds = $user['seconds_left'] ?? 0;
            header("Location: ../View/front/auth.html?error=locked&seconds=$seconds");
            exit();
        }
    }

    $isRecaptchaValid = verifyRecaptcha($recaptchaResponse);

    // Si l'utilisateur n'existe pas
    if (!$user) {
        $error = $isRecaptchaValid ? "login" : "recaptcha";
        header("Location: ../View/front/auth.html?error=$error");
        exit();
    }

    // Gestion des échecs (Mot de passe ou Recaptcha)
    $passwordValid = password_verify($password, $user['password']);
    
    if (!$isRecaptchaValid || !$passwordValid) {
        $attempts = ($user['failed_attempts'] ?? 0) + 1;
        $lockoutMinutes = 0;
        
        if ($attempts == 4) {
            $lockoutMinutes = 1;
        } elseif ($attempts == 5) {
            $lockoutMinutes = 5;
        } elseif ($attempts >= 6) {
            $lockoutMinutes = 120; // 2 heures
        }
        
        User::recordFailedAttempt($user['id'], $attempts, $lockoutMinutes);
        
        if ($lockoutMinutes > 0) {
            $seconds = $lockoutMinutes * 60;
            header("Location: ../View/front/auth.html?error=locked_now&seconds=$seconds");
        } else {
            $error = !$isRecaptchaValid ? "recaptcha" : "login";
            header("Location: ../View/front/auth.html?error=$error");
        }
        exit();
    }

    // Succès : Tout est valide
    User::resetFailedAttempts($user['id']);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['nom'] = $user['nom'];
    $_SESSION['prenom'] = $user['prenom'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['telephone'] = $user['telephone'];
    $_SESSION['profile_picture'] = $user['profile_picture'] ?? 'default.png';

    header("Location: ../View/front/profile.php");
    exit();
}

if ($action == "register") {
    if (!verifyRecaptcha($_POST['g-recaptcha-response'] ?? '')) {
        header("Location: ../View/front/auth.html?error=recaptcha");
        exit();
    }
    
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $password = $_POST['password'];

    
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

if ($action == "add") {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
        header("Location: ../View/front/auth.html");
        exit();
    }
    
    if (!verifyRecaptcha($_POST['g-recaptcha-response'] ?? '')) {
        header("Location: ../View/front/profile.php?admin_action=1&error=recaptcha");
        exit();
    }

    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $password = $_POST['password'];
    $role = $_POST['role'] ?? 'user';

    $errors = [];
    
    // Validation nom et prénom
    if (empty($nom) || empty($prenom)) {
        $errors[] = "nom_prenom_requis";
    }
    
    // Validation email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "email_invalide";
    }
    
    $telephoneClean = preg_replace('/\D/', '', $telephone);
    if (strlen($telephoneClean) != 8) {
        $errors[] = "telephone_invalide";
    }
    
    if (strlen($password) < 8) {
        $errors[] = "password_court";
    }
    
    $existingUser = User::findByEmail($email);
    if ($existingUser) {
        $errors[] = "email_exists";
    }
    
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

    
    $errors = [];
    
    if (empty($nom) || empty($prenom)) {
        $errors[] = "nom_prenom_requis";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "email_invalide";
    }
    
    $telephoneClean = preg_replace('/\D/', '', $telephone);
    if (strlen($telephoneClean) != 8) {
        $errors[] = "telephone_invalide";
    }
    
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

if ($action == "toggle_status") {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
        header("Location: ../View/front/auth.html");
        exit();
    }

    $id = $_GET['id'];
    $status = $_GET['status'];
    $result = User::updateStatus($id, $status);
    
    if ($result) {
        header("Location: ../View/back/dashboard.php?success=status_updated");
    } else {
        header("Location: ../View/back/dashboard.php?error=status_updated");
    }
    exit();
}

if ($action == "update_profile") {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../View/front/auth.html");
        exit();
    }

    $id = $_SESSION['user_id'];
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $telephoneClean = preg_replace('/\D/', '', $telephone);
    
    $pictureName = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['profile_picture']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $pictureName = uniqid() . '.' . $ext;
            $destination = __DIR__ . '/../View/assets/uploads/' . $pictureName;
            move_uploaded_file($_FILES['profile_picture']['tmp_name'], $destination);
            $_SESSION['profile_picture'] = $pictureName;
        }
    }

    $result = User::updateProfile($id, $nom, $prenom, $email, $telephoneClean, $pictureName);
    
    if ($result) {
        $_SESSION['nom'] = $nom;
        $_SESSION['prenom'] = $prenom;
        $_SESSION['email'] = $email;
        $_SESSION['telephone'] = $telephoneClean;
        header("Location: ../View/front/profile.php?success=profile_updated");
    } else {
        header("Location: ../View/front/profile.php?error=update_failed");
    }
    exit();
}
?>