<?php
require_once __DIR__ . '/../models/Abonnement.php';

/**
 * Admin Controller
 * Gère les pages d'administration (Back Office)
 */
class AdminController {
    private $model;

    public function __construct() {
        $this->model = new Abonnement();
    }

    /**
     * Dashboard principal
     */
    public function dashboard() {
        $stats = [
            'total_abonnements' => $this->model->count(),
            'abonnements_actifs' => $this->model->countActive(),
        ];
        
        require_once __DIR__ . '/../views/back/layout/header.php';
        require_once __DIR__ . '/../views/dashboard.php';
        require_once __DIR__ . '/../views/back/layout/footer.php';
    }

    /**
     * Statistiques avancées
     */
    public function statistiques() {
        $stats = [
            'total_abonnements' => $this->model->count(),
            'abonnements_actifs' => $this->model->countActive(),
            'revenue_total' => $this->model->getTotalRevenue(),
        ];
        
        require_once __DIR__ . '/../views/back/layout/header.php';
        echo "<h1>Statistiques</h1>";
        echo "<pre>";
        print_r($stats);
        echo "</pre>";
        require_once __DIR__ . '/../views/back/layout/footer.php';
    }

    /**
     * Afficher la liste des abonnements
     */
    public function listeAbonnements() {
        $abonnements = $this->model->getAll();
        require_once __DIR__ . '/../views/back/layout/header.php';
        require_once __DIR__ . '/../views/back/abonnements/liste.php';
        require_once __DIR__ . '/../views/back/layout/footer.php';
    }

    /**
     * Formulaire d'ajout d'abonnement
     */
    public function ajouterAbonnement() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom_client' => htmlspecialchars($_POST['nom_client'] ?? ''),
                'email' => filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL),
                'type_abonnement' => $_POST['type_abonnement'] ?? '',
                'prix' => floatval($_POST['prix'] ?? 0),
                'date_debut' => $_POST['date_debut'] ?? '',
                'date_fin' => $_POST['date_fin'] ?? '',
            ];
            
            $result = $this->model->create($data);
            
            if ($result) {
                header('Location: index.php?admin&action=liste&success=1');
                exit();
            } else {
                $erreur = "Erreur lors de l'ajout de l'abonnement";
            }
        }
        
        require_once __DIR__ . '/../views/back/layout/header.php';
        require_once __DIR__ . '/../views/back/abonnements/ajouter.php';
        require_once __DIR__ . '/../views/back/layout/footer.php';
    }

    /**
     * Formulaire de modification d'abonnement
     */
    public function modifierAbonnement() {
        $id = intval($_GET['id'] ?? 0);
        $abonnement = $this->model->getById($id);
        
        if (!$abonnement) {
            header('Location: index.php?admin&action=liste');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom_client' => htmlspecialchars($_POST['nom_client'] ?? ''),
                'email' => filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL),
                'type_abonnement' => $_POST['type_abonnement'] ?? '',
                'prix' => floatval($_POST['prix'] ?? 0),
                'date_debut' => $_POST['date_debut'] ?? '',
                'date_fin' => $_POST['date_fin'] ?? '',
                'statut' => $_POST['statut'] ?? 'actif'
            ];
            
            $result = $this->model->update($id, $data);
            
            if ($result) {
                header('Location: index.php?admin&action=liste&success=1');
                exit();
            } else {
                $erreur = "Erreur lors de la modification";
            }
        }
        
        require_once __DIR__ . '/../views/back/layout/header.php';
        require_once __DIR__ . '/../views/back/abonnements/modifier.php';
        require_once __DIR__ . '/../views/back/layout/footer.php';
    }

    /**
     * Détail d'un abonnement
     */
    public function detailAbonnement() {
        $id = intval($_GET['id'] ?? 0);
        $abonnement = $this->model->getById($id);
        
        if (!$abonnement) {
            header('Location: index.php?admin&action=liste');
            exit();
        }
        
        require_once __DIR__ . '/../views/back/layout/header.php';
        require_once __DIR__ . '/../views/back/abonnements/detail.php';
        require_once __DIR__ . '/../views/back/layout/footer.php';
    }

    /**
     * Supprimer un abonnement
     */
    public function supprimerAbonnement() {
        $id = intval($_GET['id'] ?? 0);
        
        if ($this->model->delete($id)) {
            header('Location: index.php?admin&action=liste&success=1');
        } else {
            header('Location: index.php?admin&action=liste&error=1');
        }
        exit();
    }

    /**
     * Formulaire de connexion
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            // Simple authentication - à remplacer par une vérification en base de données
            if ($username === 'admin' && $password === 'admin') {
                $_SESSION['user_id'] = 1;
                $_SESSION['username'] = 'admin';
                header('Location: index.php?action=dashboard');
                exit();
            } else {
                $erreur = "Identifiants invalides";
            }
        }
        
        // Ne pas charger le header/footer du back, car on est pas connecté
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Connexion - TechCare Admin</title>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: 'Inter', sans-serif;
                    background: linear-gradient(135deg, #0A1F44 0%, #1a3a6e 100%);
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 20px;
                }
                .login-container {
                    background: white;
                    border-radius: 16px;
                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                    width: 100%;
                    max-width: 450px;
                    padding: 50px 40px;
                    animation: slideUp 0.5s ease-out;
                }
                @keyframes slideUp {
                    from { opacity: 0; transform: translateY(30px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .logo-section {
                    text-align: center;
                    margin-bottom: 40px;
                }
                .logo {
                    font-size: 48px;
                    margin-bottom: 15px;
                }
                .logo-section h1 {
                    color: #333;
                    font-size: 24px;
                    margin-bottom: 5px;
                }
                .logo-section p {
                    color: #666;
                    font-size: 14px;
                }
                .form-group {
                    margin-bottom: 25px;
                }
                .form-group label {
                    display: block;
                    color: #333;
                    font-weight: 600;
                    margin-bottom: 10px;
                    font-size: 14px;
                }
                .form-group input {
                    width: 100%;
                    padding: 12px 15px;
                    border: 2px solid #e0e0e0;
                    border-radius: 8px;
                    font-size: 14px;
                    font-family: 'Inter', sans-serif;
                    transition: all 0.3s ease;
                }
                .form-group input:focus {
                    outline: none;
                    border-color: #00A86B;
                    box-shadow: 0 0 0 4px rgba(0, 168, 107, 0.1);
                }
                .error-message {
                    background: #f8d7da;
                    color: #721c24;
                    padding: 12px;
                    border-radius: 6px;
                    margin-bottom: 20px;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    border-left: 4px solid #dc3545;
                }
                .error-message i {
                    font-size: 16px;
                }
                .submit-btn {
                    width: 100%;
                    padding: 12px;
                    background: linear-gradient(135deg, #00A86B, #009a5f);
                    color: white;
                    border: none;
                    border-radius: 8px;
                    font-weight: 600;
                    font-size: 14px;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    margin-top: 10px;
                }
                .submit-btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 5px 15px rgba(0, 168, 107, 0.3);
                }
                .submit-btn:active {
                    transform: translateY(0);
                }
                .back-link {
                    text-align: center;
                    margin-top: 20px;
                }
                .back-link a {
                    color: #00A86B;
                    text-decoration: none;
                    font-size: 14px;
                    font-weight: 500;
                }
                .back-link a:hover {
                    text-decoration: underline;
                }
            </style>
        </head>
        <body>
            <div class="login-container">
                <div class="logo-section">
                    <div class="logo">🔐</div>
                    <h1>TechCare Admin</h1>
                    <p>Tableau de bord d'administration</p>
                </div>

                <?php if (isset($erreur)): ?>
                    <div class="error-message">
                        <i class="fas fa-circle-exclamation"></i>
                        <span><?= $erreur ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="username">
                            <i class="fas fa-user"></i> Nom d'utilisateur
                        </label>
                        <input 
                            type="text" 
                            id="username"
                            name="username"
                            required
                            placeholder="admin"
                            autofocus
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i> Mot de passe
                        </label>
                        <input 
                            type="password" 
                            id="password"
                            name="password"
                            required
                            placeholder="••••••••"
                        >
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </button>
                </form>

                <div class="back-link">
                    <a href="index.php?page=accueil">
                        <i class="fas fa-arrow-left"></i> Retour à l'accueil
                    </a>
                </div>
            </div>
        </body>
        </html>
        <?php
    }

    /**
     * Déconnexion
     */
    public function logout() {
        session_destroy();
        header('Location: index.php?admin&action=login');
        exit();
    }
}
?>
