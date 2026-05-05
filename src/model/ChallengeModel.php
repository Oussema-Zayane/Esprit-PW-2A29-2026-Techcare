<?php
require_once __DIR__ . '/../../config/database.php';

class ChallengeModel {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getConnection();
    }
    
    public function getDailyChallenge() {
        $day = date('N'); // 1 = lundi, 7 = dimanche
        $challenges = [
            1 => ['titre' => 'Lundi Écolo', 'description' => 'Réparer un appareil', 'points' => 75],
            2 => ['titre' => 'Mardi Vert', 'description' => 'Recycler un produit', 'points' => 100],
            3 => ['titre' => 'Mercredi Partage', 'description' => 'Parrainer un ami', 'points' => 200],
            4 => ['titre' => 'Jeudi Diagnostic', 'description' => 'Faire un diagnostic', 'points' => 60],
            5 => ['titre' => 'Vendredi Avis', 'description' => 'Laisser un avis', 'points' => 50],
            6 => ['titre' => 'Samedi Éco', 'description' => 'Achat éco-responsable', 'points' => 150],
            7 => ['titre' => 'Dimanche Bonus', 'description' => 'Défi surprise', 'points' => 80]
        ];
        return $challenges[$day] ?? $challenges[1];
    }
    
    public function hasCompletedChallenge($userId) {
        $sql = "SELECT * FROM point_history WHERE user_id = :id AND DATE(created_at) = CURDATE() AND reason LIKE 'Défi%'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch() !== false;
    }
}
?>