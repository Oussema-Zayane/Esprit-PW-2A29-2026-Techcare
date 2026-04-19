<?php
require_once __DIR__ . '/../../config/database.php';

class Type {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    /**
     * Récupérer TOUS les types d'abonnement
     */
    public function getAll() {
        $sql = "SELECT * FROM type_abonnement ORDER BY id_type ASC";
        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    /**
     * Récupérer un type par son ID
     */
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

    /**
     * Récupérer un type par son nom
     */
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
     * Récupérer un type par son nom (alias pour getByNom)
     */
    public function getByName($nom_type) {
        return $this->getByNom($nom_type);
    }

    /**
     * Créer un nouveau type
     */
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

    /**
     * Modifier un type
     */
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

    /**
     * Supprimer un type
     */
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
            return false; // Ne pas supprimer si utilisé
        }

        $stmt = $this->conn->prepare("DELETE FROM type_abonnement WHERE id_type = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Vérifier si un nom de type existe déjà
     */
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

    /**
     * Récupérer les statistiques par type
     */
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

    /**
     * Compter le nombre total de types
     */
    public function count() {
        $result = $this->conn->query("SELECT COUNT(*) as total FROM type_abonnement");
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total'] ?? 0;
        }
        return 0;
    }

    /**
     * Récupérer un type formaté pour l'affichage
     */
    public function getFormatted($id) {
        $type = $this->getById($id);
        if (!$type) {
            return null;
        }
        
        return [
            'id' => $type['id_type'],
            'nom' => $type['nom_type'],
            'prix' => number_format($type['prix_base'], 2) . ' DT',
            'duree' => $type['duree_jours'] . ' jours',
            'description' => $type['description'],
            'icone' => $type['icone'] ?? 'tag'
        ];
    }
}
?>