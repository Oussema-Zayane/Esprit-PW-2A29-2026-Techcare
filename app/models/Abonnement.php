<?php
require_once __DIR__ . '/../../config/database.php';

class Abonnement {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    /**
     * Récupérer tous les abonnements
     */
    public function getAll() {
        $sql = "SELECT id, nom_client, email, type_abonnement, prix, date_debut, date_fin, statut, created_at FROM abonnements ORDER BY date_debut DESC";
        $result = $this->conn->query($sql);
        
        // Vérifier si la requête a réussi
        if (!$result) {
            // Si la table n'existe pas, créer les tables
            $this->createTables();
            $result = $this->conn->query($sql);
        }
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        return [];
    }

    /**
     * Récupérer un abonnement par ID
     */
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM abonnements WHERE id = ?");
        if (!$stmt) {
            return null;
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Créer un abonnement
     */
    public function create($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO abonnements (nom_client, email, type_abonnement, prix, date_debut, date_fin, statut)
            VALUES (?, ?, ?, ?, ?, ?, 'actif')
        ");
        
        if (!$stmt) {
            error_log("Erreur prédparation: " . $this->conn->error);
            return false;
        }
        
        $email = $data['email'] ?? null;
        $prix = floatval($data['prix']);
        
        if (!$stmt->bind_param("sssdss",
            $data['nom_client'],
            $email,
            $data['type_abonnement'],
            $prix,
            $data['date_debut'],
            $data['date_fin']
        )) {
            error_log("Erreur bind_param: " . $stmt->error);
            return false;
        }
        
        if (!$stmt->execute()) {
            error_log("Erreur exécution: " . $stmt->error);
            return false;
        }
        
        return $stmt->insert_id ?? true;
    }

    /**
     * Mettre à jour un abonnement
     */
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

    /**
     * Supprimer un abonnement
     */
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM abonnements WHERE id = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Récupérer les abonnements par email client
     */
    public function getByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM abonnements WHERE email = ? ORDER BY date_debut DESC");
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    /**
     * Compter le nombre d'abonnements (tous)
     */
    public function count() {
        $result = $this->conn->query("SELECT COUNT(*) as total FROM abonnements");
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total'] ?? 0;
        }
        return 0;
    }

    /**
     * Compter les abonnements actifs
     */
    public function countActive() {
        $result = $this->conn->query("SELECT COUNT(*) as total FROM abonnements WHERE statut = 'actif'");
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total'] ?? 0;
        }
        return 0;
    }

    /**
     * Récupérer le total des revenus
     */
    public function getTotalRevenue() {
        $result = $this->conn->query("SELECT SUM(prix) as revenue FROM abonnements WHERE statut = 'actif'");
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['revenue'] ?? 0;
        }
        return 0;
    }

    /**
     * Créer les tables si elles n'existent pas
     */
    private function createTables() {
        try {
            // 1. Créer la table si elle n'existe pas
            $createTableSQL = "
                CREATE TABLE IF NOT EXISTS abonnements (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nom_client VARCHAR(100) NOT NULL,
                    email VARCHAR(100),
                    type_abonnement ENUM('mensuel', 'annuel', 'ponctuel') NOT NULL DEFAULT 'mensuel',
                    prix DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                    date_debut DATE NOT NULL,
                    date_fin DATE NOT NULL,
                    statut ENUM('actif', 'suspendu', 'expiré', 'annulé') DEFAULT 'actif',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_email (email),
                    INDEX idx_statut (statut)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ";
            
            if (!$this->conn->query($createTableSQL)) {
                error_log("[TECHCARE] Erreur création table abonnements: " . $this->conn->error);
                return;
            }
            
            error_log("[TECHCARE] Table abonnements créée/vérifiée");
            
            // 2. Vérifier et ajouter la colonne email si elle n'existe pas
            $checkEmail = $this->conn->query("SHOW COLUMNS FROM abonnements LIKE 'email'");
            if (!$checkEmail || $checkEmail->num_rows === 0) {
                if (!$this->conn->query("ALTER TABLE abonnements ADD COLUMN email VARCHAR(100) AFTER nom_client")) {
                    error_log("[TECHCARE] Erreur ajout colonne email: " . $this->conn->error);
                } else {
                    error_log("[TECHCARE] Colonne email ajoutée");
                }
            } else {
                error_log("[TECHCARE] Colonne email existe déjà");
            }
            
            // 3. Vérifier et ajouter les indexes
            $checkIndexEmail = $this->conn->query("SHOW INDEX FROM abonnements WHERE Column_name = 'email' AND Seq_in_index = 1");
            if (!$checkIndexEmail || $checkIndexEmail->num_rows === 0) {
                $result = @$this->conn->query("ALTER TABLE abonnements ADD INDEX idx_email (email)");
                if (!$result && strpos($this->conn->error, 'Duplicate') === false) {
                    error_log("[TECHCARE] Erreur ajout index email: " . $this->conn->error);
                } else if ($result) {
                    error_log("[TECHCARE] Index email ajouté");
                }
            }
            
            // 4. Vérifier et ajouter les données de test si la table est vide
            $countResult = $this->conn->query("SELECT COUNT(*) as total FROM abonnements");
            if ($countResult) {
                $row = $countResult->fetch_assoc();
                if ($row['total'] == 0) {
                    $today = date('Y-m-d');
                    $nextMonth = date('Y-m-d', strtotime($today . ' +1 month'));
                    $nextYear = date('Y-m-d', strtotime($today . ' +1 year'));
                    
                    $insertSQL = "
                        INSERT INTO abonnements (nom_client, email, type_abonnement, prix, date_debut, date_fin, statut) VALUES
                        ('Ahmed Ben Ali', 'ahmed@techcare.tn', 'mensuel', 29.90, '$today', '$nextMonth', 'actif'),
                        ('Sara Mansouri', 'sara@techcare.tn', 'annuel', 299.00, '$today', '$nextYear', 'actif'),
                        ('Karim Jbeli', 'karim@techcare.tn', 'ponctuel', 49.90, '$today', '$nextMonth', 'expiré')
                    ";
                    if (!$this->conn->query($insertSQL)) {
                        error_log("[TECHCARE] Erreur insertion données test: " . $this->conn->error);
                    } else {
                        error_log("[TECHCARE] Données de test insérées");
                    }
                } else {
                    error_log("[TECHCARE] Table a déjà " . $row['total'] . " enregistrements");
                }
            }
        } catch (Exception $e) {
            error_log("[TECHCARE] Exception lors de createTables: " . $e->getMessage());
        }
    }
}
?>