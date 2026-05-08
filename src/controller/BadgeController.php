<?php
require_once __DIR__ . '/../Model/BadgeModel.php';

class BadgeController {
    private $model;
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->model = new BadgeModel();
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
            $_SESSION['username'] = 'Ayari Firas';
            $_SESSION['role'] = 'admin';
        }
    }
    
    // =====================================================
    // BACKOFFICE - CRUD Badges
    // =====================================================
    
    public function index() {
        $badges = $this->model->getAll();
        $pageTitle = "Gestion des Badges";
        $activeTab = "badges_admin";
        ob_start();
        require_once __DIR__ . '/../View/backoffice/badge_index.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    public function create() {
        $pageTitle = "Ajouter un badge";
        $activeTab = "badges_admin";
        ob_start();
        require_once __DIR__ . '/../View/backoffice/badge_create.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $icon = trim($_POST['icon'] ?? '🏅');
            $seuilPoints = intval($_POST['seuilPoints'] ?? 0);
            $generateIA = isset($_POST['generate_ia']) && $_POST['generate_ia'] == '1';
            
            $errors = [];
            if (empty($nom)) $errors['nom'] = "Le nom du badge est obligatoire";
            elseif (strlen($nom) < 3) $errors['nom'] = "Le nom doit contenir au moins 3 caractères";
            if (empty($description)) $errors['description'] = "La description est obligatoire";
            elseif (strlen($description) < 10) $errors['description'] = "La description doit contenir au moins 10 caractères";
            if ($seuilPoints <= 0) $errors['seuilPoints'] = "Le seuil de points doit être un nombre positif";
            
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $_POST;
                header('Location: index.php?controller=badge&action=create');
                exit;
            }
            
            $imageIa = null;
            if ($generateIA) {
                $_SESSION['ia_status'] = "Génération de l'image en cours...";
                $imageIa = $this->model->generateImageWithAI($description, $nom);
                if ($imageIa) {
                    $_SESSION['ia_status'] = "Image générée avec succès !";
                } else {
                    $_SESSION['ia_status'] = "Échec de la génération d'image";
                }
            }
            
            if ($this->model->create($nom, $description, $icon, $seuilPoints, $imageIa)) {
                $_SESSION['success'] = "Badge '" . $nom . "' ajouté avec succès !";
                if ($imageIa) $_SESSION['success'] .= " Image IA générée.";
                unset($_SESSION['ia_status']);
                header('Location: index.php?controller=badge&action=index');
                exit;
            }
        }
    }
    
    public function edit() {
        $id = intval($_GET['id'] ?? 0);
        $badge = $this->model->getById($id);
        if (!$badge) {
            header('Location: index.php?controller=badge&action=index');
            exit;
        }
        $pageTitle = "Modifier un badge";
        $activeTab = "badges_admin";
        ob_start();
        require_once __DIR__ . '/../View/backoffice/badge_edit.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id'] ?? 0);
            $nom = trim($_POST['nom'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $icon = trim($_POST['icon'] ?? '🏅');
            $seuilPoints = intval($_POST['seuilPoints'] ?? 0);
            $generateIA = isset($_POST['generate_ia']) && $_POST['generate_ia'] == '1';
            
            $errors = [];
            if (empty($nom)) $errors['nom'] = "Le nom est obligatoire";
            if (empty($description)) $errors['description'] = "La description est obligatoire";
            if ($seuilPoints <= 0) $errors['seuilPoints'] = "Le seuil doit être positif";
            
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header("Location: index.php?controller=badge&action=edit&id=$id");
                exit;
            }
            
            $imageIa = null;
            if ($generateIA) {
                $imageIa = $this->model->generateImageWithAI($description, $nom);
            }
            
            if ($this->model->update($id, $nom, $description, $icon, $seuilPoints, $imageIa)) {
                $_SESSION['success'] = "Badge modifié avec succès !";
                if ($imageIa) $_SESSION['success'] .= " Nouvelle image IA générée.";
                header('Location: index.php?controller=badge&action=index');
                exit;
            }
        }
    }
    
    public function delete() {
        $id = intval($_GET['id'] ?? 0);
        if ($this->model->delete($id)) {
            $_SESSION['success'] = "Badge supprimé avec succès !";
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression";
        }
        header('Location: index.php?controller=badge&action=index');
        exit;
    }
    
    // =====================================================
    // API (JSON)
    // =====================================================
    
    public function getAll() {
        header('Content-Type: application/json');
        echo json_encode($this->model->getAll());
        exit;
    }
    
    public function showFideliteWithBadges() {
        header('Content-Type: application/json');
        $data = $this->model->getFidelitesWithBadges();
        echo json_encode($data);
        exit;
    }
    
    // =====================================================
    // PAGE - Programme Fidélité (anciennement jointure)
    // =====================================================
    
    public function fideliteBadges() {
        $fidelites = $this->model->getFidelitesWithBadges();
        $pageTitle = "Programme Fidélité";
        $activeTab = "fideliteBadges";
        ob_start();
        require_once __DIR__ . '/../View/backoffice/fidelite_badges_jointure.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
}
?>