<?php
require_once __DIR__ . '/../../config/database.php';

class FideliteModel {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getConnection();
    }
    
    // =====================================================
    // CRUD pour l'entité Fidelite
    // =====================================================
    
    // CREATE : Ajouter un enregistrement de fidélité
    public function create($titre, $description, $total, $dateGain) {
        $sql = "INSERT INTO fidelite (titre, description, total, dateGain) 
                VALUES (:titre, :description, :total, :dateGain)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'titre' => $titre,
            'description' => $description,
            'total' => $total,
            'dateGain' => $dateGain
        ]);
    }
    
    // READ : Récupérer tous les enregistrements
    public function getAll() {
        $sql = "SELECT * FROM fidelite ORDER BY dateGain DESC";
        return $this->pdo->query($sql)->fetchAll();
    }
    
    // READ : Récupérer un enregistrement par ID
    public function getById($id) {
        $sql = "SELECT * FROM fidelite WHERE idPoints = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    // UPDATE : Modifier un enregistrement
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
    
    // DELETE : Supprimer un enregistrement
    public function delete($id) {
        $sql = "DELETE FROM fidelite WHERE idPoints = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    // =====================================================
    // Méthodes pour les points utilisateur
    // =====================================================
    
    // Récupérer les points d'un utilisateur
    public function getUserPoints($userId) {
        $sql = "SELECT nbPoints FROM utilisateur WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);
        $result = $stmt->fetch();
        return $result ? $result['nbPoints'] : 0;
    }
    
    // Ajouter des points à un utilisateur
    public function addUserPoints($userId, $points) {
        $sql = "UPDATE utilisateur SET nbPoints = nbPoints + :points WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'points' => $points,
            'id' => $userId
        ]);
    }
    
    // Retirer des points à un utilisateur
    public function removeUserPoints($userId, $points) {
        $sql = "UPDATE utilisateur SET nbPoints = nbPoints - :points WHERE id = :id AND nbPoints >= :points";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'points' => $points,
            'id' => $userId
        ]);
    }
    
    // =====================================================
    // Méthodes pour le FrontOffice
    // =====================================================
    
    // Afficher tous les enregistrements de fidélité (alias de getAll)
    public function afficherFidelite() {
        return $this->getAll();
    }
    
    // Consulter le total des points d'un utilisateur (alias de getUserPoints)
    public function consulterTotal($userId) {
        return $this->getUserPoints($userId);
    }
    
    // Ajouter des points (alias)
    public function ajouterPoints($userId, $points) {
        return $this->addUserPoints($userId, $points);
    }
}
?>