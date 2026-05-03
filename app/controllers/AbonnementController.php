<?php
require_once __DIR__ . '/../models/Abonnement.php';
require_once __DIR__ . '/../../config/database.php';

class AbonnementController {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    // ========== LOGIQUE DE DONNÉES (Précédemment dans le Model) ==========

    public function getAll() {
        $sql = "SELECT a.*, 
                       t.nom_type, 
                       t.prix_base,
                       t.duree_jours
                FROM abonnements a
                LEFT JOIN type_abonnement t ON a.type_abonnement = t.nom_type
                ORDER BY a.date_debut DESC";
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("
            SELECT a.*, 
                   t.nom_type, 
                   t.prix_base,
                   t.duree_jours
            FROM abonnements a
            LEFT JOIN type_abonnement t ON a.type_abonnement = t.nom_type
            WHERE a.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function create($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO abonnements (nom_client, email, telephone, type_abonnement, prix, date_debut, date_fin, statut)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'actif')
        ");
        
        if (!$stmt) {
            error_log("Erreur préparation create: " . $this->conn->error);
            return false;
        }
        
        $email = $data['email'] ?? null;
        $telephone = $data['telephone'] ?? null;
        $stmt->bind_param("ssssdss",
            $data['nom_client'],
            $email,
            $telephone,
            $data['type_abonnement'],
            $data['prix'],
            $data['date_debut'],
            $data['date_fin']
        );
        
        if (!$stmt->execute()) {
            error_log("Erreur exécution create: " . $stmt->error);
            return false;
        }
        
        return $stmt->insert_id;
    }

    public function update($id, $data) {
        $stmt = $this->conn->prepare("
            UPDATE abonnements 
            SET nom_client=?, email=?, type_abonnement=?, prix=?, date_debut=?, date_fin=?, statut=?
            WHERE id=?
        ");
        
        if (!$stmt) {
            return false;
        }
        
        $email = $data['email'] ?? null;
        $stmt->bind_param("sssdsssi",
            $data['nom_client'],
            $email,
            $data['type_abonnement'],
            $data['prix'],
            $data['date_debut'],
            $data['date_fin'],
            $data['statut'],
            $id
        );
        
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM abonnements WHERE id = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getOptions($abonnement_id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM offre WHERE id_abonnement = ?
        ");
        $stmt->bind_param("i", $abonnement_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getByUserId($user_id) {
        $stmt = $this->conn->prepare("
            SELECT a.*, 
                   t.nom_type, 
                   t.prix_base,
                   t.duree_jours
            FROM abonnements a
            LEFT JOIN type_abonnement t ON a.type_abonnement = t.nom_type
            WHERE a.user_id = ?
            ORDER BY a.date_debut DESC
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function getByEmail($email) {
        $stmt = $this->conn->prepare("
            SELECT a.*, 
                   t.nom_type, 
                   t.prix_base,
                   t.duree_jours
            FROM abonnements a
            LEFT JOIN type_abonnement t ON a.type_abonnement = t.nom_type
            WHERE a.email = ?
            ORDER BY a.date_debut DESC
        ");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function getByTypeName($type_abonnement) {
        $stmt = $this->conn->prepare("
            SELECT a.*, t.nom_type, t.prix_base, t.duree_jours
            FROM abonnements a
            INNER JOIN type_abonnement t ON a.type_abonnement = t.nom_type
            WHERE a.type_abonnement = ?
            ORDER BY a.date_debut DESC
        ");
        $stmt->bind_param("s", $type_abonnement);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function getByTypeId($id_type) {
        $stmt = $this->conn->prepare("
            SELECT a.*, t.nom_type, t.prix_base, t.duree_jours
            FROM abonnements a
            INNER JOIN type_abonnement t ON a.type_abonnement = t.nom_type
            WHERE t.id_type = ?
            ORDER BY a.date_debut DESC
        ");
        $stmt->bind_param("i", $id_type);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function rechercheAvancee($type_abonnement = null, $statut = null, $nom_client = null, $email = null, $date_debut_min = null, $date_debut_max = null, $prix_min = null, $prix_max = null) {
        $sql = "SELECT a.*, t.nom_type, t.prix_base, t.duree_jours 
                FROM abonnements a 
                LEFT JOIN type_abonnement t ON a.type_abonnement = t.nom_type 
                WHERE 1=1";
        $params = [];
        $types = "";
        
        if ($type_abonnement && $type_abonnement !== '') {
            $sql .= " AND a.type_abonnement = ?";
            $params[] = $type_abonnement;
            $types .= "s";
        }
        if ($statut && $statut !== '') {
            $sql .= " AND a.statut = ?";
            $params[] = $statut;
            $types .= "s";
        }
        if ($nom_client && $nom_client !== '') {
            $sql .= " AND a.nom_client LIKE ?";
            $params[] = "%$nom_client%";
            $types .= "s";
        }
        if ($email && $email !== '') {
            $sql .= " AND a.email LIKE ?";
            $params[] = "%$email%";
            $types .= "s";
        }
        if ($date_debut_min && $date_debut_min !== '') {
            $sql .= " AND a.date_debut >= ?";
            $params[] = $date_debut_min;
            $types .= "s";
        }
        if ($date_debut_max && $date_debut_max !== '') {
            $sql .= " AND a.date_debut <= ?";
            $params[] = $date_debut_max;
            $types .= "s";
        }
        if ($prix_min && $prix_min !== '') {
            $sql .= " AND a.prix >= ?";
            $params[] = (float)$prix_min;
            $types .= "d";
        }
        if ($prix_max && $prix_max !== '') {
            $sql .= " AND a.prix <= ?";
            $params[] = (float)$prix_max;
            $types .= "d";
        }
        
        $sql .= " ORDER BY a.date_debut DESC";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Erreur prepare rechercheAvancee: " . $this->conn->error);
            return [];
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function rechercheParCritere($type_abonnement = null, $nom_client = null, $email = null, $date_debut = null, $prix = null, $date_fin = null) {
        $sql = "SELECT a.*, t.nom_type, t.prix_base, t.duree_jours 
                FROM abonnements a 
                LEFT JOIN type_abonnement t ON a.type_abonnement = t.nom_type 
                WHERE 1=1";
        $params = [];
        $types = "";
        
        if ($type_abonnement && $type_abonnement !== '') {
            $sql .= " AND a.type_abonnement = ?";
            $params[] = $type_abonnement;
            $types .= "s";
        }
        if ($nom_client && $nom_client !== '') {
            $sql .= " AND a.nom_client LIKE ?";
            $params[] = "%$nom_client%";
            $types .= "s";
        }
        if ($email && $email !== '') {
            $sql .= " AND a.email LIKE ?";
            $params[] = "%$email%";
            $types .= "s";
        }
        if ($date_debut && $date_debut !== '') {
            $sql .= " AND a.date_debut = ?";
            $params[] = $date_debut;
            $types .= "s";
        }
        if ($prix && $prix !== '') {
            $sql .= " AND a.prix = ?";
            $params[] = (float)$prix;
            $types .= "d";
        }
        if ($date_fin && $date_fin !== '') {
            $sql .= " AND a.date_fin = ?";
            $params[] = $date_fin;
            $types .= "s";
        }
        
        $sql .= " ORDER BY a.date_debut DESC";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Erreur prepare rechercheParCritere: " . $this->conn->error);
            return [];
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function search($filters = []) {
        $type_abonnement = $filters['type_abonnement'] ?? null;
        $statut = $filters['statut'] ?? null;
        $nom_client = $filters['nom_client'] ?? null;
        $email = $filters['email'] ?? null;
        $date_debut_min = $filters['date_debut_min'] ?? null;
        $date_debut_max = $filters['date_debut_max'] ?? null;
        $prix_min = $filters['prix_min'] ?? null;
        $prix_max = $filters['prix_max'] ?? null;
        
        return $this->rechercheAvancee($type_abonnement, $statut, $nom_client, $email, $date_debut_min, $date_debut_max, $prix_min, $prix_max);
    }

    public function createWithUser($data, $user_id) {
        $stmt = $this->conn->prepare("
            INSERT INTO abonnements (nom_client, email, type_abonnement, prix, date_debut, date_fin, statut, user_id)
            VALUES (?, ?, ?, ?, ?, ?, 'actif', ?)
        ");
        
        if (!$stmt) {
            error_log("Erreur préparation createWithUser: " . $this->conn->error);
            return false;
        }
        
        $email = $data['email'] ?? null;
        $stmt->bind_param("sssdssi",
            $data['nom_client'],
            $email,
            $data['type_abonnement'],
            $data['prix'],
            $data['date_debut'],
            $data['date_fin'],
            $user_id
        );
        
        if (!$stmt->execute()) {
            error_log("Erreur exécution createWithUser: " . $stmt->error);
            return false;
        }
        
        return $stmt->insert_id;
    }

    public function updateFront($id, $data) {
        $stmt = $this->conn->prepare("
            UPDATE abonnements 
            SET type_abonnement=?, date_debut=?, date_fin=?
            WHERE id=?
        ");
        
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("sssi",
            $data['type_abonnement'],
            $data['date_debut'],
            $data['date_fin'],
            $id
        );
        
        return $stmt->execute();
    }

    public function count() {
        $result = $this->conn->query("SELECT COUNT(*) as total FROM abonnements");
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total'] ?? 0;
        }
        return 0;
    }

    public function countActive() {
        $result = $this->conn->query("SELECT COUNT(*) as total FROM abonnements WHERE statut = 'actif'");
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total'] ?? 0;
        }
        return 0;
    }

    public function getTotalRevenue() {
        $result = $this->conn->query("SELECT SUM(prix) as revenue FROM abonnements WHERE statut = 'actif'");
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['revenue'] ?? 0;
        }
        return 0;
    }

    public function getStatsByType() {
        $sql = "SELECT 
                    t.id_type,
                    t.nom_type,
                    t.prix_base,
                    COUNT(a.id) as total,
                    COALESCE(SUM(a.prix), 0) as revenus,
                    COALESCE(AVG(a.prix), 0) as moyenne
                FROM type_abonnement t
                LEFT JOIN abonnements a ON t.nom_type = a.type_abonnement
                GROUP BY t.id_type
                ORDER BY total DESC";
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    // ========== VUES (HTML) ==========
    
    // Afficher la liste des abonnements
    public function liste() {
        $abonnements = $this->getAll();
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
                'date_fin' => $_POST['date_fin'],
                'email' => $_POST['email'] ?? null,
                'telephone' => $_POST['telephone'] ?? null
            ];
            
            $result = $this->create($data);
            
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
        $abonnement = $this->getById($id);
        
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
                'statut' => $_POST['statut'],
                'email' => $_POST['email'] ?? null
            ];
            
            $result = $this->update($id, $data);
            
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
        $this->delete($id);
        header('Location: index.php?action=liste');
        exit();
    }

    // Afficher le détail d'un abonnement
    public function detail() {
        $id = intval($_GET['id'] ?? 0);
        $abonnement = $this->getById($id);
        
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
        $abonnements = $this->getAll();
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
            'date_fin' => $input['date_fin'],
            'email' => $input['email'] ?? null,
            'telephone' => $input['telephone'] ?? null
        ];
        
        $id = $this->create($data);
        
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
            'statut' => $input['statut'],
            'email' => $input['email'] ?? null
        ];
        
        $result = $this->update($id, $data);
        
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
        
        $result = $this->delete($id);
        
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erreur lors de la suppression']);
        }
        exit();
    }
}
?>