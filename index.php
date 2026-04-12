<?php
/**
 * TechCare - Routeur principal
 * Front Office = page=xxx
 * Back Office = action=xxx
 */

// Démarrer la session
session_start();

// Configuration base de données
require_once 'config/database.php';

// Récupérer les paramètres
$page = $_GET['page'] ?? null;
$action = $_GET['action'] ?? null;

// ========== FRONT OFFICE (Pages publiques) ==========
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
        case 'suivi':
            $controller->suivi();
            break;
        case 'contact':
            $controller->contact();
            break;
        default:
            $controller->accueil();
            break;
    }
}
// ========== BACK OFFICE (Administration) ==========
else {
    require_once 'app/controllers/AdminController.php';
    $controller = new AdminController();
    $action = $action ?? 'dashboard';
    
    // Vérifier l'authentification (sauf pour login)
    if ($action !== 'login' && !isset($_SESSION['user_id'])) {
        header('Location: index.php?action=login');
        exit();
    }
    
    switch ($action) {
        case 'dashboard':
            $controller->dashboard();
            break;
        case 'liste':
            $controller->listeAbonnements();
            break;
        case 'ajouter':
            $controller->ajouterAbonnement();
            break;
        case 'modifier':
            $controller->modifierAbonnement();
            break;
        case 'supprimer':
            $controller->supprimerAbonnement();
            break;
        case 'detail':
            $controller->detailAbonnement();
            break;
        case 'statistiques':
            $controller->statistiques();
            break;
        case 'login':
            $controller->login();
            break;
        case 'logout':
            $controller->logout();
            break;
        default:
            $controller->dashboard();
            break;
    }
}
?>