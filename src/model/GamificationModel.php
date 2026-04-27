<?php
require_once __DIR__ . '/../../config/database.php';

class GamificationModel {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getConnection();
    }
    
    // =====================================================
    // UTILISATEUR
    // =====================================================
    
    // Récupérer un utilisateur par son ID
    public function getUser($userId = 1) {
        $sql = "SELECT id, nom as username, nbPoints as points, 
                       FLOOR(nbPoints / 1000) + 1 as level, 
                       0 as total_actions 
                FROM utilisateur WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
        $result = $stmt->fetch();
        
        if (!$result) {
            return [
                'user_id' => $userId,
                'username' => 'Utilisateur',
                'points' => 0,
                'level' => 1,
                'total_actions' => 0
            ];
        }
        
        return [
            'user_id' => $result['id'],
            'username' => $result['username'],
            'points' => $result['points'],
            'level' => $result['level'],
            'total_actions' => $result['total_actions']
        ];
    }
    
    // Récupérer tous les utilisateurs pour le classement
    public function getAllUsers() {
        $sql = "SELECT id, nom as username, nbPoints as points, 
                       FLOOR(nbPoints / 1000) + 1 as level 
                FROM utilisateur ORDER BY nbPoints DESC";
        return $this->pdo->query($sql)->fetchAll();
    }
    
    // =====================================================
    // POINTS
    // =====================================================
    
    // Ajouter des points à un utilisateur
    public function addPoints($userId, $points, $reason) {
        // Mettre à jour les points
        $sql = "UPDATE utilisateur SET nbPoints = nbPoints + :points WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['points' => $points, 'id' => $userId]);
        
        // Ajouter à l'historique (table point_history)
        $sql = "INSERT INTO point_history (user_id, points_change, reason, created_at) 
                VALUES (:id, :points, :reason, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId, 'points' => $points, 'reason' => $reason]);
        
        // Vérifier les badges
        $this->checkAndAwardBadges($userId);
        
        return true;
    }
    
    // Récupérer l'historique des points
    public function getHistory($userId, $limit = 10) {
        $sql = "SELECT history_id, points_change, reason, created_at 
                FROM point_history WHERE user_id = :id 
                ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('id', $userId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Supprimer une entrée de l'historique
    public function deleteHistoryItem($historyId, $userId) {
        $sql = "DELETE FROM point_history WHERE history_id = :id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $historyId, 'user_id' => $userId]);
    }
    
    // =====================================================
    // BADGES
    // =====================================================
    
    // Récupérer tous les badges
    public function getAllBadges() {
        $sql = "SELECT badge_id, nom as name, seuilPoints as points_required, 
                       '🏅' as icon, 'Badge éco-responsable' as description 
                FROM badge ORDER BY seuilPoints ASC";
        return $this->pdo->query($sql)->fetchAll();
    }
    
    // Récupérer les badges d'un utilisateur
    public function getUserBadges($userId) {
        // Pour l'instant, retourner des badges simulés
        // À remplacer par une vraie table user_badges si elle existe
        return [
            ['badge_id' => 1],
            ['badge_id' => 2],
            ['badge_id' => 4]
        ];
    }
    
    // Vérifier et débloquer les badges
    private function checkAndAwardBadges($userId) {
        $sql = "SELECT nbPoints FROM utilisateur WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
        $points = $stmt->fetch()['nbPoints'] ?? 0;
        
        $sql = "SELECT badge_id, seuilPoints FROM badge ORDER BY seuilPoints ASC";
        $badges = $this->pdo->query($sql)->fetchAll();
        
        foreach ($badges as $badge) {
            if ($points >= $badge['seuilPoints']) {
                // Vérifier si le badge est déjà débloqué
                $sql = "SELECT * FROM user_badges WHERE user_id = :user_id AND badge_id = :badge_id";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['user_id' => $userId, 'badge_id' => $badge['badge_id']]);
                
                if (!$stmt->fetch()) {
                    $sql = "INSERT INTO user_badges (user_id, badge_id, earned_at) 
                            VALUES (:user_id, :badge_id, NOW())";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute(['user_id' => $userId, 'badge_id' => $badge['badge_id']]);
                }
            }
        }
    }
    
    // =====================================================
    // RÉCOMPENSES
    // =====================================================
    
    // Récupérer toutes les récompenses
    public function getRewards() {
        // Récompenses par défaut
        return [
            ['reward_id' => 1, 'name' => 'Réduction 10%', 'description' => '10% sur réparation', 'points_cost' => 500, 'stock' => 100],
            ['reward_id' => 2, 'name' => 'Support Prioritaire', 'description' => 'Accès prioritaire 1 mois', 'points_cost' => 1000, 'stock' => 50],
            ['reward_id' => 3, 'name' => 'Goodies TechCare', 'description' => 'Kit exclusif', 'points_cost' => 2000, 'stock' => 30],
            ['reward_id' => 4, 'name' => 'Formation Gratuite', 'description' => 'Formation en ligne', 'points_cost' => 3000, 'stock' => 20],
            ['reward_id' => 5, 'name' => 'Réparation Gratuite', 'description' => 'Une réparation offerte', 'points_cost' => 5000, 'stock' => 10]
        ];
    }
    
    // Récupérer les récompenses échangées par un utilisateur
    public function getUserRewards($userId) {
        // Pour l'instant, retourner un tableau vide
        return [];
    }
    
    // Échanger une récompense
    public function redeemReward($userId, $rewardId) {
        $rewards = $this->getRewards();
        $reward = null;
        
        foreach ($rewards as $r) {
            if ($r['reward_id'] == $rewardId) {
                $reward = $r;
                break;
            }
        }
        
        if (!$reward) return false;
        
        $sql = "SELECT nbPoints FROM utilisateur WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
        $userPoints = $stmt->fetch()['nbPoints'] ?? 0;
        
        if ($userPoints < $reward['points_cost']) return false;
        
        // Déduire les points
        $sql = "UPDATE utilisateur SET nbPoints = nbPoints - :points WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['points' => $reward['points_cost'], 'id' => $userId]);
        
        // Ajouter à l'historique
        $sql = "INSERT INTO point_history (user_id, points_change, reason, created_at) 
                VALUES (:id, :points, :reason, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $userId,
            'points' => -$reward['points_cost'],
            'reason' => 'Échange : ' . $reward['name']
        ]);
        
        return true;
    }
}
?>