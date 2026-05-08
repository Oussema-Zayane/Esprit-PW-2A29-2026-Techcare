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
            case 'api_recommend_options':
                $controller->apiRecommanderOptions();
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
require_once __DIR__ . '/controller/DiagnosticController.php';
require_once __DIR__ . '/controller/SolutionController.php';

$controller = new DiagnosticController();
$solutionController = new SolutionController();

$action = $_GET['action'] ?? 'front_show';

switch ($action) {

    // DIAGNOSTIC FRONT
    case 'front_show':
        require_once __DIR__ . '/view/frontoffice/diagnostic/show.php';
        break;

    case 'front_create':
        require_once __DIR__ . '/view/frontoffice/diagnostic/create.php';
        break;

    case 'front_edit':
        require_once __DIR__ . '/view/frontoffice/diagnostic/edit.php';
        break;

    case 'front_delete':
        require_once __DIR__ . '/view/frontoffice/diagnostic/delete.php';
        break;

    case 'diagnostic_stats':
        require_once __DIR__ . '/view/frontoffice/diagnostic/stats.php';
        break;

    case 'diagnostic_filter':
        require_once __DIR__ . '/view/frontoffice/diagnostic/filter.php';
        break;

    case 'diagnostic_tri':
        require_once __DIR__ . '/view/frontoffice/diagnostic/tri.php';
        break;

    case 'diagnostic_score':
        require_once __DIR__ . '/view/frontoffice/diagnostic/score.php';
        break;

    case 'diagnostic_auto':
        require_once __DIR__ . '/view/frontoffice/diagnostic/auto.php';
        break;


    case 'diagnostic_history':
        $idDiagnostic = $_GET['id'] ?? null;
        if (!$idDiagnostic) {
            header('Location: index.php?action=front_show');
            exit;
        }
        $diagnostic = $controller->getDiagnosticById($idDiagnostic);
        $solutions = $solutionController->getSolutionsByDiagnostic($idDiagnostic);
        require_once __DIR__ . '/view/frontoffice/diagnostic/history.php';
        break;


    // ADMIN ADVANCED
    case 'admin_dashboard':
        require_once __DIR__ . '/view/backoffice/dashboard.php';
        break;

    case 'admin_alerts':
        require_once __DIR__ . '/view/backoffice/diagnostic/alerts.php';
        break;

    case 'admin_relations':
        require_once __DIR__ . '/view/backoffice/diagnostic/relations.php';
        break;

    case 'admin_analyse':
        require_once __DIR__ . '/view/backoffice/diagnostic/analyse.php';
        break;

    case 'admin_clean':
        require_once __DIR__ . '/view/backoffice/diagnostic/clean.php';
        break;

    case 'admin_audit':
        require_once __DIR__ . '/view/backoffice/audit.php';
        break;

    // DIAGNOSTIC BACK
    case 'back_show':
        require_once __DIR__ . '/view/backoffice/diagnostic/show.php';
        break;

    case 'back_create':
        require_once __DIR__ . '/view/backoffice/diagnostic/create.php';
        break;

    case 'back_edit':
        require_once __DIR__ . '/view/backoffice/diagnostic/edit.php';
        break;

    case 'back_delete':
        require_once __DIR__ . '/view/backoffice/diagnostic/delete.php';
        break;

    // SOLUTION FRONT
    case 'solution_front_show':
        require_once __DIR__ . '/view/frontoffice/solution/show.php';
        break;

    case 'solution_front_create':
        require_once __DIR__ . '/view/frontoffice/solution/create.php';
        break;

    case 'solution_front_edit':
        require_once __DIR__ . '/view/frontoffice/solution/edit.php';
        break;

    case 'solution_front_delete':
        require_once __DIR__ . '/view/frontoffice/solution/delete.php';
        break;

    case 'solution_search':
        require_once __DIR__ . '/view/frontoffice/solution/search.php';
        break;

    // SOLUTION BACK
    case 'solution_back_show':
        require_once __DIR__ . '/view/backoffice/solution/show.php';
        break;

    case 'solution_back_create':
        require_once __DIR__ . '/view/backoffice/solution/create.php';
        break;

    case 'solution_back_edit':
        require_once __DIR__ . '/view/backoffice/solution/edit.php';
        break;

    case 'solution_back_delete':
        require_once __DIR__ . '/view/backoffice/solution/delete.php';
        break;

    default:
        require_once __DIR__ . '/view/frontoffice/diagnostic/show.php';
        break;
}
?>
