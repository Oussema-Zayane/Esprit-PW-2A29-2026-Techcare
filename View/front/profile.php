<?php
session_start();

// sécurité
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.html");
    exit();
}
?>

<h1>Bienvenue 👋</h1>

<p>Email : <?php echo $_SESSION['email']; ?></p>

<!-- 🔥 BOUTON DASHBOARD (SEULEMENT ADMIN) -->
<?php if ($_SESSION['role'] == 'admin') { ?>
    <a href="../back/dashboard.php">
        <button>Dashboard Admin 👑</button>
    </a>
<?php } ?>

<br><br>

<a href="../../Controller/logout.php">Se déconnecter</a>