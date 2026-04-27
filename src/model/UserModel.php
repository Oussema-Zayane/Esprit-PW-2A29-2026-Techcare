<?php
require_once __DIR__ . '/../../config/database.php';

class UserModel {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getConnection();
    }
    
    // =====================================================
    // READ - Récupérer les utilisateurs
    // =====================================================
    
    // Récupérer tous les utilisateurs
    public function getAllUsers() {
        $sql = "SELECT id, nom, email, nbPoints FROM utilisateur ORDER BY nom ASC";
        return $this->pdo->query($sql)->fetchAll();
    }
    
    // Récupérer un utilisateur par son ID
    public function getUserById($id) {
        $sql = "SELECT id, nom, email, nbPoints FROM utilisateur WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    // Récupérer un utilisateur par son email (pour connexion)
    public function getUserByEmail($email) {
        $sql = "SELECT id, nom, email, nbPoints FROM utilisateur WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }
    
    // =====================================================
    // CREATE - Ajouter un utilisateur
    // =====================================================
    
    public function createUser($nom, $email, $motDePasse, $nbPoints = 0) {
        $sql = "INSERT INTO utilisateur (nom, email, motDePasse, nbPoints) 
                VALUES (:nom, :email, :motDePasse, :nbPoints)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'nom' => $nom,
            'email' => $email,
            'motDePasse' => password_hash($motDePasse, PASSWORD_DEFAULT),
            'nbPoints' => $nbPoints
        ]);
    }
    
    // =====================================================
    // UPDATE - Modifier un utilisateur
    // =====================================================
    
    public function updateUser($id, $nom, $email, $nbPoints = null) {
        if ($nbPoints !== null) {
            $sql = "UPDATE utilisateur SET nom = :nom, email = :email, nbPoints = :nbPoints WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'nom' => $nom,
                'email' => $email,
                'nbPoints' => $nbPoints,
                'id' => $id
            ]);
        } else {
            $sql = "UPDATE utilisateur SET nom = :nom, email = :email WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'nom' => $nom,
                'email' => $email,
                'id' => $id
            ]);
        }
    }
    
    // Ajouter des points à un utilisateur
    public function addPoints($id, $points) {
        $sql = "UPDATE utilisateur SET nbPoints = nbPoints + :points WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'points' => $points,
            'id' => $id
        ]);
    }
    
    // Retirer des points à un utilisateur
    public function removePoints($id, $points) {
        $sql = "UPDATE utilisateur SET nbPoints = nbPoints - :points WHERE id = :id AND nbPoints >= :points";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'points' => $points,
            'id' => $id
        ]);
    }
    
    // =====================================================
    // DELETE - Supprimer un utilisateur
    // =====================================================
    
    public function deleteUser($id) {
        // Supprimer d'abord les références dans les autres tables
        $this->pdo->prepare("DELETE FROM fidelite WHERE user_id = :id")->execute(['id' => $id]);
        $this->pdo->prepare("DELETE FROM user_badges WHERE user_id = :id")->execute(['id' => $id]);
        $this->pdo->prepare("DELETE FROM point_history WHERE user_id = :id")->execute(['id' => $id]);
        $this->pdo->prepare("DELETE FROM user_rewards WHERE user_id = :id")->execute(['id' => $id]);
        
        // Puis supprimer l'utilisateur
        $sql = "DELETE FROM utilisateur WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    // =====================================================
    // STATISTIQUES
    // =====================================================
    
    public function getTotalUsers() {
        $sql = "SELECT COUNT(*) as total FROM utilisateur";
        $result = $this->pdo->query($sql)->fetch();
        return $result['total'] ?? 0;
    }
    
    public function getTotalPoints() {
        $sql = "SELECT SUM(nbPoints) as total FROM utilisateur";
        $result = $this->pdo->query($sql)->fetch();
        return $result['total'] ?? 0;
    }
    
    public function getTotalBadges() {
        $sql = "SELECT COUNT(*) as total FROM user_badges";
        $result = $this->pdo->query($sql)->fetch();
        return $result['total'] ?? 0;
    }
    
    public function getTotalActions() {
        $sql = "SELECT COUNT(*) as total FROM point_history";
        $result = $this->pdo->query($sql)->fetch();
        return $result['total'] ?? 0;
    }
    
    // =====================================================
    // STATISTIQUES AVEC JOINTURE (pour le dashboard)
    // =====================================================
    
    // Récupérer le classement des utilisateurs
    public function getLeaderboard($limit = 10) {
        $sql = "SELECT id, nom, nbPoints, 
                       FLOOR(nbPoints / 1000) + 1 as level 
                FROM utilisateur 
                ORDER BY nbPoints DESC 
                LIMIT :limit";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    // Récupérer les statistiques par utilisateur (avec jointure)
    public function getUserStatsWithFidelite() {
        $sql = "SELECT u.id, u.nom, u.email, u.nbPoints,
                       COUNT(f.idPoints) as total_fidelites,
                       COALESCE(SUM(f.total), 0) as total_points_offerts,
                       MAX(f.dateGain) as derniere_offre
                FROM utilisateur u
                LEFT JOIN fidelite f ON u.id = f.user_id
                GROUP BY u.id
                ORDER BY u.nbPoints DESC";
        return $this->pdo->query($sql)->fetchAll();
    }
}
?>