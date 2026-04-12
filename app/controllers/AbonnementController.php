<?php
require_once __DIR__ . '/../models/Abonnement.php';

class AbonnementController {
    private $model;

    public function __construct() {
        $this->model = new Abonnement();
    }

    // ========== VUES (HTML) ==========
    
    // Afficher la liste des abonnements
    public function liste() {
        $abonnements = $this->model->getAll();
        require_once __DIR__ . '/../views/back/abonnements/liste.php';
    }

    // Afficher formulaire d'ajout
    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Traitement du formulaire
            $data = [
                'nom_client' => htmlspecialchars($_POST['nom_client']),
                'type_abonnement' => $_POST['type_abonnement'],
                'prix' => floatval($_POST['prix']),
                'date_debut' => $_POST['date_debut'],
                'date_fin' => $_POST['date_fin']
            ];
            
            $result = $this->model->create($data);
            
            if ($result) {
                header('Location: index.php?action=liste&success=1');
                exit();
            } else {
                $erreur = "Erreur lors de l'ajout";
            }
        }
        
        require_once __DIR__ . '/../views/back/abonnements/ajouter.php';
    }

    // Afficher formulaire de modification
    public function modifier() {
        $id = intval($_GET['id'] ?? 0);
        $abonnement = $this->model->getById($id);
        
        if (!$abonnement) {
            header('Location: index.php?action=liste');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom_client' => htmlspecialchars($_POST['nom_client']),
                'type_abonnement' => $_POST['type_abonnement'],
                'prix' => floatval($_POST['prix']),
                'date_debut' => $_POST['date_debut'],
                'date_fin' => $_POST['date_fin'],
                'statut' => $_POST['statut']
            ];
            
            $result = $this->model->update($id, $data);
            
            if ($result) {
                header('Location: index.php?action=liste&success=1');
                exit();
            } else {
                $erreur = "Erreur lors de la modification";
            }
        }
        
        require_once __DIR__ . '/../views/back/abonnements/modifier.php';
    }

    // Supprimer un abonnement
    public function supprimer() {
        $id = intval($_GET['id'] ?? 0);
        $this->model->delete($id);
        header('Location: index.php?action=liste');
        exit();
    }

    // Afficher le détail d'un abonnement
    public function detail() {
        $id = intval($_GET['id'] ?? 0);
        $abonnement = $this->model->getById($id);
        
        if (!$abonnement) {
            header('Location: index.php?action=liste');
            exit();
        }
        
        require_once __DIR__ . '/../views/back/abonnements/detail.php';
    }

    // ========== API (JSON) pour AJAX ==========
    
    // API: Liste des abonnements (JSON)
    public function apiListe() {
        header('Content-Type: application/json');
        $abonnements = $this->model->getAll();
        echo json_encode($abonnements);
        exit();
    }

    // API: Ajouter un abonnement (JSON)
    public function apiAjouter() {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            echo json_encode(['success' => false, 'error' => 'Données invalides']);
            exit();
        }
        
        $data = [
            'nom_client' => htmlspecialchars($input['nom_client']),
            'type_abonnement' => $input['type_abonnement'],
            'prix' => floatval($input['prix']),
            'date_debut' => $input['date_debut'],
            'date_fin' => $input['date_fin']
        ];
        
        $id = $this->model->create($data);
        
        if ($id) {
            echo json_encode(['success' => true, 'id' => $id]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'ajout']);
        }
        exit();
    }

    // API: Modifier un abonnement (JSON)
    public function apiModifier() {
        header('Content-Type: application/json');
        
        $id = intval($_GET['id'] ?? 0);
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$id || !$input) {
            echo json_encode(['success' => false, 'error' => 'Données invalides']);
            exit();
        }
        
        $data = [
            'nom_client' => htmlspecialchars($input['nom_client']),
            'type_abonnement' => $input['type_abonnement'],
            'prix' => floatval($input['prix']),
            'date_debut' => $input['date_debut'],
            'date_fin' => $input['date_fin'],
            'statut' => $input['statut']
        ];
        
        $result = $this->model->update($id, $data);
        
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de la modification']);
        }
        exit();
    }

    // API: Supprimer un abonnement (JSON)
    public function apiSupprimer() {
        header('Content-Type: application/json');
        
        $id = intval($_GET['id'] ?? 0);
        
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'ID manquant']);
            exit();
        }
        
        $result = $this->model->delete($id);
        
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression']);
        }
        exit();
    }
}
?>