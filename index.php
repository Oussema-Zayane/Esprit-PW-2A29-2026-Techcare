<?php
/**
 * TechCare - Point d'entrée unique (Router)
 */

require_once 'config/database.php';

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les paramètres
$page = $_GET['page'] ?? null;
$action = $_GET['action'] ?? null;

// ========== FRONT OFFICE ==========
if ($page !== null) {
    require_once 'app/controllers/FrontController.php';
    $controller = new FrontController();

    switch ($page) {
        case 'accueil':
            $controller->accueil();
            break;
        case 'offres':
            $controller->offres();
            break;
        case 'souscrire':
            $controller->souscrire();
            break;
        case 'mes-abonnements':
            $controller->mesAbonnements();
            break;
        case 'detail-abonnement':
            $controller->detailAbonnement();
            break;
        case 'modifier-abonnement':
            $controller->modifierAbonnementFront();
            break;
        case 'supprimer-abonnement':
            $controller->supprimerAbonnementFront();
            break;
        case 'login':
            $controller->login();
            break;
        case 'register':
            $controller->register();
            break;
        case 'logout':
            $controller->logout();
            break;
        case 'contact':
            $controller->contact();
            break;
        default:
            $controller->accueil();
            break;
    }
}
// ========== BACK OFFICE ==========
else {
    // Vérifier si l'action concerne les types d'abonnement
    if (strpos($action, 'types-') === 0 || $action === 'types') {
        require_once 'app/controllers/TypeController.php';
        $controller = new TypeController();
        
        switch ($action) {
            case 'types':
                $controller->liste();
                break;
            case 'types-ajouter':
                $controller->ajouter();
                break;
            case 'types-modifier':
                $controller->modifier();
                break;
            case 'types-supprimer':
                $controller->supprimer();
                break;
            case 'types-detail':
                $controller->detail();
                break;
            default:
                $controller->liste();
                break;
        }
    }
    // Vérifier si l'action concerne les offres (options supplémentaires)
    elseif (strpos($action, 'offres-') === 0 || $action === 'offres') {
        require_once 'app/controllers/OffreController.php';
        $controller = new OffreController();
        
        switch ($action) {
            case 'offres':
                $controller->liste();
                break;
            case 'offres-ajouter':
                $controller->ajouter();
                break;
            case 'offres-modifier':
                $controller->modifier();
                break;
            case 'offres-supprimer':
                $controller->supprimer();
                break;
            case 'offres-detail':
                $controller->detail();
                break;
            default:
                $controller->liste();
                break;
        }
    }
    // Actions SMS
    elseif (strpos($action, 'sms') === 0) {
        require_once 'app/controllers/AdminController.php';
        $controller = new AdminController();
        
        switch ($action) {
            case 'smsLogs':
                $controller->smsLogs();
                break;
            case 'smsEnvoyer':
                $controller->smsEnvoyer();
                break;
            case 'smsDeleteLog':
                $controller->smsDeleteLog();
                break;
            case 'smsRappels':
                $controller->smsRappels();
                break;
            case 'smsRenvoyer':
                $controller->smsRenvoyer();
                break;
            default:
                $controller->dashboard();
                break;
        }
    }
    // Actions pour les abonnements et dashboard
    else {
        require_once 'app/controllers/AdminController.php';
        $controller = new AdminController();
        $action = $action ?? 'dashboard';

        switch ($action) {
            case 'dashboard':
                $controller->dashboard();
                break;
            case 'liste':
                $controller->liste();
                break;
            case 'ajouter':
                $controller->ajouter();
                break;
            case 'modifier':
                $controller->modifier();
                break;
            case 'supprimer':
                $controller->supprimer();
                break;
            case 'detail':
                $controller->detail();
                break;
            case 'statistiques':
                $controller->statistiques();
                break;
            case 'recherche':
                $controller->recherche();
                break;
            case 'logout':
                $controller->logout();
                break;
            default:
                $controller->dashboard();
                break;
        }
    }
}
?>