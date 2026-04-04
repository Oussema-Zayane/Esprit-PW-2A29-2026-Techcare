<?php
session_start();

// 🔒 sécurité
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.html");
    exit();
}
?>

<h1>Bienvenue 👋</h1>

<p>Nom : <?php echo $_SESSION['nom']; ?></p>
<p>Prénom : <?php echo $_SESSION['prenom']; ?></p>
<p>Email : <?php echo $_SESSION['email']; ?></p>

<a href="../../Controller/logout.php">Se déconnecter</a>