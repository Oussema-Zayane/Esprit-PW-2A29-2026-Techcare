<?php
require_once __DIR__ . '/../Model/FideliteModel.php';

class FideliteController {
    private $model;
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->model = new FideliteModel();
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
            $_SESSION['username'] = 'Ayari Firas';
            $_SESSION['role'] = 'admin';
        }
    }
    
    // =====================================================
    // BACKOFFICE - CRUD Fidélité (Gestion complète)
    // =====================================================
    
    // READ - Afficher la liste (BackOffice)
    public function index() {
        $fidelites = $this->model->getAll();
        $pageTitle = "Gestion de la Fidélité (BackOffice)";
        $activeTab = "fidelite";
        ob_start();
        require_once __DIR__ . '/../View/backoffice/fidelite_index.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    // CREATE - Afficher le formulaire d'ajout (BackOffice)
    public function create() {
        $pageTitle = "Ajouter une offre de fidélité";
        $activeTab = "fidelite";
        ob_start();
        require_once __DIR__ . '/../View/backoffice/fidelite_create.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    // CREATE - Enregistrer en base (BackOffice)
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $total = intval($_POST['total'] ?? 0);
            $dateGain = $_POST['dateGain'] ?? date('Y-m-d');
            
            // Contrôle de saisie PHP
            $errors = [];
            if (empty($titre)) {
                $errors['titre'] = "Le titre est obligatoire";
            } elseif (strlen($titre) < 3) {
                $errors['titre'] = "Le titre doit contenir au moins 3 caractères";
            }
            if (empty($description)) {
                $errors['description'] = "La description est obligatoire";
            } elseif (strlen($description) < 10) {
                $errors['description'] = "La description doit contenir au moins 10 caractères";
            }
            if ($total < 0) {
                $errors['total'] = "Le nombre de points ne peut pas être négatif";
            }
            
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $_POST;
                header('Location: index.php?controller=fidelite&action=create');
                exit;
            }
            
            if ($this->model->create($titre, $description, $total, $dateGain)) {
                $_SESSION['success'] = "Offre de fidélité ajoutée avec succès !";
                header('Location: index.php?controller=fidelite&action=index');
                exit;
            }
        }
    }
    
    // UPDATE - Afficher le formulaire d'édition (BackOffice)
    public function edit() {
        $id = intval($_GET['id'] ?? 0);
        $fidelite = $this->model->getById($id);
        if (!$fidelite) {
            header('Location: index.php?controller=fidelite&action=index');
            exit;
        }
        $pageTitle = "Modifier une offre de fidélité";
        $activeTab = "fidelite";
        ob_start();
        require_once __DIR__ . '/../View/backoffice/fidelite_edit.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    // UPDATE - Mettre à jour en base (BackOffice)
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id'] ?? 0);
            $titre = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $total = intval($_POST['total'] ?? 0);
            $dateGain = $_POST['dateGain'] ?? date('Y-m-d');
            
            // Contrôle de saisie PHP
            $errors = [];
            if (empty($titre)) {
                $errors['titre'] = "Le titre est obligatoire";
            } elseif (strlen($titre) < 3) {
                $errors['titre'] = "Le titre doit contenir au moins 3 caractères";
            }
            if (empty($description)) {
                $errors['description'] = "La description est obligatoire";
            } elseif (strlen($description) < 10) {
                $errors['description'] = "La description doit contenir au moins 10 caractères";
            }
            if ($total < 0) {
                $errors['total'] = "Le nombre de points ne peut pas être négatif";
            }
            
            if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                header("Location: index.php?controller=fidelite&action=edit&id=$id");
                exit;
            }
            
            if ($this->model->update($id, $titre, $description, $total, $dateGain)) {
                $_SESSION['success'] = "Offre modifiée avec succès !";
                header('Location: index.php?controller=fidelite&action=index');
                exit;
            }
        }
    }
    
    // DELETE - Supprimer un enregistrement (BackOffice)
    public function delete() {
        $id = intval($_GET['id'] ?? 0);
        if ($this->model->delete($id)) {
            $_SESSION['success'] = "Offre supprimée avec succès !";
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression";
        }
        header('Location: index.php?controller=fidelite&action=index');
        exit;
    }
    
    // =====================================================
    // FRONTOFFICE - Fidélité (Affichage des offres)
    // =====================================================
    
    // FrontOffice - Afficher les offres de fidélité (consultation)
    public function frontoffice() {
        $fidelites = $this->model->getAll();
        $userPoints = $this->model->getUserPoints($_SESSION['user_id']);
        
        $pageTitle = "Programme de Fidélité";
        $activeTab = "loyalty";
        ob_start();
        require_once __DIR__ . '/../View/frontoffice/loyalty.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    // =====================================================
    // FRONTOFFICE - Gestion personnelle des demandes (CRUD utilisateur)
    // =====================================================
    
    // FrontOffice - Gérer mes propres demandes
    public function manageFront() {
        $fidelites = $this->model->getAll();
        $userPoints = $this->model->getUserPoints($_SESSION['user_id']);
        
        $pageTitle = "Gérer mes demandes de fidélité";
        $activeTab = "manageFidelite";
        ob_start();
        require_once __DIR__ . '/../View/frontoffice/fidelite_manage.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    // CREATE - FrontOffice (ajouter une demande personnelle)
    public function storeFront() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $total = intval($_POST['total'] ?? 0);
            $dateGain = date('Y-m-d');
            
            // Contrôle de saisie PHP
            $errors = [];
            if (strlen($titre) < 3) $errors[] = "Titre trop court (min 3 caractères)";
            if (strlen($description) < 10) $errors[] = "Description trop courte (min 10 caractères)";
            if ($total < 0) $errors[] = "Points invalides";
            
            if (empty($errors)) {
                if ($this->model->create($titre, $description, $total, $dateGain)) {
                    $_SESSION['success'] = "Demande ajoutée avec succès !";
                } else {
                    $_SESSION['error'] = "Erreur lors de l'ajout";
                }
            } else {
                $_SESSION['error'] = implode(', ', $errors);
            }
        }
        header('Location: index.php?controller=fidelite&action=manageFront');
        exit;
    }
    
    // UPDATE - FrontOffice (afficher formulaire de modification)
    public function editFront() {
        $id = intval($_GET['id'] ?? 0);
        $fidelite = $this->model->getById($id);
        if (!$fidelite) {
            header('Location: index.php?controller=fidelite&action=manageFront');
            exit;
        }
        $pageTitle = "Modifier ma demande";
        $activeTab = "manageFidelite";
        ob_start();
        require_once __DIR__ . '/../View/backoffice/fidelite_edit.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    // UPDATE - FrontOffice (enregistrer modification)
    public function updateFront() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id'] ?? 0);
            $titre = trim($_POST['titre'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $total = intval($_POST['total'] ?? 0);
            $dateGain = date('Y-m-d');
            
            if ($this->model->update($id, $titre, $description, $total, $dateGain)) {
                $_SESSION['success'] = "Demande modifiée avec succès !";
            } else {
                $_SESSION['error'] = "Erreur lors de la modification";
            }
        }
        header('Location: index.php?controller=fidelite&action=manageFront');
        exit;
    }
    
    // DELETE - FrontOffice (supprimer une demande personnelle)
    public function deleteFront() {
        $id = intval($_GET['id'] ?? 0);
        $result = $this->model->delete($id);
        header('Content-Type: application/json');
        echo json_encode(['success' => $result]);
        exit;
    }
    
    // =====================================================
    // API (AJAX)
    // =====================================================
    
    // API - Récupérer toutes les fidélités
    public function getUserFidelites() {
        header('Content-Type: application/json');
        echo json_encode($this->model->getAll());
        exit;
    }
    
    // API - Récupérer toutes les fidélités avec points utilisateur
    public function getAll() {
        header('Content-Type: application/json');
        echo json_encode([
            'fidelites' => $this->model->getAll(),
            'userPoints' => $this->model->getUserPoints($_SESSION['user_id'])
        ]);
        exit;
    }
    
    // Ajouter des points (FrontOffice)
    public function addPoints() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $points = intval($_POST['points'] ?? 0);
            if ($points > 0) {
                $this->model->addUserPoints($_SESSION['user_id'], $points);
                $_SESSION['success'] = "+$points points ajoutés !";
            }
        }
        header('Location: index.php?controller=fidelite&action=frontoffice');
        exit;
    }
}
?>