<?php
require_once __DIR__ . '/../../config/database.php';

class Offre {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    /**
     * Récupérer TOUTES les offres avec les types d'abonnement associés
     */
    public function getAll() {
        $sql = "SELECT o.id, o.description,
                       GROUP_CONCAT(t.nom_type SEPARATOR ', ') as type_noms,
                       GROUP_CONCAT(t.id_type SEPARATOR ',') as type_ids
                FROM offre o
                LEFT JOIN offre_type_abonnement ota ON o.id = ota.id_offre
                LEFT JOIN type_abonnement t ON ota.id_type = t.id_type
                GROUP BY o.id, o.description
                ORDER BY o.id DESC";
        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $offres = $result->fetch_all(MYSQLI_ASSOC);
            foreach ($offres as &$offre) {
                // Pour compatibilité avec les anciennes vues
                $offre['type_nom'] = $offre['type_noms'] ? $offre['type_noms'] : null;
                $offre['types'] = $offre['type_ids'] ? explode(',', $offre['type_ids']) : [];
            }
            return $offres;
        }
        return [];
    }

    /**
     * Récupérer une offre par son ID
     */
    public function getById($id) {
        $stmt = $this->conn->prepare("
            SELECT o.id, o.description,
                   GROUP_CONCAT(t.nom_type SEPARATOR ', ') as type_noms,
                   GROUP_CONCAT(t.id_type SEPARATOR ',') as type_ids
            FROM offre o
            LEFT JOIN offre_type_abonnement ota ON o.id = ota.id_offre
            LEFT JOIN type_abonnement t ON ota.id_type = t.id_type
            WHERE o.id = ?
            GROUP BY o.id, o.description
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $offre = $result->fetch_assoc();
        
        if ($offre) {
            $offre['types'] = $offre['type_ids'] ? explode(',', $offre['type_ids']) : [];
        }
        
        return $offre;
    }

    /**
     * Créer une nouvelle offre (liée à un ou plusieurs types d'abonnement)
     */
    public function create($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO offre (description)
            VALUES (?)
        ");
        $stmt->bind_param("s", $data['description']);
        
        if ($stmt->execute()) {
            $id_offre = $this->conn->insert_id;
            
            // Lier les types d'abonnement
            if (!empty($data['id_type']) && is_array($data['id_type'])) {
                $this->syncTypes($id_offre, $data['id_type']);
            }
            
            return $id_offre;
        }
        error_log("[TECHCARE] Erreur creation offre: " . $stmt->error);
        return false;
    }

    /**
     * Modifier une offre
     */
    public function update($id, $data) {
        $stmt = $this->conn->prepare("
            UPDATE offre 
            SET description = ?
            WHERE id = ?
        ");
        $stmt->bind_param("si", $data['description'], $id);
        $result = $stmt->execute();
        
        if ($result) {
            // Mettre à jour les liaisons
            $types = !empty($data['id_type']) && is_array($data['id_type']) ? $data['id_type'] : [];
            $this->syncTypes($id, $types);
        }
        
        return $result;
    }
    
    /**
     * Synchroniser les types d'abonnement pour une offre
     */
    private function syncTypes($id_offre, $types) {
        // Supprimer les anciennes liaisons
        $stmtDel = $this->conn->prepare("DELETE FROM offre_type_abonnement WHERE id_offre = ?");
        $stmtDel->bind_param("i", $id_offre);
        $stmtDel->execute();
        
        // Insérer les nouvelles
        if (!empty($types)) {
            $stmtIns = $this->conn->prepare("INSERT INTO offre_type_abonnement (id_offre, id_type) VALUES (?, ?)");
            foreach ($types as $id_type) {
                $id_type = intval($id_type);
                $stmtIns->bind_param("ii", $id_offre, $id_type);
                $stmtIns->execute();
            }
        }
    }

    /**
     * Supprimer une offre
     */
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM offre WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Récupérer tous les types d'abonnement pour le select
     */
    public function getTypesList() {
        $sql = "SELECT id_type, nom_type, prix_base FROM type_abonnement ORDER BY id_type ASC";
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    /**
     * Compter le nombre total d'offres
     */
    public function count() {
        $result = $this->conn->query("SELECT COUNT(*) as total FROM offre");
        if ($result) {
            $row = $result->fetch_assoc();
            return $row['total'] ?? 0;
        }
        return 0;
    }

    /**
     * Récupérer les statistiques des offres
     */
    public function getStats() {
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM offre) as total_offres,
                    (SELECT COUNT(DISTINCT id_type) FROM offre_type_abonnement) as types_concernes,
                    (SELECT COUNT(o.id) FROM offre o LEFT JOIN offre_type_abonnement ota ON o.id = ota.id_offre WHERE ota.id_type IS NULL) as offres_non_liees";
        $result = $this->conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return [
            'total_offres' => 0,
            'types_concernes' => 0,
            'offres_non_liees' => 0
        ];
    }
}
?>