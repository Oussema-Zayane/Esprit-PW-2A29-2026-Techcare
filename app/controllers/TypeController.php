<?php
require_once __DIR__ . '/../models/Type.php';

class TypeController {
    private $model;

    public function __construct() {
        $this->model = new Type();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ========== VUES (HTML) ==========

    /**
     * Afficher la liste des types d'abonnement
     */
    public function liste() {
        $types = $this->model->getAll();
        include __DIR__ . '/../views/back/types/liste.php';
    }

    /**
     * Afficher formulaire d'ajout
     */
    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier si le nom existe déjà
            if ($this->model->nomExists($_POST['nom_type'])) {
                $_SESSION['error'] = "Ce nom de type existe déjà";
                header('Location: index.php?action=types-ajouter');
                exit();
            }

            $data = [
                'nom_type' => htmlspecialchars($_POST['nom_type']),
                'prix_base' => floatval($_POST['prix_base']),
                'duree_jours' => intval($_POST['duree_jours']),
                'description' => htmlspecialchars($_POST['description'] ?? ''),
                'icone' => htmlspecialchars($_POST['icone'] ?? 'tag')
            ];

            $result = $this->model->create($data);

            if ($result) {
                $_SESSION['success'] = "Type d'abonnement ajouté avec succès";
                header('Location: index.php?action=types');
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout";
                header('Location: index.php?action=types-ajouter');
            }
            exit();
        }

        include __DIR__ . '/../views/back/types/ajouter.php';
    }

    /**
     * Afficher formulaire de modification
     */
    public function modifier() {
        $id = intval($_GET['id'] ?? 0);
        $type = $this->model->getById($id);

        if (!$type) {
            header('Location: index.php?action=types');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier si le nom existe déjà (sauf pour ce type)
            if ($this->model->nomExists($_POST['nom_type'], $id)) {
                $_SESSION['error'] = "Ce nom de type existe déjà";
                header("Location: index.php?action=types-modifier&id=$id");
                exit();
            }

            $data = [
                'nom_type' => htmlspecialchars($_POST['nom_type']),
                'prix_base' => floatval($_POST['prix_base']),
                'duree_jours' => intval($_POST['duree_jours']),
                'description' => htmlspecialchars($_POST['description'] ?? ''),
                'icone' => htmlspecialchars($_POST['icone'] ?? 'tag')
            ];

            $result = $this->model->update($id, $data);

            if ($result) {
                $_SESSION['success'] = "Type d'abonnement modifié avec succès";
                header('Location: index.php?action=types');
            } else {
                $_SESSION['error'] = "Erreur lors de la modification";
                header("Location: index.php?action=types-modifier&id=$id");
            }
            exit();
        }

        include __DIR__ . '/../views/back/types/modifier.php';
    }

    /**
     * Supprimer un type
     */
    public function supprimer() {
        $id = intval($_GET['id'] ?? 0);
        
        $result = $this->model->delete($id);
        
        if ($result) {
            $_SESSION['success'] = "Type d'abonnement supprimé avec succès";
        } else {
            $_SESSION['error'] = "Impossible de supprimer ce type (il est utilisé par des abonnements)";
        }
        
        header('Location: index.php?action=types');
        exit();
    }

    /**
     * Afficher le détail d'un type
     */
    public function detail() {
        $id = intval($_GET['id'] ?? 0);
        $type = $this->model->getById($id);

        if (!$type) {
            header('Location: index.php?action=types');
            exit();
        }

        include __DIR__ . '/../views/back/types/detail.php';
    }

    // ========== API (JSON) pour AJAX ==========

    /**
     * API: Liste des types (JSON)
     */
    public function apiListe() {
        header('Content-Type: application/json');
        $types = $this->model->getAll();
        echo json_encode($types);
        exit();
    }

    /**
     * API: Ajouter un type (JSON)
     */
    public function apiAjouter() {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            echo json_encode(['success' => false, 'error' => 'Données invalides']);
            exit();
        }

        $data = [
            'nom_type' => htmlspecialchars($input['nom_type']),
            'prix_base' => floatval($input['prix_base']),
            'duree_jours' => intval($input['duree_jours']),
            'description' => htmlspecialchars($input['description'] ?? ''),
            'icone' => htmlspecialchars($input['icone'] ?? 'tag')
        ];

        $id = $this->model->create($data);

        if ($id) {
            echo json_encode(['success' => true, 'id' => $id]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'ajout']);
        }
        exit();
    }

    /**
     * API: Modifier un type (JSON)
     */
    public function apiModifier() {
        header('Content-Type: application/json');

        $id = intval($_GET['id'] ?? 0);
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$id || !$input) {
            echo json_encode(['success' => false, 'error' => 'Données invalides']);
            exit();
        }

        $data = [
            'nom_type' => htmlspecialchars($input['nom_type']),
            'prix_base' => floatval($input['prix_base']),
            'duree_jours' => intval($input['duree_jours']),
            'description' => htmlspecialchars($input['description'] ?? ''),
            'icone' => htmlspecialchars($input['icone'] ?? 'tag')
        ];

        $result = $this->model->update($id, $data);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de la modification']);
        }
        exit();
    }

    /**
     * API: Supprimer un type (JSON)
     */
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
            echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression (type utilisé)']);
        }
        exit();
    }
}
?>