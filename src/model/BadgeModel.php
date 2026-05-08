<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/api_keys.php';

class BadgeModel {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getConnection();
    }
    
    public function getAll() {
        $sql = "SELECT * FROM badge ORDER BY seuilPoints ASC";
        return $this->pdo->query($sql)->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM badge WHERE badge_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function create($nom, $description, $icon, $seuilPoints, $image_ia = null, $est_limite = false, $date_debut = null, $date_fin = null) {
        $sql = "INSERT INTO badge (nom, description, icon, seuilPoints, image_ia, est_limite, date_debut, date_fin) 
                VALUES (:nom, :description, :icon, :seuilPoints, :image_ia, :est_limite, :date_debut, :date_fin)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'nom' => $nom,
            'description' => $description,
            'icon' => $icon,
            'seuilPoints' => $seuilPoints,
            'image_ia' => $image_ia,
            'est_limite' => $est_limite ? 1 : 0,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin
        ]);
    }
    
    public function update($id, $nom, $description, $icon, $seuilPoints, $image_ia = null, $est_limite = false, $date_debut = null, $date_fin = null) {
        if ($image_ia) {
            $sql = "UPDATE badge SET nom = :nom, description = :description, 
                    icon = :icon, seuilPoints = :seuilPoints, image_ia = :image_ia,
                    est_limite = :est_limite, date_debut = :date_debut, date_fin = :date_fin
                    WHERE badge_id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'nom' => $nom,
                'description' => $description,
                'icon' => $icon,
                'seuilPoints' => $seuilPoints,
                'image_ia' => $image_ia,
                'est_limite' => $est_limite ? 1 : 0,
                'date_debut' => $date_debut,
                'date_fin' => $date_fin,
                'id' => $id
            ]);
        } else {
            $sql = "UPDATE badge SET nom = :nom, description = :description, 
                    icon = :icon, seuilPoints = :seuilPoints,
                    est_limite = :est_limite, date_debut = :date_debut, date_fin = :date_fin
                    WHERE badge_id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'nom' => $nom,
                'description' => $description,
                'icon' => $icon,
                'seuilPoints' => $seuilPoints,
                'est_limite' => $est_limite ? 1 : 0,
                'date_debut' => $date_debut,
                'date_fin' => $date_fin,
                'id' => $id
            ]);
        }
    }
    
    public function delete($id) {
        $sql = "DELETE FROM badge WHERE badge_id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    public function generateImageWithAI($description, $badgeName) {
        $prompt = "eco friendly game badge " . $badgeName . " " . $description . " green gold medal vector icon";
        $imageUrl = "https://image.pollinations.ai/prompt/" . urlencode($prompt) . "?width=256&height=256";
        
        $uploadDir = __DIR__ . '/../../assets/images/badges/';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $imageUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $imageData = curl_exec($ch);
        curl_close($ch);
        
        if ($imageData) {
            $safeName = preg_replace('/[^a-zA-Z0-9]/', '_', $badgeName);
            $filename = 'badge_' . $safeName . '_' . uniqid() . '.png';
            $filepath = $uploadDir . $filename;
            file_put_contents($filepath, $imageData);
            return 'assets/images/badges/' . $filename;
        }
        return null;
    }
    
    public function getFidelitesWithBadges() {
        $sql = "SELECT f.*, u.nom as user_nom, u.email as user_email,
                       GROUP_CONCAT(DISTINCT b.nom SEPARATOR ', ') as badges,
                       COUNT(DISTINCT b.badge_id) as nb_badges
                FROM fidelite f
                LEFT JOIN utilisateur u ON f.user_id = u.id
                LEFT JOIN user_badges ub ON u.id = ub.user_id
                LEFT JOIN badge b ON ub.badge_id = b.badge_id
                GROUP BY f.idPoints
                ORDER BY f.dateGain DESC";
        return $this->pdo->query($sql)->fetchAll();
    }
}
?>