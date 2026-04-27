<?php
session_start();

// Inclusion des contrôleurs
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Controller/GamificationController.php';
require_once __DIR__ . '/../src/Controller/FideliteController.php';
require_once __DIR__ . '/../src/Controller/UserController.php';
require_once __DIR__ . '/../src/Controller/BadgeController.php';

// Récupérer le contrôleur et l'action depuis l'URL
$controller = $_GET['controller'] ?? 'gamification';
$action = $_GET['action'] ?? 'dashboard';

// =====================================================
// ROUTAGE - GAMIFICATION (FrontOffice)
// =====================================================
if ($controller === 'gamification') {
    $ctrl = new GamificationController();
    if (method_exists($ctrl, $action)) {
        $ctrl->$action();
    } else {
        $ctrl->dashboard();
    }
}
// =====================================================
// ROUTAGE - FIDELITE (BackOffice + FrontOffice + Jointures)
// =====================================================
elseif ($controller === 'fidelite') {
    $ctrl = new FideliteController();
    switch($action) {
        // BackOffice CRUD
        case 'index': $ctrl->index(); break;
        case 'create': $ctrl->create(); break;
        case 'store': $ctrl->store(); break;
        case 'edit': $ctrl->edit(); break;
        case 'update': $ctrl->update(); break;
        case 'delete': $ctrl->delete(); break;
        // FrontOffice
        case 'frontoffice': $ctrl->frontoffice(); break;
        case 'manageFront': $ctrl->manageFront(); break;
        case 'storeFront': $ctrl->storeFront(); break;
        case 'editFront': $ctrl->editFront(); break;
        case 'updateFront': $ctrl->updateFront(); break;
        case 'deleteFront': $ctrl->deleteFront(); break;
        // API
        case 'getUserFidelites': $ctrl->getUserFidelites(); break;
        case 'getAll': $ctrl->getAll(); break;
        case 'addPoints': $ctrl->addPoints(); break;
        // Jointure
        case 'showByUser': $ctrl->showByUser(); break;
        default: $ctrl->index(); break;
    }
}
// =====================================================
// ROUTAGE - BADGE (BackOffice CRUD + IA + Jointures)
// =====================================================
elseif ($controller === 'badge') {
    $ctrl = new BadgeController();
    switch($action) {
        // BackOffice CRUD
        case 'index': $ctrl->index(); break;
        case 'create': $ctrl->create(); break;
        case 'store': $ctrl->store(); break;
        case 'edit': $ctrl->edit(); break;
        case 'update': $ctrl->update(); break;
        case 'delete': $ctrl->delete(); break;
        // API
        case 'getAll': $ctrl->getAll(); break;
        // Jointure Fidélité + Badges
        case 'showFideliteWithBadges': $ctrl->showFideliteWithBadges(); break;
        default: $ctrl->index(); break;
    }
}
// =====================================================
// ROUTAGE - USER (BackOffice Admin)
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