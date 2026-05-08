<?php
require_once __DIR__ . '/../models/Offre.php';
require_once __DIR__ . '/../models/Type.php';
require_once __DIR__ . '/../../config/database.php';

class OffreController {
    private $conn;
    private $model;

    public function __construct() {
        $this->conn = Database::getConnection();
        $this->model = new Offre();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Liste des offres (options supplémentaires)
     */
    public function liste() {
        $offres = $this->model->getAll();
        include __DIR__ . '/../views/back/offres/liste.php';
    }

    /**
     * Formulaire d'ajout
     */
    public function ajouter() {
        $types = $this->model->getTypesList();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation
            if (empty($_POST['description'])) {
                $_SESSION['error'] = "La description est obligatoire";
                header('Location: index.php?action=offres-ajouter');
                exit();
            }
            
            $data = [
                'description' => htmlspecialchars($_POST['description']),
                'id_type' => isset($_POST['id_type']) && is_array($_POST['id_type']) ? $_POST['id_type'] : []
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
        $types = $this->model->getTypesList();
        
        if (!$offre) {
            $_SESSION['error'] = "Option introuvable";
            header('Location: index.php?action=offres');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation
            if (empty($_POST['description'])) {
                $_SESSION['error'] = "La description est obligatoire";
                header("Location: index.php?action=offres-modifier&id=$id");
                exit();
            }
            
            $data = [
                'description' => htmlspecialchars($_POST['description']),
                'id_type' => isset($_POST['id_type']) && is_array($_POST['id_type']) ? $_POST['id_type'] : []
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
            $_SESSION['error'] = "Option introuvable";
            header('Location: index.php?action=offres');
            exit();
        }
        
        // Récupérer les types d'abonnement associés
        $types = [];
        if (!empty($offre['types'])) {
            $ids = implode(',', array_map('intval', $offre['types']));
            if (!empty($ids)) {
                $stmt = $this->conn->prepare("SELECT * FROM type_abonnement WHERE id_type IN ($ids)");
                $stmt->execute();
                $types = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            }
        }
        
        include __DIR__ . '/../views/back/offres/detail.php';
    }
}
?>