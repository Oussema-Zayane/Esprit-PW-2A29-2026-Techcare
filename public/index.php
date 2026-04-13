<?php
/**
 * TechCare - Routeur principal (MVC)
 * Toutes les requêtes passent par ce fichier
 */

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclure les dépendances
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Controller/GamificationController.php';
require_once __DIR__ . '/../src/Controller/UserController.php';

// Récupérer le contrôleur et l'action depuis l'URL
$controller = $_GET['controller'] ?? 'gamification';
$action = $_GET['action'] ?? 'dashboard';

// =====================================================
// ROUTAGE - GAMIFICATION (Front Office)
// =====================================================
if ($controller === 'gamification') {
    $ctrl = new GamificationController();
    
    switch ($action) {
        case 'dashboard':
            $ctrl->dashboard();
            break;
        case 'addPoints':
            $ctrl->addPoints();
            break;
        case 'points':
            $ctrl->points();
            break;
        case 'deleteHistory':
            $ctrl->deleteHistory();
            break;
        case 'badges':
            $ctrl->badges();
            break;
        case 'leaderboard':
            $ctrl->leaderboard();
            break;
        case 'rewards':
            $ctrl->rewards();
            break;
        case 'redeemReward':
            $ctrl->redeemReward();
            break;
        case 'loyalty':
            $ctrl->loyalty();
            break;
        // API Methods pour AJAX
        case 'getBadgesApi':
            $ctrl->getBadgesApi();
            break;
        case 'getRewardsApi':
            $ctrl->getRewardsApi();
            break;
        case 'addPointsApi':
            $ctrl->addPointsApi();
            break;
        default:
            $ctrl->dashboard();
            break;
    }
}
// =====================================================
// ROUTAGE - USER (Back Office)
// =====================================================
elseif ($controller === 'user') {
    $ctrl = new UserController();
    
    switch ($action) {
        case 'admin':
            $ctrl->adminDashboard();
            break;
        case 'getStats':
            $ctrl->getStats();
            break;
        case 'getUsers':
            $ctrl->getUsers();
            break;
        case 'deleteUser':
            $ctrl->deleteUser();
            break;
        case 'addUser':
            $ctrl->addUser();
            break;
        case 'updateUser':
            $ctrl->updateUser();
            break;
        default:
            $ctrl->adminDashboard();
            break;
    }
}
// =====================================================
// ROUTAGE PAR DÉFAUT
// =====================================================
else {
    header('Location: index.php?controller=gamification&action=dashboard');
    exit;
}
?>