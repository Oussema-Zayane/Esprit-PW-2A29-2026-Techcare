<?php
require_once __DIR__ . '/../../config/database.php';

class GamificationModel {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getConnection();
    }
    
    public function getUser($userId = 1) {
        $sql = "SELECT user_id, username, points, level, total_actions FROM user_points WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch();
    }
    
    public function addPoints($userId, $points, $reason) {
        $sql = "UPDATE user_points SET points = points + :points, total_actions = total_actions + 1 WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['points' => $points, 'id' => $userId]);
        
        $sql = "UPDATE user_points SET level = FLOOR(points / 1000) + 1 WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
        
        $sql = "INSERT INTO point_history (user_id, points_change, reason) VALUES (:id, :points, :reason)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId, 'points' => $points, 'reason' => $reason]);
        
        $this->checkAndAwardBadges($userId);
        return true;
    }
    
    private function checkAndAwardBadges($userId) {
        $sql = "SELECT points FROM user_points WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
        $points = $stmt->fetch()['points'];
        
        $sql = "SELECT badge_id, points_required FROM badges ORDER BY points_required ASC";
        $badges = $this->pdo->query($sql)->fetchAll();
        
        foreach ($badges as $badge) {
            if ($points >= $badge['points_required']) {
                $sql = "INSERT IGNORE INTO user_badges (user_id, badge_id) VALUES (:id, :badge_id)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['id' => $userId, 'badge_id' => $badge['badge_id']]);
            }
        }
    }
    
    public function getHistory($userId, $limit = 10) {
        $sql = "SELECT history_id, points_change, reason, created_at FROM point_history WHERE user_id = :id ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('id', $userId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function deleteHistoryItem($historyId, $userId) {
        $sql = "DELETE FROM point_history WHERE history_id = :id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $historyId, 'user_id' => $userId]);
    }
    
    public function getAllUsers() {
        $sql = "SELECT user_id, username, points, level FROM user_points ORDER BY points DESC";
        return $this->pdo->query($sql)->fetchAll();
    }
    
    public function getAllBadges() {
        $sql = "SELECT * FROM badges ORDER BY points_required ASC";
        return $this->pdo->query($sql)->fetchAll();
    }
    
    public function getUserBadges($userId) {
        $sql = "SELECT badge_id FROM user_badges WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll();
    }
    
    public function getRewards() {
        $sql = "SELECT * FROM rewards WHERE is_active = 1 AND stock > 0 ORDER BY points_cost ASC";
        return $this->pdo->query($sql)->fetchAll();
    }
    
    public function redeemReward($userId, $rewardId) {
        $sql = "SELECT points_cost, stock FROM rewards WHERE reward_id = :id AND is_active = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $rewardId]);
        $reward = $stmt->fetch();
        if (!$reward) return false;
        
        $sql = "SELECT points FROM user_points WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
        $userPoints = $stmt->fetch()['points'];
        if ($userPoints < $reward['points_cost']) return false;
        if ($reward['stock'] <= 0) return false;
        
        $this->pdo->beginTransaction();
        try {
            $sql = "UPDATE user_points SET points = points - :points WHERE user_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['points' => $reward['points_cost'], 'id' => $userId]);
            
            $sql = "UPDATE user_points SET level = FLOOR(points / 1000) + 1 WHERE user_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $userId]);
            
            $sql = "INSERT INTO user_rewards (user_id, reward_id) VALUES (:id, :reward_id)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $userId, 'reward_id' => $rewardId]);
            
            $sql = "UPDATE rewards SET stock = stock - 1 WHERE reward_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $rewardId]);
            
            $sql = "INSERT INTO point_history (user_id, points_change, reason) VALUES (:id, :points, :reason)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $userId, 'points' => -$reward['points_cost'], 'reason' => 'Échange de récompense']);
            
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
    
    public function getUserRewards($userId) {
        $sql = "SELECT r.*, ur.redeemed_at FROM user_rewards ur JOIN rewards r ON ur.reward_id = r.reward_id WHERE ur.user_id = :id ORDER BY ur.redeemed_at DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll();
    }
}
?>