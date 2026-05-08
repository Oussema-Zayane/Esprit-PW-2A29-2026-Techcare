<?php
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
