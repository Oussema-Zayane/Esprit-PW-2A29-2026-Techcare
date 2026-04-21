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
    
    // READ - Afficher la liste des badges
    public function index() {
        $badges = $this->model->getAll();
        $pageTitle = "Gestion des Badges";
        $activeTab = "badges";
        ob_start();
        require_once __DIR__ . '/../View/backoffice/badge_index.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    // CREATE - Afficher le formulaire d'ajout
    public function create() {
        $pageTitle = "Ajouter un badge";
        $activeTab = "badges";
        ob_start();
        require_once __DIR__ . '/../View/backoffice/badge_create.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    // CREATE - Enregistrer en base
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = trim($_POST['nom'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $icon = trim($_POST['icon'] ?? '🏅');
            $seuilPoints = intval($_POST['seuilPoints'] ?? 0);
            
            // Contrôle de saisie PHP
            $errors = [];
            if (empty($nom)) {
                $errors['nom'] = "Le nom du badge est obligatoire";
            } elseif (strlen($nom) < 3) {
                $errors['nom'] = "Le nom doit contenir au moins 3 caractères";
            } elseif (strlen($nom) > 50) {
                $errors['nom'] = "Le nom ne doit pas dépasser 50 caractères";
            }
            
            if (empty($description)) {
                $errors['description'] = "La description est obligatoire";
            } elseif (strlen($description) < 10) {
                $errors['description'] = "La description doit contenir au moins 10 caractères";
            } elseif (strlen($description) > 200) {
                $errors['description'] = "La description ne doit pas dépasser 200 caractères";
            }
            
            if ($seuilPoints <= 0) {
                $errors['seuilPoints'] = "Le seuil de points doit être un nombre positif";
            } elseif ($seuilPoints > 10000) {
                $errors['seuilPoints'] = "Le seuil de points ne peut pas dépasser 10 000";
            }
            
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $_POST;
                header('Location: index.php?controller=badge&action=create');
                exit;
            }
            
            if ($this->model->create($nom, $description, $icon, $seuilPoints)) {
                $_SESSION['success'] = "Badge ajouté avec succès !";
                header('Location: index.php?controller=badge&action=index');
                exit;
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout";
                header('Location: index.php?controller=badge&action=create');
                exit;
            }
        }
    }
    
    // UPDATE - Afficher le formulaire d'édition
    public function edit() {
        $id = intval($_GET['id'] ?? 0);
        $badge = $this->model->getById($id);
        if (!$badge) {
            header('Location: index.php?controller=badge&action=index');
            exit;
        }
        $pageTitle = "Modifier un badge";
        $activeTab = "badges";
        ob_start();
        require_once __DIR__ . '/../View/backoffice/badge_edit.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    // UPDATE - Mettre à jour en base
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id'] ?? 0);
            $nom = trim($_POST['nom'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $icon = trim($_POST['icon'] ?? '🏅');
            $seuilPoints = intval($_POST['seuilPoints'] ?? 0);
            
            // Contrôle de saisie PHP
            $errors = [];
            if (empty($nom)) {
                $errors['nom'] = "Le nom du badge est obligatoire";
            } elseif (strlen($nom) < 3) {
                $errors['nom'] = "Le nom doit contenir au moins 3 caractères";
            }
            
            if (empty($description)) {
                $errors['description'] = "La description est obligatoire";
            } elseif (strlen($description) < 10) {
                $errors['description'] = "La description doit contenir au moins 10 caractères";
            }
            
            if ($seuilPoints <= 0) {
                $errors['seuilPoints'] = "Le seuil de points doit être un nombre positif";
            }
            
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header("Location: index.php?controller=badge&action=edit&id=$id");
                exit;
            }
            
            if ($this->model->update($id, $nom, $description, $icon, $seuilPoints)) {
                $_SESSION['success'] = "Badge modifié avec succès !";
                header('Location: index.php?controller=badge&action=index');
                exit;
            } else {
                $_SESSION['error'] = "Erreur lors de la modification";
                header("Location: index.php?controller=badge&action=edit&id=$id");
                exit;
            }
        }
    }
    
    // DELETE - Supprimer un badge
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
    // API
    // =====================================================
    
    public function getAll() {
        header('Content-Type: application/json');
        echo json_encode($this->model->getAll());
        exit;
    }
}
?>