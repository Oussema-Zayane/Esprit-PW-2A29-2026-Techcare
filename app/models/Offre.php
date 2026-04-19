<?php
require_once __DIR__ . '/../../config/database.php';

class Offre {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    /**
     * Récupérer TOUTES les offres avec le nom de l'abonnement associé
     */
    public function getAll() {
        $sql = "SELECT o.*, a.nom_client, a.type_abonnement 
                FROM offre o
                LEFT JOIN abonnements a ON o.id_abonnement = a.id
                ORDER BY o.id DESC";
        $result = $this->conn->query($sql);

        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    /**
     * Récupérer les offres par abonnement
     */
    public function getByAbonnementId($abonnement_id) {
        $stmt = $this->conn->prepare("SELECT * FROM offre WHERE id_abonnement = ?");
        $stmt->bind_param("i", $abonnement_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Récupérer une offre par son ID
     */
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM offre WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Créer une nouvelle offre
     */
    public function create($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO offre (description, id_abonnement)
            VALUES (?, ?)
        ");
        $stmt->bind_param("si",
            $data['description'],
            $data['id_abonnement']
        );
        return $stmt->execute() ? $this->conn->insert_id : false;
    }

    /**
     * Modifier une offre
     */
    public function update($id, $data) {
        $stmt = $this->conn->prepare("
            UPDATE offre 
            SET description = ?, id_abonnement = ?
            WHERE id = ?
        ");
        $stmt->bind_param("sii",
            $data['description'],
            $data['id_abonnement'],
            $id
        );
        return $stmt->execute();
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
     * Récupérer tous les abonnements pour le select
     */
    public function getAbonnementsList() {
        $sql = "SELECT id, nom_client, type_abonnement FROM abonnements ORDER BY id DESC";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>