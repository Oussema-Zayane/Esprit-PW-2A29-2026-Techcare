<?php
/**
 * ====================================
 * FRONT CONTROLLER - PAGES PUBLIQUES
 * ====================================
 */
require_once __DIR__ . '/../models/Abonnement.php';
require_once __DIR__ . '/../models/Type.php';
require_once __DIR__ . '/../models/Sms.php';
require_once __DIR__ . '/../../config/database.php';

class FrontController {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ========== LOGIQUE DE DONNÉES (Abonnements) ==========

    public function createAbonnement($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO abonnements (nom_client, email, telephone, type_abonnement, prix, date_debut, date_fin, statut)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'actif')
        ");
        if (!$stmt) return false;
        $email = $data['email'] ?? null;
        $telephone = $data['telephone'] ?? null;
        $stmt->bind_param("ssssdss", $data['nom_client'], $email, $telephone, $data['type_abonnement'], $data['prix'], $data['date_debut'], $data['date_fin']);
        return $stmt->execute() ? $this->conn->insert_id : false;
    }

    public function getAbonnementsByEmail($email) {
        $stmt = $this->conn->prepare("
            SELECT a.*, t.nom_type, t.prix_base, t.duree_jours
            FROM abonnements a
            LEFT JOIN type_abonnement t ON a.type_abonnement = t.nom_type
            WHERE a.email = ?
            ORDER BY a.date_debut DESC
        ");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAbonnementById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM abonnements WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateAbonnement($id, $data) {
        $stmt = $this->conn->prepare("
            UPDATE abonnements 
            SET nom_client=?, type_abonnement=?, prix=?, date_debut=?, date_fin=?, statut=?
            WHERE id=?
        ");
        $stmt->bind_param("ssdsssi", $data['nom_client'], $data['type_abonnement'], $data['prix'], $data['date_debut'], $data['date_fin'], $data['statut'], $id);
        return $stmt->execute();
    }

    public function deleteAbonnement($id) {
        $stmt = $this->conn->prepare("DELETE FROM abonnements WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // ========== LOGIQUE DE DONNÉES (Types) ==========

    public function getAllTypes() {
        $result = $this->conn->query("SELECT * FROM type_abonnement ORDER BY id_type ASC");
        return ($result && $result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // ========== VUES (HTML) ==========

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

    public function souscrire() {
        $erreur = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $nom_client = htmlspecialchars($_POST['nom_client'] ?? '');
            $type = $_POST['type_abonnement'] ?? 'mensuel';
            $telephone = trim($_POST['telephone'] ?? '');
            if (!empty($telephone) && !str_starts_with($telephone, '+')) {
                $telephone = '+216' . preg_replace('/\D/', '', $telephone);
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erreur = "Email invalide.";
            } elseif (empty($nom_client)) {
                $erreur = "Le nom est obligatoire.";
            } elseif (!empty($telephone) && !preg_match('/^\+\d{10,15}$/', $telephone)) {
                $erreur = "Numéro de téléphone invalide.";
            } else {
                $date_debut = date('Y-m-d');
                switch ($type) {
                    case 'mensuel': $date_fin = date('Y-m-d', strtotime('+1 month')); $prix = 29.90; break;
                    case 'annuel': $date_fin = date('Y-m-d', strtotime('+1 year')); $prix = 299.00; break;
                    case 'ponctuel': $date_fin = date('Y-m-d', strtotime('+7 days')); $prix = 49.90; break;
                    default: $date_fin = date('Y-m-d', strtotime('+1 month')); $prix = 29.90;
                }
                $data = ['nom_client' => $nom_client, 'email' => $email, 'telephone' => $telephone, 'type_abonnement' => $type, 'prix' => $prix, 'date_debut' => $date_debut, 'date_fin' => $date_fin];
                $result = $this->createAbonnement($data);
                if ($result) {
                    $_SESSION['user_email'] = $email;
                    $_SESSION['success'] = "Souscription réussie !";
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

    public function suivi() { $this->mesAbonnements(); }

    public function mesAbonnements() {
        $email = ''; $mesAbonnements = [];
        if (!empty($_GET['email'])) {
            $email = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['user_email'] = $email;
                $mesAbonnements = $this->getAbonnementsByEmail($email);
            }
        } elseif (!empty($_SESSION['user_email'])) {
            $email = $_SESSION['user_email'];
            $mesAbonnements = $this->getAbonnementsByEmail($email);
        }
        require_once __DIR__ . '/../views/front/layout/header.php';
        require_once __DIR__ . '/../views/front/abonnements/mes-abonnements.php';
        require_once __DIR__ . '/../views/front/layout/footer.php';
    }

    public function modifierAbonnementFront() {
        $id = intval($_GET['id'] ?? 0);
        $abonnement = $this->getAbonnementById($id);
        if (!$abonnement) { header('Location: index.php?page=mes-abonnements'); exit(); }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = ['nom_client' => htmlspecialchars($_POST['nom_client'] ?? ''), 'type_abonnement' => $_POST['type_abonnement'] ?? '', 'prix' => floatval($_POST['prix'] ?? 0), 'date_debut' => $_POST['date_debut'] ?? '', 'date_fin' => $_POST['date_fin'] ?? '', 'statut' => 'actif'];
            $this->updateAbonnement($id, $data);
            $_SESSION['success'] = "Abonnement modifié avec succès.";
            header('Location: index.php?page=mes-abonnements');
            exit();
        }
        require_once __DIR__ . '/../views/front/layout/header.php';
        require_once __DIR__ . '/../views/front/abonnements/modifier-abo.php';
        require_once __DIR__ . '/../views/front/layout/footer.php';
    }

    public function supprimerAbonnementFront() {
        $id = intval($_GET['id'] ?? 0);
        $this->deleteAbonnement($id);
        $_SESSION['success'] = "Abonnement résilié avec succès.";
        header('Location: index.php?page=mes-abonnements');
        exit();
    }

    public function detailAbonnement() {
        $id = intval($_GET['id'] ?? 0);
        $abonnement = $this->getAbonnementById($id);
        require_once __DIR__ . '/../views/front/layout/header.php';
        include __DIR__ . '/../views/front/abonnements/detail.php'; // Simplified check
        require_once __DIR__ . '/../views/front/layout/footer.php';
    }

    public function contact() {
        $success_message = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = htmlspecialchars($_POST['nom'] ?? '');
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
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
                <div class="form-group"><label>Nom complet</label><input type="text" name="nom" required></div>
                <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
                <div class="form-group"><label>Message</label><textarea name="message" rows="5" required></textarea></div>
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

    public function offres() {
        $types = $this->getAllTypes();
        include __DIR__ . '/../views/front/abonnements/index.php';
    }
}
?>