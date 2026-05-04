<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
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
            <i class="fas fa-shield-halved" style="color: #00A86B;"></i>
            Sécurité & Biométrie
        </div>
    </div>
    
    <div style="background: var(--surface2); padding: 2rem; border-radius: var(--r-lg); max-width: 600px; border: 1px solid var(--border);">
        <p style="color: var(--txt-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">
            Enregistrez votre visage pour vous connecter rapidement et en toute sécurité sans mot de passe.
        </p>
        <button id="btn-register-face" class="btn-face" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.8rem; padding: 1rem; background: rgba(0, 168, 107, 0.1); border: 1px solid var(--green); color: var(--green); border-radius: var(--r-md); font-weight: 600; cursor: pointer; transition: all 0.2s;">
            <i class="fas fa-camera"></i> Enregistrer mon visage
        </button>
        <div id="face-status" style="margin-top: 1rem; font-size: 0.85rem; text-align: center; color: var(--txt-muted);"></div>
    </div>

    <div class="section-header" style="margin-top: 2.5rem;">
        <div class="section-title">
            <i class="fas fa-cog" style="color: #00A86B;"></i>
            Actions rapides
        </div>
    </div>
    
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <button onclick="confirmDelete()" class="btn-primary" style="background: rgba(231,76,60,0.1); color: var(--danger); border: 1px solid var(--danger); text-decoration: none; padding: 0.8rem 1.5rem; border-radius: var(--r-md); font-weight: 600; display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
            <i class="fas fa-user-times"></i> Supprimer le compte
        </button>
        <a href="../../Controller/logout.php" onclick="broadcastLogout()" class="btn-primary" style="background: rgba(231,76,60,0.1); color: var(--txt-muted); border: 1px solid var(--border); text-decoration: none; padding: 0.8rem 1.5rem; border-radius: var(--r-md); font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
            <i class="fas fa-sign-out-alt"></i> Se déconnecter
        </a>
    </div>
</div>

<!-- Modal pour la caméra -->
<div id="camera-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 9999; align-items: center; justify-content: center; flex-direction: column;">
    <div style="background: var(--surface); padding: 1.5rem; border-radius: var(--r-lg); position: relative; max-width: 90%; text-align: center;">
        <h3 style="margin-bottom: 1rem; color: var(--txt);">Scan Facial</h3>
        <div style="position: relative; overflow: hidden; border-radius: var(--r-md); background: #000; width: 400px; height: 300px; max-width: 100%;">
            <video id="video" width="400" height="300" autoplay muted style="transform: scaleX(-1);"></video>
            <canvas id="overlay" style="position: absolute; top: 0; left: 0;"></canvas>
        </div>
        <p id="modal-status" style="margin-top: 1rem; font-size: 0.9rem; color: var(--txt-muted);">Initialisation de la caméra...</p>
        <button id="btn-close-modal" style="margin-top: 1.5rem; padding: 0.6rem 1.2rem; background: var(--border); border: none; border-radius: var(--r-md); color: var(--txt); cursor: pointer;">Annuler</button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.min.js"></script>
<script>
    const btnRegister = document.getElementById('btn-register-face');
    const cameraModal = document.getElementById('camera-modal');
    const video = document.getElementById('video');
    const modalStatus = document.getElementById('modal-status');
    const btnClose = document.getElementById('btn-close-modal');
    
    let isModelsLoaded = false;

    async function loadModels() {
        if (isModelsLoaded) return;
        modalStatus.innerText = "Chargement des modèles d'IA...";
        const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
        ]);
        isModelsLoaded = true;
    }

    btnRegister.addEventListener('click', async () => {
        cameraModal.style.display = 'flex';
        await loadModels();
        
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: {} });
            video.srcObject = stream;
            modalStatus.innerText = "Positionnez votre visage devant la caméra...";
            
            // Attendre la détection
            const detectionInterval = setInterval(async () => {
                const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
                    .withFaceLandmarks()
                    .withFaceDescriptor();
                
                if (detection) {
                    clearInterval(detectionInterval);
                    modalStatus.innerText = "Visage détecté ! Enregistrement...";
                    
                    const descriptor = Array.from(detection.descriptor);
                    
                    // Envoyer au serveur
                    const response = await fetch('../../Controller/UserController.php?action=update_face', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'descriptor=' + JSON.stringify(descriptor)
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        modalStatus.innerText = "Succès ! Votre visage est enregistré.";
                        setTimeout(() => closeModal(), 2000);
                        document.getElementById('face-status').innerText = "✅ Visage enregistré";
                    } else {
                        modalStatus.innerText = "Erreur: " + data.error;
                    }
                }
            }, 1000);
            
        } catch (err) {
            modalStatus.innerText = "Erreur d'accès à la caméra.";
            console.error(err);
        }
    });

    function closeModal() {
        const stream = video.srcObject;
        if (stream) {
            const tracks = stream.getTracks();
            tracks.forEach(track => track.stop());
        }
        video.srcObject = null;
        cameraModal.style.display = 'none';
    }

    btnClose.addEventListener('click', closeModal);

    function confirmDelete() {
        if (confirm("Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.")) {
            window.location.href = "../../Controller/UserController.php?action=delete_profile";
        }
    }
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/app.php';
?>