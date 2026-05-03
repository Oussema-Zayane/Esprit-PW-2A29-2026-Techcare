<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechCare - Nos offres d'abonnement</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/front.css">
    <link rel="stylesheet" href="assets/css/crud.css">
    <script src="assets/js/theme.js"></script>
    <style>
        * { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="front-body">
<header class="front-header">
    <div class="container">
        <div class="logo">
            <i class="fa-solid fa-bolt" style="color: #00A86B;"></i>
            <span>TechCare</span>
        </div>
        <nav>
            <a href="index.php?page=accueil" class="<?= ($_GET['page'] ?? '') === 'accueil' ? 'active' : '' ?>">Accueil</a>
            <a href="index.php?page=offres" class="<?= ($_GET['page'] ?? '') === 'offres' ? 'active' : '' ?>">Offres</a>
            <a href="index.php?page=mes-abonnements" class="<?= ($_GET['page'] ?? '') === 'mes-abonnements' ? 'active' : '' ?>">📋 Mes abonnements</a>
            <a href="index.php?page=contact" class="<?= ($_GET['page'] ?? '') === 'contact' ? 'active' : '' ?>">Contact</a>
        </nav>
        <div class="header-actions">
            <a href="index.php?page=suivi" class="btn-outline">
                <i class="fa-solid fa-magnifying-glass"></i> Suivre
            </a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="index.php?action=dashboard" class="btn-primary" style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px;">
                    <i class="fa-solid fa-gate"></i> Admin
                </a>
                <a href="index.php?action=logout" class="btn-outline" style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px;">
                    <i class="fa-solid fa-sign-out-alt"></i> Déconnexion
                </a>
            <?php else: ?>
                <a href="index.php?action=login" class="btn-primary" style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px;">
                    <i class="fa-solid fa-lock"></i> Admin
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
<main>