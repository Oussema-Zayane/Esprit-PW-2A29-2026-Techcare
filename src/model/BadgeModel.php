<?php
require_once __DIR__ . '/../../config/database.php';

class BadgeModel {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getConnection();
    }
    
    // CREATE
    public function create($nom, $description, $icon, $seuilPoints) {
        $sql = "INSERT INTO badge (nom, description, icon, seuilPoints) 
                VALUES (:nom, :description, :icon, :seuilPoints)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'nom' => $nom,
            'description' => $description,
            'icon' => $icon,
            'seuilPoints' => $seuilPoints
        ]);
    }
    
    // READ - All
    public function getAll() {
        $sql = "SELECT * FROM badge ORDER BY seuilPoints ASC";
        return $this->pdo->query($sql)->fetchAll();
    }
    
    // READ - By ID
    public function getById($id) {
        $sql = "SELECT * FROM badge WHERE badge_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    // UPDATE
    public function update($id, $nom, $description, $icon, $seuilPoints) {
        $sql = "UPDATE badge SET nom = :nom, description = :description, 
                icon = :icon, seuilPoints = :seuilPoints WHERE badge_id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'nom' => $nom,
            'description' => $description,
            'icon' => $icon,
            'seuilPoints' => $seuilPoints,
            'id' => $id
        ]);
    }
    
    // DELETE
    public function delete($id) {
        // D'abord supprimer les références dans user_badges
        $sql = "DELETE FROM user_badges WHERE badge_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        // Puis supprimer le badge
        $sql = "DELETE FROM badge WHERE badge_id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
?>