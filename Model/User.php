<?php
require_once __DIR__ . "/../config.php";

class User {

    public static function findByEmail($email) {
        $conn = Config::getConnexion();
        $sql = "SELECT * FROM personne WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function register($nom, $prenom, $email, $telephone, $password, $role) {
        $conn = Config::getConnexion();
        $sql = "INSERT INTO personne (nom, prenom, email, telephone, password, role) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$nom, $prenom, $email, $telephone, $password, $role]);
    }

    public static function getAllUsers() {
        $conn = Config::getConnexion();
        $sql = "SELECT * FROM personne ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Debug: Afficher le nombre d'utilisateurs trouvés
        error_log("Nombre d'utilisateurs trouvés: " . count($result));
        
        return $result;
    }

    public static function deleteUser($id) {
        $conn = Config::getConnexion();
        // Supprimer la restriction "AND role != 'admin'" pour permettre la suppression de n'importe quel utilisateur
        $sql = "DELETE FROM personne WHERE id = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    public static function updateUser($id, $nom, $prenom, $email, $telephone, $role) {
        $conn = Config::getConnexion();
        $sql = "UPDATE personne SET nom = ?, prenom = ?, email = ?, telephone = ?, role = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        // Debug: Afficher la requête SQL
        error_log("SQL Update: $sql, ID: $id");
        
        return $stmt->execute([$nom, $prenom, $email, $telephone, $role, $id]);
    }
}
?>