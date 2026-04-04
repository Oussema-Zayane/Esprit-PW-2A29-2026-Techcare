<?php
require_once "../config.php";
require_once "../Model/User.php";

session_start();

$action = $_GET['action'] ?? '';

/* ================= LOGIN ================= */
if ($action == "login") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = User::findByEmail($email);

    if (!$user) {
        echo "Compte introuvable ❌";
    } else {
        if (password_verify($password, $user['password'])) {

            // ✅ SESSION
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];

            // 🔁 REDIRECTION
            header("Location: ../View/front/profile.php");

        } else {
            echo "Mot de passe incorrect ❌";
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

    // Vérifier mot de passe
    if (strlen($password) < 8) {
        die("Mot de passe min 8 caractères ❌");
    }

    // Vérifier si email existe déjà
    $user = User::findByEmail($email);

    if ($user) {
        echo "Email déjà utilisé ❌";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        User::register($nom, $prenom, $email, $telephone, $hashedPassword);

        echo "Compte créé avec succès ✅";
    }
}
?>