<?php
require_once __DIR__ . '/../models/Abonnement.php';

class FrontController {
    private $model;

    public function __construct() {
        $this->model = new Abonnement();
        session_start();
    }

    /**
     * Page d'accueil
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
            .hero-section p { font-size: 20px; margin-bottom: 30px; opacity: 0.9; }
        </style>
        <?php
        require_once __DIR__ . '/../views/front/layout/footer.php';
    }

    /**
     * Page des offres d'abonnements
     */
    public function offres() {
        $abonnements = $this->model->getAll();
        require_once __DIR__ . '/../views/front/layout/header.php';
        require_once __DIR__ . '/../views/front/abonnements/index.php';
        require_once __DIR__ . '/../views/front/layout/footer.php';
    }

    /**
     * Page de souscription
     */
    public function souscrire() {
        $erreur = null;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type = $_POST['type_abonnement'] ?? 'mensuel';
            // Définir les prix selon le type
            $prices = [
                'mensuel' => 29.90,
                'annuel' => 299.00
            ];
            
            // Calculer la date de fin selon le type
            $dateDebut = $_POST['date_debut'] ?? date('Y-m-d');
            if ($type === 'mensuel') {
                $dateFin = date('Y-m-d', strtotime($dateDebut . ' +1 month'));
            } else {
                $dateFin = date('Y-m-d', strtotime($dateDebut . ' +1 year'));
            }
            
            // Valider l'email
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $erreur = "Email invalide";
            } else {
                $data = [
                    'nom_client' => htmlspecialchars($_POST['nom_client'] ?? ''),
                    'email' => $email,
                    'type_abonnement' => $type,
                    'prix' => $prices[$type] ?? 29.90,
                    'date_debut' => $dateDebut,
                    'date_fin' => $dateFin
                ];
                
                $result = $this->model->create($data);
                
                if ($result) {
                    $_SESSION['success'] = "Souscription réussie !";
                    $_SESSION['user_email'] = $data['email'];
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
        // Pour l'exemple, on utilise un email fixe ou de session
        $email = $_SESSION['user_email'] ?? $_GET['email'] ?? 'client@techcare.com';
        $mesAbonnements = $this->model->getByEmail($email);
        
        require_once __DIR__ . '/../views/front/layout/header.php';
        require_once __DIR__ . '/../views/front/abonnements/mes-abonnements.php';
        require_once __DIR__ . '/../views/front/layout/footer.php';
    }

    /**
     * Modifier un abonnement (Front Office)
     */
    public function modifierAbonnement() {
        $id = intval($_GET['id'] ?? 0);
        $abonnement = $this->model->getById($id);
        
        if (!$abonnement) {
            header('Location: index.php?page=mes-abonnements');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom_client' => htmlspecialchars($_POST['nom_client'] ?? ''),
                'type_abonnement' => $_POST['type_abonnement'] ?? '',
                'prix' => floatval($_POST['prix'] ?? 0),
                'date_debut' => $_POST['date_debut'] ?? '',
                'date_fin' => $_POST['date_fin'] ?? '',
                'statut' => 'actif'
            ];
            $this->model->update($id, $data);
            $_SESSION['success'] = "Abonnement modifié avec succès";
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
        $_SESSION['success'] = "Abonnement résilié avec succès";
        header('Location: index.php?page=mes-abonnements');
        exit();
    }

    /**
     * Détail d'un abonnement (Front Office)
     */
    public function detailAbonnement() {
        $id = intval($_GET['id'] ?? 0);
        $abonnement = $this->model->getById($id);
        
        require_once __DIR__ . '/../views/front/layout/header.php';
        ?>
        <div class="container">
            <div class="detail-card">
                <h1>Détail de l'abonnement #<?= $abonnement['id'] ?></h1>
                <div class="detail-grid">
                    <div><strong>Client :</strong> <?= htmlspecialchars($abonnement['nom_client']) ?></div>
                    <div><strong>Type :</strong> <?= $abonnement['type_abonnement'] ?></div>
                    <div><strong>Prix :</strong> <?= $abonnement['prix'] ?> DT</div>
                    <div><strong>Début :</strong> <?= $abonnement['date_debut'] ?></div>
                    <div><strong>Fin :</strong> <?= $abonnement['date_fin'] ?></div>
                    <div><strong>Statut :</strong> <?= $abonnement['statut'] ?></div>
                </div>
                <a href="index.php?page=mes-abonnements" class="btn-secondary">← Retour</a>
            </div>
        </div>
        <style>
            .detail-card { background: white; padding: 30px; border-radius: 16px; margin: 40px 0; }
            .detail-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 20px; margin: 20px 0; }
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
            .contact-form { max-width: 600px; margin: 30px auto; background: white; padding: 30px; border-radius: 16px; }
            .form-group { margin-bottom: 20px; }
            .form-group label { display: block; margin-bottom: 8px; font-weight: bold; }
            .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; }
            .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; }
            .alert.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        </style>
        <?php
        require_once __DIR__ . '/../views/front/layout/footer.php';
    }
}
?>