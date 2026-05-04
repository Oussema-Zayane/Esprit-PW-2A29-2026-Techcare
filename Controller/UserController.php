<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../Model/User.php";
require_once __DIR__ . "/Mailer.php";

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
    
    if (isset($_SESSION['user_id'])) {
        header("Location: ../View/front/auth.php");
        exit();
    }
    
    $user = User::findByEmail($email);
    
    // Si l'utilisateur existe, on vérifie d'abord son statut et le lockout
    if ($user) {
        if (isset($user['status']) && $user['status'] === 'inactif') {
            header("Location: ../View/front/auth.php?error=banned");
            exit();
        }

        // Vérification du lockout (is_locked vient de notre requête SQL NOW())
        if (isset($user['is_locked']) && $user['is_locked']) {
            $seconds = $user['seconds_left'] ?? 0;
            header("Location: ../View/front/auth.php?error=locked&seconds=$seconds");
            exit();
        }

        // Vérification de l'email
        if (isset($user['is_verified']) && $user['is_verified'] == 0) {
            header("Location: ../View/front/auth.php?error=not_verified&email=" . urlencode($email));
            exit();
        }
    }

    $isRecaptchaValid = verifyRecaptcha($recaptchaResponse);

    // Si l'utilisateur n'existe pas
    if (!$user) {
        $error = $isRecaptchaValid ? "login" : "recaptcha";
        header("Location: ../View/front/auth.php?error=$error");
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
            header("Location: ../View/front/auth.php?error=locked_now&seconds=$seconds");
        } else {
            $error = !$isRecaptchaValid ? "recaptcha" : "login";
            header("Location: ../View/front/auth.php?error=$error");
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

    // Single Session Management
    User::updateSessionId($user['id'], session_id());

    header("Location: ../View/front/profile.php");
    exit();
}

if ($action == "face_login") {
    $descriptor = json_decode($_POST['descriptor'] ?? '[]', true);
    if (empty($descriptor)) {
        echo json_encode(['success' => false, 'error' => 'Descriptor manquant']);
        exit();
    }

    if (isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Une session est déjà ouverte']);
        exit();
    }

    $conn = Config::getConnexion();
    $stmt = $conn->query("SELECT id, email, nom, prenom, role, telephone, profile_picture, status, face_descriptor FROM personne WHERE face_descriptor IS NOT NULL");
    $users = $stmt->fetchAll();

    foreach ($users as $user) {
        $storedDescriptor = json_decode($user['face_descriptor'], true);
        if (!$storedDescriptor) continue;

        $distance = 0;
        for ($i = 0; $i < count($descriptor); $i++) {
            $distance += pow($descriptor[$i] - $storedDescriptor[$i], 2);
        }
        $distance = sqrt($distance);

        // Seuil de tolérance (0.6 est standard)
        if ($distance < 0.6) {
            if ($user['status'] === 'inactif') {
                echo json_encode(['success' => false, 'error' => 'Compte banni']);
                exit();
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['telephone'] = $user['telephone'] ?? '';
            $_SESSION['profile_picture'] = $user['profile_picture'] ?? 'default.png';

            // Single Session Management
            User::updateSessionId($user['id'], session_id());

            echo json_encode(['success' => true]);
            exit();
        }
    }

    echo json_encode(['success' => false, 'error' => 'Visage non reconnu']);
    exit();
}

if ($action == "google_login") {
    $idToken = $_POST['id_token'] ?? '';
    if (empty($idToken)) {
        echo json_encode(['success' => false, 'error' => 'Token manquant']);
        exit();
    }

    if (isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Une session est déjà ouverte']);
        exit();
    }

    // Vérifier le token avec Google API
    $url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $idToken;
    $response = @file_get_contents($url);
    if ($response === false) {
        echo json_encode(['success' => false, 'error' => 'Erreur de vérification Google']);
        exit();
    }

    $data = json_decode($response, true);
    if (!isset($data['sub'])) {
        echo json_encode(['success' => false, 'error' => 'Token invalide']);
        exit();
    }

    $googleId = $data['sub'];
    $email = $data['email'];
    $nom = $data['family_name'] ?? 'Utilisateur';
    $prenom = $data['given_name'] ?? 'Google';

    $user = User::findByGoogleId($googleId);
    if (!$user) {
        $user = User::findByEmail($email);
        if ($user) {
            // Lier le compte Google au compte existant
            $conn = Config::getConnexion();
            $stmt = $conn->prepare("UPDATE personne SET google_id = ? WHERE id = ?");
            $stmt->execute([$googleId, $user['id']]);
        } else {
            // Créer un nouveau compte
            User::register($nom, $prenom, $email, '', password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT), 'user', $googleId);
            $user = User::findByGoogleId($googleId);
        }
    }

    if ($user['status'] === 'inactif') {
        echo json_encode(['success' => false, 'error' => 'Compte banni']);
        exit();
    }

    // Login
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['nom'] = $user['nom'];
    $_SESSION['prenom'] = $user['prenom'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['telephone'] = $user['telephone'] ?? '';
    $_SESSION['profile_picture'] = $user['profile_picture'] ?? 'default.png';

    // Single Session Management
    User::updateSessionId($user['id'], session_id());

    echo json_encode(['success' => true]);
    exit();
}

if ($action == "update_face") {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Non connecté']);
        exit();
    }
    
    $descriptor = $_POST['descriptor'] ?? '';
    if (empty($descriptor)) {
        echo json_encode(['success' => false, 'error' => 'Descriptor manquant']);
        exit();
    }
    
    if (User::updateFaceDescriptor($_SESSION['user_id'], $descriptor)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erreur DB']);
    }
    exit();
}

if ($action == "register") {
    if (!verifyRecaptcha($_POST['g-recaptcha-response'] ?? '')) {
        header("Location: ../View/front/auth.php?error=recaptcha");
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
        header("Location: ../View/front/auth.php?error=" . $errorCode);
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $result = User::register($nom, $prenom, $email, $telephoneClean, $hashedPassword, 'user');
    
    if ($result) {
        $newUser = User::findByEmail($email);
        $token = bin2hex(random_bytes(32));
        User::updateVerificationToken($newUser['id'], $token);
        
        // Envoyer l'email
        Mailer::sendVerificationEmail($email, $token);
        
        header("Location: ../View/front/auth.php?success=register_verify");
    } else {
        header("Location: ../View/front/auth.php?error=register");
    }
    exit();
}

if ($action == "verify") {
    $token = $_GET['token'] ?? '';
    if (User::verifyUser($token)) {
        header("Location: ../View/front/auth.php?success=verified");
    } else {
        header("Location: ../View/front/auth.php?error=invalid_token");
    }
    exit();
}

if ($action == "forgot_password") {
    $email = trim($_POST['email'] ?? '');
    $user = User::findByEmail($email);
    
    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        User::setResetToken($email, $token, $expires);
        
        Mailer::sendResetEmail($email, $token);
        header("Location: ../View/front/auth.php?success=reset_sent");
    } else {
        // Pour des raisons de sécurité, on peut dire que l'email est envoyé même s'il n'existe pas
        // Mais ici on va être explicite pour l'exercice
        header("Location: ../View/front/auth.php?error=email_not_found");
    }
    exit();
}

if ($action == "reset_password") {
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $user = User::findByResetToken($token);
    if ($user) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        User::updatePassword($user['id'], $hashedPassword);
        header("Location: ../View/front/auth.php?success=password_reset");
    } else {
        header("Location: ../View/front/auth.php?error=invalid_reset_token");
    }
    exit();
}

if ($action == "add") {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
        header("Location: ../View/front/auth.php");
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
        header("Location: ../View/front/auth.php");
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
        header("Location: ../View/front/auth.php");
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
        header("Location: ../View/front/auth.php");
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
        header("Location: ../View/front/auth.php");
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

if ($action == "delete_profile") {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../View/front/auth.php");
        exit();
    }

    $id = $_SESSION['user_id'];
    $result = User::deleteUser($id);
    
    if ($result) {
        session_destroy();
        header("Location: ../View/front/auth.php?success=account_deleted");
    } else {
        header("Location: ../View/front/profile.php?error=delete_failed");
    }
    exit();
}
?>