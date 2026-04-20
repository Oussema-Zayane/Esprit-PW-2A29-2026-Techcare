<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.html");
    exit();
}

$pageTitle = "Mon Profil";
$activeTab = "profile";
$content = "";

// Démarrer la bufferisation
ob_start();
?>

<div class="hero">
    <div class="hero-content" style="display: flex; align-items: center; gap: 2rem;">
        <div style="position: relative;">
            <?php
                $avatarPath = '../assets/uploads/' . ($_SESSION['profile_picture'] ?? 'default.png');
                if (!file_exists(__DIR__ . '/' . $avatarPath) || ($_SESSION['profile_picture'] ?? 'default.png') == 'default.png') {
                    $avatarPath = 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['prenom'] . ' ' . $_SESSION['nom']) . '&background=random&color=fff&size=120';
                }
            ?>
            <img src="<?= htmlspecialchars($avatarPath) ?>" alt="Avatar" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--surface); box-shadow: var(--sh-md);">
        </div>
        <div>
            <div class="hero-badge">
                <i class="fas fa-user-check"></i> Rôle: <?= htmlspecialchars($_SESSION['role']) ?>
            </div>
            <h1 class="hero-title">
                Bonjour <?= htmlspecialchars($_SESSION['prenom']) ?> <span class="gradient-text">👋</span>
            </h1>
            <p class="hero-subtitle">
                Gérez vos informations personnelles et mettez à jour votre photo de profil.
            </p>
        </div>
    </div>
    <div class="hero-visual">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <i class="fas fa-user hero-main-icon"></i>
    </div>
</div>

<div class="products-section">
    <?php if (isset($_GET['success']) && $_GET['success'] == 'profile_updated'): ?>
        <div style="background: rgba(0,168,107,0.1); border: 1px solid var(--green); color: var(--green); padding: 1rem; border-radius: var(--r-md); margin-bottom: 2rem; font-weight: 500;">
            ✅ Profil mis à jour avec succès !
        </div>
    <?php elseif (isset($_GET['error'])): ?>
        <div style="background: rgba(231,76,60,0.1); border: 1px solid var(--danger); color: var(--danger); padding: 1rem; border-radius: var(--r-md); margin-bottom: 2rem; font-weight: 500;">
            ❌ Erreur lors de la mise à jour du profil.
        </div>
    <?php endif; ?>

    <div class="section-header">
        <div class="section-title">
            <i class="fas fa-id-card" style="color: #00A86B;"></i>
            Mes Informations
        </div>
    </div>
    
    <div style="background: var(--surface2); padding: 2rem; border-radius: var(--r-lg); max-width: 600px; border: 1px solid var(--border);">
        <form action="../../Controller/UserController.php?action=update_profile" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--txt); font-weight: 500; font-size: 0.9rem;">Prénom</label>
                    <input type="text" name="prenom" value="<?= htmlspecialchars($_SESSION['prenom']) ?>" required style="width: 100%; padding: 0.8rem 1rem; background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-md); color: var(--txt); font-family: inherit;">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; color: var(--txt); font-weight: 500; font-size: 0.9rem;">Nom</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars($_SESSION['nom']) ?>" required style="width: 100%; padding: 0.8rem 1rem; background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-md); color: var(--txt); font-family: inherit;">
                </div>
            </div>
            
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--txt); font-weight: 500; font-size: 0.9rem;">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_SESSION['email']) ?>" required style="width: 100%; padding: 0.8rem 1rem; background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-md); color: var(--txt); font-family: inherit;">
            </div>
            
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--txt); font-weight: 500; font-size: 0.9rem;">Téléphone</label>
                <input type="text" name="telephone" value="<?= htmlspecialchars($_SESSION['telephone'] ?? '') ?>" required style="width: 100%; padding: 0.8rem 1rem; background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-md); color: var(--txt); font-family: inherit;">
            </div>
            
            <div class="form-group">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--txt); font-weight: 500; font-size: 0.9rem;">Photo de profil (Optionnel)</label>
                <input type="file" name="profile_picture" accept="image/png, image/jpeg, image/jpg" style="width: 100%; padding: 0.8rem 1rem; background: var(--surface); border: 1px solid var(--border); border-radius: var(--r-md); color: var(--txt); font-family: inherit;">
            </div>
            
            <button type="submit" class="btn-primary" style="margin-top: 1rem; padding: 1rem; font-size: 1rem; font-weight: 600; width: 100%; cursor: pointer;">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
        </form>
    </div>
    
    <div class="section-header" style="margin-top: 2.5rem;">
        <div class="section-title">
            <i class="fas fa-cog" style="color: #00A86B;"></i>
            Actions rapides
        </div>
    </div>
    
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="../../Controller/logout.php" class="btn-primary" style="background: rgba(231,76,60,0.1); color: var(--danger); border: 1px solid var(--danger);">
            <i class="fas fa-sign-out-alt"></i> Se déconnecter
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include "../layouts/app.php";
?>