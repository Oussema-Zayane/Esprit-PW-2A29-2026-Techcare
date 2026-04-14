<?php
require_once __DIR__ . '/../../config/database.php';

class UserModel {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getConnection();
    }
    
    public function getAllUsers() {
        $sql = "SELECT user_id, username, points, level, total_actions FROM user_points ORDER BY points DESC";
        return $this->pdo->query($sql)->fetchAll();
    }
    
    public function getUserById($id) {
        $sql = "SELECT user_id, username, points, level, total_actions FROM user_points WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function createUser($username, $points = 0) {
        $level = floor($points / 1000) + 1;
        $sql = "INSERT INTO user_points (username, points, level, total_actions) VALUES (:username, :points, :level, 0)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['username' => $username, 'points' => $points, 'level' => $level]);
    }
    
    public function updateUser($id, $username, $points) {
        $level = floor($points / 1000) + 1;
        $sql = "UPDATE user_points SET username = :username, points = :points, level = :level WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['username' => $username, 'points' => $points, 'level' => $level, 'id' => $id]);
    }
    
    public function deleteUser($id) {
        $sql = "DELETE FROM user_badges WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $sql = "DELETE FROM point_history WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $sql = "DELETE FROM user_rewards WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $sql = "DELETE FROM user_points WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    public function getTotalUsers() {
        $sql = "SELECT COUNT(*) as total FROM user_points";
        return $this->pdo->query($sql)->fetch()['total'];
    }
    
    public function getTotalPoints() {
        $sql = "SELECT SUM(points) as total FROM user_points";
        return $this->pdo->query($sql)->fetch()['total'] ?? 0;
    }
    
    public function getTotalBadgesUnlocked() {
        $sql = "SELECT COUNT(*) as total FROM user_badges";
        return $this->pdo->query($sql)->fetch()['total'] ?? 0;
    }
    
    public function getTotalRewardsExchanged() {
        $sql = "SELECT COUNT(*) as total FROM user_rewards";
        return $this->pdo->query($sql)->fetch()['total'] ?? 0;
    }
}
?>