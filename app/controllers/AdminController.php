<?php
require_once __DIR__ . '/../models/Abonnement.php';
require_once __DIR__ . '/../models/Type.php';
require_once __DIR__ . '/../models/Sms.php';

class AdminController {
    private $model;
    private $sms;

    public function __construct() {
        $this->model = new Abonnement();
        $this->sms   = new Sms();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Dashboard - Page d'accueil admin
     */
    public function dashboard() {
        $abonnements = $this->model->getAll();
        $totalAbonnements = count($abonnements);
        $actifs = count(array_filter($abonnements, fn($a) => $a['statut'] == 'actif'));
        $expires = count(array_filter($abonnements, fn($a) => $a['statut'] == 'expiré'));
        $revenus = array_sum(array_column($abonnements, 'prix'));
        $derniersAbonnements = array_slice($abonnements, 0, 5);
        
        // Statistiques SMS pour le dashboard
        $smsStats = $this->sms->getStats();

        include __DIR__ . '/../views/back/dashboard.php';
    }

    /**
     * Liste des abonnements
     */
    public function liste() {
        $abonnements = $this->model->getAll();
        include __DIR__ . '/../views/back/abonnements/liste.php';
    }

    /**
     * Ajouter un abonnement
     */
    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom_client' => $_POST['nom_client'],
                'email' => $_POST['email'] ?? '',
                'telephone' => $_POST['telephone'] ?? '',
                'type_abonnement' => $_POST['type_abonnement'],
                'prix' => $_POST['prix'],
                'date_debut' => $_POST['date_debut'],
                'date_fin' => $_POST['date_fin'],
            ];

            $result = $this->model->create($data);

            if ($result) {
                // Envoi SMS de bienvenue si numéro renseigné
                $telephone = trim($data['telephone'] ?? '');
                if (!empty($telephone)) {
                    $data['id'] = $result;
                    $this->sms->envoyerBienvenue($data);
                }
                
                // ═══ NOTIFICATION ADMIN ════════════════════
                $this->sms->notifierAdmin($data);
                header('Location: index.php?action=liste&success=ajoute');
            } else {
                header('Location: index.php?action=ajouter&error=1');
            }
            exit();
        }
        include __DIR__ . '/../views/back/abonnements/ajouter.php';
    }

    /**
     * Modifier un abonnement
     */
    public function modifier() {
        $id = $_GET['id'] ?? 0;
        $abonnement = $this->model->getById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom_client' => $_POST['nom_client'],
                'email' => $_POST['email'] ?? '',
                'telephone' => $_POST['telephone'] ?? '',
                'type_abonnement' => $_POST['type_abonnement'],
                'prix' => $_POST['prix'],
                'date_debut' => $_POST['date_debut'],
                'date_fin' => $_POST['date_fin'],
                'statut' => $_POST['statut'],
            ];
            $this->model->update($id, $data);
            header('Location: index.php?action=liste&success=modifie');
            exit();
        }

        include __DIR__ . '/../views/back/abonnements/modifier.php';
    }

    /**
     * Supprimer un abonnement
     */
    public function supprimer() {
        $id = $_GET['id'] ?? 0;
        $this->model->delete($id);
        header('Location: index.php?action=liste&success=supprime');
        exit();
    }

    /**
     * Détail d'un abonnement
     */
    public function detail() {
        $id = $_GET['id'] ?? 0;
        $abonnement = $this->model->getById($id);
        include __DIR__ . '/../views/back/abonnements/detail.php';
    }

    /**
     * Statistiques
     */
    public function statistiques() {
        $abonnements = $this->model->getAll();
        include __DIR__ . '/../views/back/statistiques.php';
    }

    /**
     * Recherche avancée
     */
    public function recherche() {
        $typeModel = new Type();
        $types = $typeModel->getAll();

        $type_abonnement = isset($_GET['type_abonnement']) && $_GET['type_abonnement'] !== '' ? $_GET['type_abonnement'] : null;
        $nom_client = isset($_GET['nom_client']) && $_GET['nom_client'] !== '' ? $_GET['nom_client'] : null;
        $email = isset($_GET['email']) && $_GET['email'] !== '' ? $_GET['email'] : null;
        $date_debut = isset($_GET['date_debut']) && $_GET['date_debut'] !== '' ? $_GET['date_debut'] : null;
        $prix = isset($_GET['prix']) && $_GET['prix'] !== '' ? floatval($_GET['prix']) : null;
        $date_fin = isset($_GET['date_fin']) && $_GET['date_fin'] !== '' ? $_GET['date_fin'] : null;

        $hasFilters = ($type_abonnement || $nom_client || $email || $date_debut || $prix || $date_fin);

        $abonnements = $hasFilters
            ? $this->model->rechercheParCritere($type_abonnement, $nom_client, $email, $date_debut, $prix, $date_fin)
            : $this->model->getAll();

        include __DIR__ . '/../views/back/recherche_abonnements.php';
    }

    /**
     * API recherche (JSON)
     */
    public function apiRecherche() {
        header('Content-Type: application/json');
        $resultats = $this->model->rechercheAvancee(
            $_GET['type_abonnement'] ?? null,
            $_GET['statut'] ?? null,
            $_GET['nom_client'] ?? null,
            $_GET['email'] ?? null,
            $_GET['date_debut_min'] ?? null,
            $_GET['date_debut_max'] ?? null,
            $_GET['prix_min'] ?? null,
            $_GET['prix_max'] ?? null
        );
        echo json_encode($resultats);
        exit();
    }

    /**
     * Déconnexion
     */
    public function logout() {
        session_destroy();
        header('Location: index.php?page=accueil');
        exit();
    }

    /* ══════════════════════════════════════════════════════
       ═══════════════  MÉTHODES SMS  ═══════════════════════
    ══════════════════════════════════════════════════════ */

    /**
     * Logs SMS — liste paginée avec filtres
     */
    public function smsLogs() {
        $page = max(1, intval($_GET['page'] ?? 1));
        $filtres = [
            'type_sms' => $_GET['type_sms'] ?? '',
            'statut'   => $_GET['statut']   ?? '',
            'search'   => $_GET['search']   ?? '',
        ];

        $logs  = $this->sms->getLogs($page, 20, $filtres);
        $total = $this->sms->countLogs($filtres);
        $stats = $this->sms->getStats();
        $pages = max(1, ceil($total / 20));

        // Flash message
        $flash = $_SESSION['sms_flash'] ?? null;
        unset($_SESSION['sms_flash']);

        include __DIR__ . '/../views/back/sms_logs.php';
    }

    /**
     * Envoi SMS manuel depuis le Back Office
     */
    public function smsEnvoyer() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=smsLogs');
            exit();
        }

        $telephone = trim($_POST['telephone'] ?? '');
        $nom = htmlspecialchars($_POST['nom_client'] ?? 'Client');
        $message = htmlspecialchars($_POST['message'] ?? '');
        $abonnement_id = !empty($_POST['abonnement_id']) ? intval($_POST['abonnement_id']) : null;

        if (empty($telephone) || empty($message)) {
            $_SESSION['sms_flash'] = [
                'type' => 'error',
                'msg'  => 'Le téléphone et le message sont obligatoires.',
            ];
            header('Location: index.php?action=smsLogs');
            exit();
        }

        $result = $this->sms->envoyerManuel($telephone, $nom, $message, $abonnement_id);

        if ($result['success']) {
            $mode = ($result['simule'] ?? false) ? ' (mode simulation)' : '';
            $_SESSION['sms_flash'] = [
                'type' => 'success',
                'msg'  => "SMS envoyé avec succès$mode !",
            ];
        } else {
            $_SESSION['sms_flash'] = [
                'type' => 'error',
                'msg'  => 'Échec : ' . ($result['error'] ?? 'Erreur inconnue'),
            ];
        }

        header('Location: index.php?action=smsLogs');
        exit();
    }

    /**
     * Lancer les rappels automatiques J-7 et J-3
     */
    public function smsRappels() {
        $rapport = $this->sms->lancerRappelsAutomatiques();

        $_SESSION['sms_flash'] = [
            'type' => 'success',
            'msg'  => "Rappels lancés : {$rapport['envoyes']} SMS envoyés, {$rapport['erreurs']} erreurs. " .
                      "(J-7 : " . count($rapport['j7']) . " | J-3 : " . count($rapport['j3']) . ")",
        ];

        header('Location: index.php?action=smsLogs');
        exit();
    }

    /**
     * Renvoyer le SMS de bienvenue pour un abonnement existant
     */
    public function smsRenvoyer() {
        $id = intval($_GET['id'] ?? 0);
        $abonnement = $this->model->getById($id);

        if (!$abonnement || empty($abonnement['telephone'])) {
            $_SESSION['sms_flash'] = [
                'type' => 'error',
                'msg'  => 'Abonnement introuvable ou numéro de téléphone manquant.',
            ];
            header('Location: index.php?action=smsLogs');
            exit();
        }

        $result = $this->sms->envoyerBienvenue($abonnement);

        $_SESSION['sms_flash'] = $result['success']
            ? ['type' => 'success', 'msg' => "SMS renvoyé à {$abonnement['nom_client']} ({$abonnement['telephone']})"]
            : ['type' => 'error',   'msg' => "Échec renvoi : " . ($result['error'] ?? 'inconnu')];

        header('Location: index.php?action=smsLogs');
        exit();
    }

    /**
     * Supprimer un log SMS
     */
    public function smsDeleteLog() {
        $id = intval($_GET['id'] ?? 0);
        $this->sms->deleteLog($id);
        
        $_SESSION['sms_flash'] = [
            'type' => 'success',
            'msg'  => 'Log SMS supprimé avec succès.',
        ];
        
        header('Location: index.php?action=smsLogs');
        exit();
    }
}
?>