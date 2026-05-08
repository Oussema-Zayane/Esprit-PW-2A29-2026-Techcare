<?php
require_once __DIR__ . '/../../config/database.php';

class GamificationModel {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getConnection();
    }
    
    // =====================================================
    // CALENDRIER DE CONNEXION
    // =====================================================
    
    public function enregistrerConnexion($userId) {
        $today = date('Y-m-d');
        
        // Vérifier si déjà enregistré aujourd'hui
        $sql = "SELECT * FROM connexion_log WHERE user_id = :user_id AND date_connexion = :today";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId, 'today' => $today]);
        
        if ($stmt->fetch()) {
            return false;
        }
        
        // Calculer le streak (connexions consécutives)
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $sql = "SELECT streak FROM connexion_log 
                WHERE user_id = :user_id AND date_connexion = :yesterday 
                ORDER BY date_connexion DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId, 'yesterday' => $yesterday]);
        $lastStreak = $stmt->fetch();
        
        $streak = $lastStreak ? $lastStreak['streak'] + 1 : 1;
        
        // Bonus selon le streak
        $bonusPoints = $this->calculerBonusStreak($streak);
        
        // Enregistrer la connexion
        $sql = "INSERT INTO connexion_log (user_id, date_connexion, streak, points_gagnes) 
                VALUES (:user_id, :today, :streak, :bonus)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'today' => $today,
            'streak' => $streak,
            'bonus' => $bonusPoints
        ]);
        
        // Ajouter les points bonus
        if ($bonusPoints > 0) {
            $this->addPoints($userId, $bonusPoints, "Bonus de connexion (Jour $streak)");
        }
        
        return ['streak' => $streak, 'bonus' => $bonusPoints];
    }
    
    private function calculerBonusStreak($streak) {
        if ($streak == 3) return 50;
        if ($streak == 7) return 150;
        if ($streak == 14) return 300;
        if ($streak == 30) return 500;
        if ($streak % 7 == 0 && $streak > 0) return 100;
        return 0;
    }
    
    public function getConnexionStats($userId) {
        $sql = "SELECT streak, points_gagnes, date_connexion 
                FROM connexion_log 
                WHERE user_id = :user_id 
                ORDER BY date_connexion DESC LIMIT 30";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }
    
    public function getCurrentStreak($userId) {
        $sql = "SELECT streak FROM connexion_log 
                WHERE user_id = :user_id 
                ORDER BY date_connexion DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch();
        return $result ? $result['streak'] : 0;
    }
    
    // =====================================================
    // PARRAINAGE
    // =====================================================
    
    public function genererCodeParrainage($userId) {
        $code = strtoupper(substr(md5($userId . time()), 0, 8));
        
        $sql = "INSERT INTO codes_parrainage (user_id, code) 
                VALUES (:user_id, :code) 
                ON DUPLICATE KEY UPDATE code = :code";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId, 'code' => $code]);
        
        return $code;
    }
    
    public function getCodeParrainage($userId) {
        $sql = "SELECT code, nb_utilisations FROM codes_parrainage WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch();
        
        if (!$result) {
            return ['code' => $this->genererCodeParrainage($userId), 'nb_utilisations' => 0];
        }
        return $result;
    }
    
    public function utiliserCodeParrainage($code, $nouveauUserId) {
        // Vérifier si le code existe
        $sql = "SELECT user_id FROM codes_parrainage WHERE code = :code";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['code' => $code]);
        $parrain = $stmt->fetch();
        
        if (!$parrain) {
            return ['success' => false, 'message' => 'Code invalide'];
        }
        
        if ($parrain['user_id'] == $nouveauUserId) {
            return ['success' => false, 'message' => 'Vous ne pouvez pas utiliser votre propre code'];
        }
        
        // Vérifier si déjà utilisé
        $sql = "SELECT * FROM parrainage WHERE filleul_id = :filleul_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['filleul_id' => $nouveauUserId]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Vous avez déjà utilisé un code'];
        }
        
        // Enregistrer le parrainage
        $pointsParrain = 200;
        $pointsFilleul = 100;
        
        $sql = "INSERT INTO parrainage (parrain_id, filleul_id, code_parrainage, points_parrain, points_filleul) 
                VALUES (:parrain_id, :filleul_id, :code, :points_parrain, :points_filleul)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'parrain_id' => $parrain['user_id'],
            'filleul_id' => $nouveauUserId,
            'code' => $code,
            'points_parrain' => $pointsParrain,
            'points_filleul' => $pointsFilleul
        ]);
        
        // Mettre à jour le compteur d'utilisations
        $sql = "UPDATE codes_parrainage SET nb_utilisations = nb_utilisations + 1 WHERE code = :code";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['code' => $code]);
        
        // Ajouter les points
        $this->addPoints($parrain['user_id'], $pointsParrain, "Parrainage de l'utilisateur #$nouveauUserId");
        $this->addPoints($nouveauUserId, $pointsFilleul, "Inscription via code parrainage");
        
        return ['success' => true, 'message' => 'Code valide ! +200 pts pour le parrain, +100 pts pour vous'];
    }
    
    public function getStatsParrainage($userId) {
        $sql = "SELECT COUNT(*) as nb_parrainages, SUM(points_parrain) as total_points 
                FROM parrainage WHERE parrain_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch();
    }
    
    // =====================================================
    // EXPORT PDF (récupération des données)
    // =====================================================
    
    public function getExportData($userId) {
        $user = $this->getUser($userId);
        $history = $this->getHistory($userId, 1000);
        $badges = $this->getUserBadges($userId);
        $rewards = $this->getUserRewards($userId);
        
        return [
            'user' => $user,
            'history' => $history,
            'badges' => $badges,
            'rewards' => $rewards,
            'total_points' => $user['points'],
            'total_actions' => $user['total_actions'],
            'date' => date('d/m/Y H:i:s')
        ];
    }
    
    // =====================================================
    // MÉTHODES EXISTANTES (à conserver)
    // =====================================================
    
    public function getUser($userId = 1) {
        $sql = "SELECT id as user_id, nom as username, nbPoints as points, 
                       FLOOR(nbPoints / 1000) + 1 as level, 
                       0 as total_actions 
                FROM utilisateur WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
        $result = $stmt->fetch();
        
        if (!$result) {
            return ['user_id' => $userId, 'username' => 'Utilisateur', 'points' => 0, 'level' => 1, 'total_actions' => 0];
        }
        return $result;
    }
    
    public function getAllUsers() {
        $sql = "SELECT id as user_id, nom as username, nbPoints as points, 
                       FLOOR(nbPoints / 1000) + 1 as level 
                FROM utilisateur ORDER BY nbPoints DESC";
        return $this->pdo->query($sql)->fetchAll();
    }
    
    public function addPoints($userId, $points, $reason) {
        $sql = "UPDATE utilisateur SET nbPoints = nbPoints + :points WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['points' => $points, 'id' => $userId]);
        
        $sql = "INSERT INTO point_history (user_id, points_change, reason, created_at) 
                VALUES (:id, :points, :reason, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId, 'points' => $points, 'reason' => $reason]);
        
        $this->checkAndAwardBadges($userId);
        return true;
    }
    
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
    
    public function deleteHistoryItem($historyId, $userId) {
        $sql = "DELETE FROM point_history WHERE history_id = :id AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $historyId, 'user_id' => $userId]);
    }
    
    public function getAllBadges() {
        $sql = "SELECT badge_id, nom as name, description, icon, seuilPoints as points_required, image_ia,
                       est_limite, date_debut, date_fin
                FROM badge ORDER BY seuilPoints ASC";
        return $this->pdo->query($sql)->fetchAll();
    }
    
    public function getUserBadges($userId) {
        $sql = "SELECT badge_id FROM user_badges WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
        return $stmt->fetchAll();
    }
    
    private function checkAndAwardBadges($userId) {
        $sql = "SELECT nbPoints FROM utilisateur WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
        $points = $stmt->fetch()['nbPoints'] ?? 0;
        
        $sql = "SELECT badge_id, seuilPoints, est_limite, date_debut, date_fin FROM badge";
        $badges = $this->pdo->query($sql)->fetchAll();
        
        foreach ($badges as $badge) {
            // Vérifier si badge limité (date)
            if ($badge['est_limite']) {
                $today = date('Y-m-d');
                if ($badge['date_debut'] && $badge['date_debut'] > $today) continue;
                if ($badge['date_fin'] && $badge['date_fin'] < $today) continue;
            }
            
            if ($points >= $badge['seuilPoints']) {
                $sql = "INSERT IGNORE INTO user_badges (user_id, badge_id) VALUES (:user_id, :badge_id)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['user_id' => $userId, 'badge_id' => $badge['badge_id']]);
            }
        }
    }
    
    public function getRewards() {
        return [
            ['reward_id' => 1, 'name' => 'Réduction 10%', 'description' => '10% sur réparation', 'points_cost' => 500, 'stock' => 100],
            ['reward_id' => 2, 'name' => 'Support Prioritaire', 'description' => 'Accès prioritaire 1 mois', 'points_cost' => 1000, 'stock' => 50],
            ['reward_id' => 3, 'name' => 'Goodies TechCare', 'description' => 'Kit exclusif', 'points_cost' => 2000, 'stock' => 30],
            ['reward_id' => 4, 'name' => 'Formation Gratuite', 'description' => 'Formation en ligne', 'points_cost' => 3000, 'stock' => 20],
            ['reward_id' => 5, 'name' => 'Réparation Gratuite', 'description' => 'Une réparation offerte', 'points_cost' => 5000, 'stock' => 10]
        ];
    }
    
    public function getUserRewards($userId) {
        return [];
    }
    
    public function redeemReward($userId, $rewardId) {
        $rewards = $this->getRewards();
        $reward = null;
        foreach ($rewards as $r) {
            if ($r['reward_id'] == $rewardId) $reward = $r;
        }
        if (!$reward) return false;
        
        $sql = "SELECT nbPoints FROM utilisateur WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
        $userPoints = $stmt->fetch()['nbPoints'] ?? 0;
        if ($userPoints < $reward['points_cost']) return false;
        
        $sql = "UPDATE utilisateur SET nbPoints = nbPoints - :points WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['points' => $reward['points_cost'], 'id' => $userId]);
        
        $sql = "INSERT INTO point_history (user_id, points_change, reason, created_at) 
                VALUES (:id, :points, :reason, NOW())";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId, 'points' => -$reward['points_cost'], 'reason' => 'Échange : ' . $reward['name']]);
        return true;
    }
}
?>