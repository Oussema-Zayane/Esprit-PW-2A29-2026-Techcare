<?php
require_once __DIR__ . '/../../config/database.php';

class UserModel {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getConnection();
    }
    
    public function getAllUsers() {
        $sql = "SELECT id, nom, nbPoints, email FROM utilisateur ORDER BY nbPoints DESC";
        return $this->pdo->query($sql)->fetchAll();
    }
    
    public function createUser($username, $points) {
        $sql = "INSERT INTO utilisateur (nom, email, motDePasse, nbPoints) VALUES (:nom, 'test@test.com', 'password', :points)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['nom' => $username, 'points' => $points]);
    }
    
    public function deleteUser($id) {
        $sql = "DELETE FROM utilisateur WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    public function getTotalUsers() {
        return $this->pdo->query("SELECT COUNT(*) FROM utilisateur")->fetchColumn();
    }
    
    public function getTotalPoints() {
        return $this->pdo->query("SELECT SUM(nbPoints) FROM utilisateur")->fetchColumn() ?: 0;
    }
    
    public function getTotalBadges() {
        return 23;
    }
    
    public function getTotalActions() {
        return 98;
    }
}
?>