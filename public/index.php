<?php
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Controller/GamificationController.php';
require_once __DIR__ . '/../src/Controller/FideliteController.php';
require_once __DIR__ . '/../src/Controller/UserController.php';
require_once __DIR__ . '/../src/Controller/BadgeController.php';

$controller = $_GET['controller'] ?? 'gamification';
$action = $_GET['action'] ?? 'dashboard';

// =====================================================
// ROUTAGE - GAMIFICATION
// =====================================================
if ($controller === 'gamification') {
    $ctrl = new GamificationController();
    switch($action) {
        // Pages principales
        case 'dashboard': $ctrl->dashboard(); break;
        case 'points': $ctrl->points(); break;
        case 'badges': $ctrl->badges(); break;
        case 'leaderboard': $ctrl->leaderboard(); break;
        case 'rewards': $ctrl->rewards(); break;
        case 'statistiques': $ctrl->statistiques(); break;
        // Parrainage
        case 'parrainage': $ctrl->parrainage(); break;
        case 'genererCode': $ctrl->genererCode(); break;
        case 'utiliserCode': $ctrl->utiliserCode(); break;
        // Calendrier connexion
        case 'enregistrerConnexion': $ctrl->enregistrerConnexion(); break;
        // Export
        case 'exportPDF': $ctrl->exportPDF(); break;
        // Actions
        case 'addPoints': $ctrl->addPoints(); break;
        case 'redeemReward': $ctrl->redeemReward(); break;
        case 'deleteHistory': $ctrl->deleteHistory(); break;
        // API
        case 'getUserData': $ctrl->getUserData(); break;
        case 'getLeaderboard': $ctrl->getLeaderboard(); break;
        case 'getGlobalStats': $ctrl->getGlobalStats(); break;
        case 'getBadgesApi': $ctrl->getBadgesApi(); break;
        case 'getRewardsApi': $ctrl->getRewardsApi(); break;
        case 'addPointsApi': $ctrl->addPointsApi(); break;
        default: $ctrl->dashboard(); break;
    }
}
// =====================================================
// ROUTAGE - FIDELITE
// =====================================================
elseif ($controller === 'fidelite') {
    $ctrl = new FideliteController();
    switch($action) {
        case 'index': $ctrl->index(); break;
        case 'create': $ctrl->create(); break;
        case 'store': $ctrl->store(); break;
        case 'edit': $ctrl->edit(); break;
        case 'update': $ctrl->update(); break;
        case 'delete': $ctrl->delete(); break;
        case 'frontoffice': $ctrl->frontoffice(); break;
        case 'manageFront': $ctrl->manageFront(); break;
        case 'storeFront': $ctrl->storeFront(); break;
        case 'editFront': $ctrl->editFront(); break;
        case 'updateFront': $ctrl->updateFront(); break;
        case 'deleteFront': $ctrl->deleteFront(); break;
        case 'getUserFidelites': $ctrl->getUserFidelites(); break;
        case 'getAll': $ctrl->getAll(); break;
        case 'addPoints': $ctrl->addPoints(); break;
        case 'showByUser': $ctrl->showByUser(); break;
        default: $ctrl->index(); break;
    }
}
// =====================================================
// ROUTAGE - BADGE
// =====================================================
elseif ($controller === 'badge') {
    $ctrl = new BadgeController();
    switch($action) {
        case 'index': $ctrl->index(); break;
        case 'create': $ctrl->create(); break;
        case 'store': $ctrl->store(); break;
        case 'edit': $ctrl->edit(); break;
        case 'update': $ctrl->update(); break;
        case 'delete': $ctrl->delete(); break;
        case 'getAll': $ctrl->getAll(); break;
        case 'showFideliteWithBadges': $ctrl->showFideliteWithBadges(); break;
        case 'fideliteBadges': $ctrl->fideliteBadges(); break;
        default: $ctrl->index(); break;
    }
}
// =====================================================
// ROUTAGE - USER
// =====================================================
elseif ($controller === 'user') {
    $ctrl = new UserController();
    switch($action) {
        case 'admin': $ctrl->adminDashboard(); break;
        case 'getStats': $ctrl->getStats(); break;
        case 'getUsers': $ctrl->getUsers(); break;
        case 'deleteUser': $ctrl->deleteUser(); break;
        case 'addUser': $ctrl->addUser(); break;
        default: $ctrl->adminDashboard(); break;
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