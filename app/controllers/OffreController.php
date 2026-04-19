<?php
require_once __DIR__ . '/../models/Offre.php';

class OffreController {
    private $model;

    public function __construct() {
        $this->model = new Offre();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Liste des offres
     */
    public function liste() {
        $offres = $this->model->getAll();
        include __DIR__ . '/../views/back/offres/liste.php';
    }

    /**
     * Formulaire d'ajout
     */
    public function ajouter() {
        $abonnements = $this->model->getAbonnementsList();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'description' => $_POST['description'],
                'id_abonnement' => $_POST['id_abonnement']
            ];
            
            if ($this->model->create($data)) {
                $_SESSION['success'] = "Option ajoutée avec succès";
                header('Location: index.php?action=offres');
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout";
                header('Location: index.php?action=offres-ajouter');
            }
            exit();
        }
        
        include __DIR__ . '/../views/back/offres/ajouter.php';
    }

    /**
     * Formulaire de modification
     */
    public function modifier() {
        $id = $_GET['id'] ?? 0;
        $offre = $this->model->getById($id);
        $abonnements = $this->model->getAbonnementsList();
        
        if (!$offre) {
            header('Location: index.php?action=offres');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'description' => $_POST['description'],
                'id_abonnement' => $_POST['id_abonnement']
            ];
            
            if ($this->model->update($id, $data)) {
                $_SESSION['success'] = "Option modifiée avec succès";
                header('Location: index.php?action=offres');
            } else {
                $_SESSION['error'] = "Erreur lors de la modification";
                header("Location: index.php?action=offres-modifier&id=$id");
            }
            exit();
        }
        
        include __DIR__ . '/../views/back/offres/modifier.php';
    }

    /**
     * Supprimer une offre
     */
    public function supprimer() {
        $id = $_GET['id'] ?? 0;
        
        if ($this->model->delete($id)) {
            $_SESSION['success'] = "Option supprimée avec succès";
        } else {
            $_SESSION['error'] = "Erreur lors de la suppression";
        }
        
        header('Location: index.php?action=offres');
        exit();
    }

    /**
     * Détail d'une offre
     */
    public function detail() {
        $id = $_GET['id'] ?? 0;
        $offre = $this->model->getById($id);
        
        if (!$offre) {
            header('Location: index.php?action=offres');
            exit();
        }
        
        // Récupérer l'abonnement associé
        $abonnement = null;
        if ($offre['id_abonnement']) {
            require_once __DIR__ . '/../models/Abonnement.php';
            $abonnementModel = new Abonnement();
            $abonnement = $abonnementModel->getById($offre['id_abonnement']);
        }
        
        include __DIR__ . '/../views/back/offres/detail.php';
    }
}
?>