<?php
require_once __DIR__ . '/../models/Type.php';
require_once __DIR__ . '/../models/Offre.php';
require_once __DIR__ . '/../../config/database.php';

class TypeController {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ========== LOGIQUE DE DONNÉES ==========

    public function getAll() {
        $sql = "SELECT * FROM type_abonnement ORDER BY id_type ASC";
        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM type_abonnement WHERE id_type = ?");
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getByNom($nom_type) {
        $stmt = $this->conn->prepare("SELECT * FROM type_abonnement WHERE nom_type = ?");
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param("s", $nom_type);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Récupérer les offres associées à un type d'abonnement
     */
    public function getOffresByTypeId($id_type) {
        $sql = "SELECT o.* 
                FROM offre o
                INNER JOIN type_abonnement_offre tof ON o.id = tof.id_offre
                WHERE tof.id_type = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param("i", $id_type);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO type_abonnement (nom_type, prix_base, duree_jours, description, icone)
            VALUES (?, ?, ?, ?, ?)
        ");
        if (!$stmt) {
            error_log("[TECHCARE] Erreur prepare create type: " . $this->conn->error);
            return false;
        }
        
        $icone = $data['icone'] ?? 'tag';
        $description = $data['description'] ?? '';
        
        $stmt->bind_param("sdiss",
            $data['nom_type'],
            $data['prix_base'],
            $data['duree_jours'],
            $description,
            $icone
        );

        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }
        error_log("[TECHCARE] Erreur execute create type: " . $stmt->error);
        return false;
    }

    public function update($id, $data) {
        $stmt = $this->conn->prepare("
            UPDATE type_abonnement
            SET nom_type = ?, prix_base = ?, duree_jours = ?, description = ?, icone = ?
            WHERE id_type = ?
        ");
        if (!$stmt) {
            error_log("[TECHCARE] Erreur prepare update type: " . $this->conn->error);
            return false;
        }
        
        $icone = $data['icone'] ?? 'tag';
        $description = $data['description'] ?? '';
        
        $stmt->bind_param("sdissi",
            $data['nom_type'],
            $data['prix_base'],
            $data['duree_jours'],
            $description,
            $icone,
            $id
        );

        $result = $stmt->execute();
        if (!$result) {
            error_log("[TECHCARE] Erreur execute update type: " . $stmt->error);
        }
        return $result;
    }

    public function delete($id) {
        // Vérifier si le type est utilisé dans des abonnements
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as count 
            FROM abonnements a
            WHERE a.type_abonnement = (SELECT nom_type FROM type_abonnement WHERE id_type = ?)
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            error_log("[TECHCARE] Impossible de supprimer le type, il est utilisé par {$row['count']} abonnement(s)");
            return false;
        }

        $stmt = $this->conn->prepare("DELETE FROM type_abonnement WHERE id_type = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function nomExists($nom_type, $exclude_id = null) {
        $sql = "SELECT id_type FROM type_abonnement WHERE nom_type = ?";
        $params = [$nom_type];
        $types = "s";

        if ($exclude_id) {
            $sql .= " AND id_type != ?";
            $params[] = $exclude_id;
            $types .= "i";
        }

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function getStats() {
        $sql = "SELECT 
                    t.id_type,
                    t.nom_type,
                    t.prix_base,
                    t.duree_jours,
                    COUNT(a.id) as total_abonnements,
                    COALESCE(SUM(a.prix), 0) as revenus_totaux,
                    COALESCE(AVG(a.prix), 0) as prix_moyen
                FROM type_abonnement t
                LEFT JOIN abonnements a ON t.nom_type = a.type_abonnement
                GROUP BY t.id_type
                ORDER BY total_abonnements DESC";
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function count() {
        $result = $this->conn->query("SELECT COUNT(*) as total FROM type_abonnement");
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total'] ?? 0;
        }
        return 0;
    }

    // ========== VUES (HTML) ==========

    /**
     * Afficher la liste des types d'abonnement
     */
    public function liste() {
        $types = $this->getAll();
        include __DIR__ . '/../views/back/types/liste.php';
    }

    /**
     * Afficher formulaire d'ajout avec gestion des offres
     */
    public function ajouter() {
        // Récupérer les offres existantes pour affichage
        $offreModel = new Offre();
        $offres = $offreModel->getAll();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier si le nom existe déjà
            if ($this->nomExists($_POST['nom_type'])) {
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

            $typeId = $this->create($data);

            if ($typeId) {
                // ═══ ASSOCIER LES OFFRES SÉLECTIONNÉES ═══
                if (isset($_POST['offres_selected']) && is_array($_POST['offres_selected'])) {
                    $offresCount = 0;
                    foreach ($_POST['offres_selected'] as $offreId) {
                        $stmt = $this->conn->prepare("
                            INSERT IGNORE INTO type_abonnement_offre (id_type, id_offre)
                            VALUES (?, ?)
                        ");
                        if ($stmt) {
                            $stmt->bind_param("ii", $typeId, $offreId);
                            $stmt->execute();
                            $offresCount++;
                        }
                    }
                    $_SESSION['success'] = "Type d'abonnement ajouté avec succès" . 
                        ($offresCount > 0 ? " et $offresCount offre(s) associée(s)" : "");
                } else {
                    $_SESSION['success'] = "Type d'abonnement ajouté avec succès";
                }
                header('Location: index.php?action=types');
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout du type";
                header('Location: index.php?action=types-ajouter');
            }
            exit();
        }

        include __DIR__ . '/../views/back/types/ajouter.php';
    }

    /**
     * Afficher formulaire de modification avec gestion des offres
     */
    public function modifier() {
        $id = intval($_GET['id'] ?? 0);
        $type = $this->getById($id);

        if (!$type) {
            header('Location: index.php?action=types');
            exit();
        }

        // Récupérer toutes les offres disponibles
        $offreModel = new Offre();
        $allOffres = $offreModel->getAll();
        
        // Récupérer les offres déjà associées à ce type
        $offresAssociees = $this->getOffresByTypeId($id);
        $offresAssocieesIds = array_column($offresAssociees, 'id');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérifier si le nom existe déjà (sauf pour ce type)
            if ($this->nomExists($_POST['nom_type'], $id)) {
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

            $result = $this->update($id, $data);

            if ($result) {
                // ═══ METTRE À JOUR LES OFFRES ASSOCIÉES ═══
                // Supprimer toutes les associations existantes
                $stmt = $this->conn->prepare("DELETE FROM type_abonnement_offre WHERE id_type = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                
                // Ajouter les nouvelles associations
                if (isset($_POST['offres_selected']) && is_array($_POST['offres_selected'])) {
                    $offresCount = 0;
                    foreach ($_POST['offres_selected'] as $offreId) {
                        $stmt = $this->conn->prepare("
                            INSERT INTO type_abonnement_offre (id_type, id_offre)
                            VALUES (?, ?)
                        ");
                        $stmt->bind_param("ii", $id, $offreId);
                        $stmt->execute();
                        $offresCount++;
                    }
                    $_SESSION['success'] = "Type d'abonnement modifié avec succès" . 
                        ($offresCount > 0 ? " et $offresCount offre(s) associée(s)" : "");
                } else {
                    $_SESSION['success'] = "Type d'abonnement modifié avec succès";
                }
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
        
        $result = $this->delete($id);
        
        if ($result) {
            $_SESSION['success'] = "Type d'abonnement supprimé avec succès";
        } else {
            $_SESSION['error'] = "Impossible de supprimer ce type (il est utilisé par des abonnements)";
        }
        
        header('Location: index.php?action=types');
        exit();
    }

    /**
     * Afficher le détail d'un type avec ses offres associées
     */
    public function detail() {
        $id = intval($_GET['id'] ?? 0);
        $type = $this->getById($id);

        if (!$type) {
            header('Location: index.php?action=types');
            exit();
        }

        // Récupérer les offres associées à ce type
        $offres = $this->getOffresByTypeId($id);

        include __DIR__ . '/../views/back/types/detail.php';
    }

    // ========== API (JSON) pour AJAX ==========

    /**
     * API: Liste des types (JSON)
     */
    public function apiListe() {
        header('Content-Type: application/json');
        $types = $this->getAll();
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

        $id = $this->create($data);

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

        $result = $this->update($id, $data);

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

        $result = $this->delete($id);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression (type utilisé)']);
        }
        exit();
    }
}
?>