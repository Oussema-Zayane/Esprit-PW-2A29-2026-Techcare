<?php
/**
 * ====================================
 * FRONT CONTROLLER - PAGES PUBLIQUES
 * ====================================
 */
require_once __DIR__ . '/../models/Abonnement.php';
require_once __DIR__ . '/../models/Type.php';
require_once __DIR__ . '/../models/Sms.php'; // ← AJOUT SMS

class FrontController {
    private $model;
    private $sms; // ← AJOUT SMS

    public function __construct() {
        $this->model = new Abonnement();
        $this->sms   = new Sms(); // ← AJOUT SMS
    }

    /**
     * PAGE ACCUEIL
     */
    public function accueil() {
        require_once __DIR__ . '/../views/front/layout/header.php';
        ?>
        <div class="container">
            <section class="hero-section">
                <h1>Bienvenue chez TechCare</h1>
                <p>Votre partenaire technologique de confiance</p>
                <a href="index.php?page=offres" class="btn-primary">Découvrir nos offres</a>
            </section>
        </div>
        <style>
            .hero-section {
                text-align: center;
                padding: 80px 20px;
                background: linear-gradient(135deg, #0A1F44, #1a3a6e);
                border-radius: 20px;
                color: white;
                margin: 40px 0;
            }
            .hero-section h1 { font-size: 48px; margin-bottom: 20px; }
            .hero-section p  { font-size: 20px; margin-bottom: 30px; opacity: 0.9; }
        </style>
        <?php
        require_once __DIR__ . '/../views/front/layout/footer.php';
    }

    /**
     * PAGE SOUSCRIRE
     * ── MODIFIÉ : ajout champ téléphone + envoi SMS bienvenue ──
     */
    public function souscrire() {
        $erreur = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // ── Récupération et nettoyage des données ────────
            $email      = filter_var($_POST['email']      ?? '', FILTER_SANITIZE_EMAIL);
            $nom_client = htmlspecialchars($_POST['nom_client'] ?? '');
            $type       = $_POST['type_abonnement'] ?? 'mensuel';

            // ── NOUVEAU : récupération téléphone ─────────────
            $telephone = trim($_POST['telephone'] ?? '');
            // Normaliser : si l'utilisateur a envoyé +216XXXXXXXX (déjà formaté par le JS)
            if (!empty($telephone) && !str_starts_with($telephone, '+')) {
                $telephone = '+216' . preg_replace('/\D/', '', $telephone);
            }

            // ── Validation ───────────────────────────────────
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erreur = "Email invalide.";
            } elseif (empty($nom_client)) {
                $erreur = "Le nom est obligatoire.";
            } elseif (!empty($telephone) && !preg_match('/^\+\d{10,15}$/', $telephone)) {
                $erreur = "Numéro de téléphone invalide.";
            } else {

                // ── Calcul prix et dates selon type ──────────
                $date_debut = date('Y-m-d');
                switch ($type) {
                    case 'mensuel':
                        $date_fin = date('Y-m-d', strtotime('+1 month'));
                        $prix     = 29.90;
                        break;
                    case 'annuel':
                        $date_fin = date('Y-m-d', strtotime('+1 year'));
                        $prix     = 299.00;
                        break;
                    case 'ponctuel':
                        $date_fin = date('Y-m-d', strtotime('+7 days'));
                        $prix     = 49.90;
                        break;
                    default:
                        $date_fin = date('Y-m-d', strtotime('+1 month'));
                        $prix     = 29.90;
                }

                $data = [
                    'nom_client'      => $nom_client,
                    'email'           => $email,
                    'telephone'       => $telephone, // ← NOUVEAU
                    'type_abonnement' => $type,
                    'prix'            => $prix,
                    'date_debut'      => $date_debut,
                    'date_fin'        => $date_fin,
                ];

                $result = $this->model->create($data);

                if ($result) {

                    // ═══ ENVOI SMS BIENVENUE ═══════════════════
                    if (!empty($telephone)) {
                        $data['id'] = $result; // injecter l'ID créé
                        $smsResult  = $this->sms->envoyerBienvenue($data);

                        // Debug: vérifier le résultat
                        error_log("[TechCare] SMS Résultat: " . json_encode($smsResult));

                        if (!$smsResult['success']) {
                            // Ne pas bloquer la souscription si le SMS échoue
                            error_log("[TechCare] SMS bienvenue échoué : " .
                                ($smsResult['error'] ?? 'inconnu'));
                        }

                        // ═══ NOTIFICATION ADMIN ════════════════════
                        $this->sms->notifierAdmin($data);
                    } else {
                        error_log("[TechCare] Telephone vide, SMS non envoyé");
                    }
                    // ═══════════════════════════════════════════

                    $_SESSION['user_email'] = $email;
                    
                    // Message différent selon le mode SMS
                    if (!empty($telephone)) {
                        $twilioConfigured = defined('TWILIO_SID') && TWILIO_SID !== 'VOTRE_ACCOUNT_SID';
                        if ($twilioConfigured) {
                            $_SESSION['success'] = "Souscription réussie ! Un SMS de confirmation vous a été envoyé.";
                        } else {
                            $_SESSION['success'] = "Souscription réussie ! (Mode test - SMS simulé)";
                        }
                    } else {
                        $_SESSION['success'] = "Souscription réussie !";
                    }

                    header('Location: index.php?page=mes-abonnements');
                    exit();

                } else {
                    $erreur = "Erreur lors de la souscription. Veuillez réessayer.";
                }
            }
        }

        require_once __DIR__ . '/../views/front/layout/header.php';
        require_once __DIR__ . '/../views/front/abonnements/souscrire.php';
        require_once __DIR__ . '/../views/front/layout/footer.php';
    }

    /**
     * Page de suivi / Mes abonnements
     */
    public function suivi() {
        $this->mesAbonnements();
    }

    /**
     * Mes abonnements (client)
     */
    public function mesAbonnements() {
        $email          = '';
        $mesAbonnements = [];

        if (!empty($_GET['email'])) {
            $email = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['user_email'] = $email;
                $mesAbonnements = $this->model->getByEmail($email);
            }
        } elseif (!empty($_SESSION['user_email'])) {
            $email          = $_SESSION['user_email'];
            $mesAbonnements = $this->model->getByEmail($email);
        }

        require_once __DIR__ . '/../views/front/layout/header.php';
        require_once __DIR__ . '/../views/front/abonnements/mes-abonnements.php';
        require_once __DIR__ . '/../views/front/layout/footer.php';
    }

    /**
     * Modifier un abonnement (Front Office)
     */
    public function modifierAbonnement() {
        $id          = intval($_GET['id'] ?? 0);
        $abonnement  = $this->model->getById($id);

        if (!$abonnement) {
            header('Location: index.php?page=mes-abonnements');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom_client'      => htmlspecialchars($_POST['nom_client'] ?? ''),
                'type_abonnement' => $_POST['type_abonnement'] ?? '',
                'prix'            => floatval($_POST['prix']   ?? 0),
                'date_debut'      => $_POST['date_debut']      ?? '',
                'date_fin'        => $_POST['date_fin']        ?? '',
                'statut'          => 'actif',
            ];
            $this->model->update($id, $data);
            $_SESSION['success'] = "Abonnement modifié avec succès.";
            header('Location: index.php?page=mes-abonnements');
            exit();
        }

        require_once __DIR__ . '/../views/front/layout/header.php';
        require_once __DIR__ . '/../views/front/abonnements/modifier-abo.php';
        require_once __DIR__ . '/../views/front/layout/footer.php';
    }

    /**
     * Supprimer / Résilier un abonnement (Front Office)
     */
    public function supprimerAbonnement() {
        $id = intval($_GET['id'] ?? 0);
        $this->model->delete($id);
        $_SESSION['success'] = "Abonnement résilié avec succès.";
        header('Location: index.php?page=mes-abonnements');
        exit();
    }

    /**
     * Détail d'un abonnement (Front Office)
     */
    public function detailAbonnement() {
        $id         = intval($_GET['id'] ?? 0);
        $abonnement = $this->model->getById($id);

        require_once __DIR__ . '/../views/front/layout/header.php';
        ?>
        <div class="container">
            <div class="detail-card">
                <h1>Détail de l'abonnement #<?= $abonnement['id'] ?></h1>
                <div class="detail-grid">
                    <div><strong>Client :</strong> <?= htmlspecialchars($abonnement['nom_client']) ?></div>
                    <div><strong>Type :</strong>   <?= $abonnement['type_abonnement'] ?></div>
                    <div><strong>Prix :</strong>   <?= $abonnement['prix'] ?> DT</div>
                    <div><strong>Début :</strong>  <?= $abonnement['date_debut'] ?></div>
                    <div><strong>Fin :</strong>    <?= $abonnement['date_fin'] ?></div>
                    <div><strong>Statut :</strong> <?= $abonnement['statut'] ?></div>
                    <?php if (!empty($abonnement['telephone'])): ?>
                    <div><strong>Téléphone :</strong> <?= htmlspecialchars($abonnement['telephone']) ?></div>
                    <?php endif; ?>
                </div>
                <a href="index.php?page=mes-abonnements" class="btn-secondary">← Retour</a>
            </div>
        </div>
        <style>
            .detail-card  { background: white; padding: 30px; border-radius: 16px; margin: 40px 0; }
            .detail-grid  { display: grid; grid-template-columns: repeat(2,1fr); gap: 20px; margin: 20px 0; }
        </style>
        <?php
        require_once __DIR__ . '/../views/front/layout/footer.php';
    }

    /**
     * Page de contact
     */
    public function contact() {
        $success_message = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom     = htmlspecialchars($_POST['nom']     ?? '');
            $email   = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $message = htmlspecialchars($_POST['message'] ?? '');
            $success_message = "Merci pour votre message. Nous vous recontacterons bientôt.";
        }

        require_once __DIR__ . '/../views/front/layout/header.php';
        ?>
        <div class="container">
            <h1>Nous contacter</h1>
            <?php if ($success_message): ?>
                <div class="alert success"><?= $success_message ?></div>
            <?php endif; ?>
            <form method="POST" class="contact-form">
                <div class="form-group">
                    <label>Nom complet</label>
                    <input type="text" name="nom" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn-primary">Envoyer</button>
            </form>
        </div>
        <style>
            .contact-form { max-width:600px; margin:30px auto; background:white; padding:30px; border-radius:16px; }
            .form-group   { margin-bottom:20px; }
            .form-group label { display:block; margin-bottom:8px; font-weight:bold; }
            .form-group input, .form-group textarea { width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; }
            .alert { padding:15px; border-radius:8px; margin-bottom:20px; }
            .alert.success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
        </style>
        <?php
        require_once __DIR__ . '/../views/front/layout/footer.php';
    }

    /**
     * Page des offres d'abonnement
     */
    public function offres() {
        $typeModel = new Type();
        $types     = $typeModel->getAll();
        include __DIR__ . '/../views/front/abonnements/index.php';
    }
}
?>