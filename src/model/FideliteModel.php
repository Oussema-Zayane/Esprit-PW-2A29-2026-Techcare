<?php
require_once __DIR__ . '/../../config/database.php';

class FideliteModel {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getConnection();
    }
    
    // =====================================================
    // CRUD de base
    // =====================================================
    
    public function create($titre, $description, $total, $dateGain, $userId = null) {
        // Si userId n'est pas fourni, utiliser l'utilisateur connecté
        if ($userId === null) {
            $userId = $_SESSION['user_id'] ?? 1;
        }
        
        $sql = "INSERT INTO fidelite (titre, description, total, dateGain, user_id) 
                VALUES (:titre, :description, :total, :dateGain, :user_id)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'titre' => $titre,
            'description' => $description,
            'total' => $total,
            'dateGain' => $dateGain,
            'user_id' => $userId
        ]);
    }
    
    public function getAll() {
        $sql = "SELECT * FROM fidelite ORDER BY dateGain DESC";
        return $this->pdo->query($sql)->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM fidelite WHERE idPoints = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function update($id, $titre, $description, $total, $dateGain) {
        $sql = "UPDATE fidelite SET 
                    titre = :titre, 
                    description = :description, 
                    total = :total, 
                    dateGain = :dateGain 
                WHERE idPoints = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'titre' => $titre,
            'description' => $description,
            'total' => $total,
            'dateGain' => $dateGain,
            'id' => $id
        ]);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM fidelite WHERE idPoints = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    // =====================================================
    // MÉTHODES AVEC JOINTURE (WORKSHOP)
    // =====================================================
    
    // Récupérer toutes les fidélités avec les infos utilisateur (JOINTURE)
    public function getFidelitesWithUser() {
        $sql = "SELECT f.*, u.id as user_id, u.nom as user_nom, u.email as user_email, u.nbPoints as user_points
                FROM fidelite f
                LEFT JOIN utilisateur u ON f.user_id = u.id
                ORDER BY f.dateGain DESC";
        return $this->pdo->query($sql)->fetchAll();
    }
    
    // Récupérer les fidélités d'un utilisateur spécifique (JOINTURE avec filtre)
    public function getFidelitesByUser($userId) {
        $sql = "SELECT f.*, u.nom as user_nom, u.email as user_email
                FROM fidelite f
                INNER JOIN utilisateur u ON f.user_id = u.id
                WHERE f.user_id = :user_id
                ORDER BY f.dateGain DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }
    
    // Récupérer les statistiques par utilisateur (JOINTURE avec agrégation)
    public function getStatsByUser() {
        $sql = "SELECT u.id, u.nom, u.email, 
                       COUNT(f.idPoints) as total_fidelites,
                       SUM(f.total) as total_points_offerts,
                       MAX(f.dateGain) as derniere_date
                FROM utilisateur u
                LEFT JOIN fidelite f ON u.id = f.user_id
                GROUP BY u.id
                ORDER BY total_points_offerts DESC";
        return $this->pdo->query($sql)->fetchAll();
    }
    
    // =====================================================
    // MÉTHODES POUR LES POINTS UTILISATEUR
    // =====================================================
    
    public function getUserPoints($userId) {
        $sql = "SELECT nbPoints FROM utilisateur WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
        $result = $stmt->fetch();
        return $result ? $result['nbPoints'] : 0;
    }
    
    public function addUserPoints($userId, $points) {
        $sql = "UPDATE utilisateur SET nbPoints = nbPoints + :points WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['points' => $points, 'id' => $userId]);
    }
}
?>