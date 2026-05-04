<?php
require_once __DIR__ . "/../config.php";

class User {

    public static function findByEmail($email) {
        $conn = Config::getConnexion();
        $sql = "SELECT *, 
                (lockout_time IS NOT NULL AND lockout_time > NOW()) as is_locked,
                (CASE WHEN lockout_time > NOW() THEN TIMESTAMPDIFF(SECOND, NOW(), lockout_time) ELSE 0 END) as seconds_left 
                FROM personne WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function findByGoogleId($googleId) {
        $conn = Config::getConnexion();
        $sql = "SELECT * FROM personne WHERE google_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$googleId]);
        return $stmt->fetch();
    }

    public static function register($nom, $prenom, $email, $telephone, $password, $role, $googleId = null) {
        $conn = Config::getConnexion();
        $sql = "INSERT INTO personne (nom, prenom, email, telephone, password, role, google_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$nom, $prenom, $email, $telephone, $password, $role, $googleId]);
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
    public static function updateProfile($id, $nom, $prenom, $email, $telephone, $picture = null) {
        $conn = Config::getConnexion();
        if ($picture) {
            $sql = "UPDATE personne SET nom = ?, prenom = ?, email = ?, telephone = ?, profile_picture = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            return $stmt->execute([$nom, $prenom, $email, $telephone, $picture, $id]);
        } else {
            $sql = "UPDATE personne SET nom = ?, prenom = ?, email = ?, telephone = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            return $stmt->execute([$nom, $prenom, $email, $telephone, $id]);
        }
    }

    public static function updateStatus($id, $status) {
        $conn = Config::getConnexion();
        $sql = "UPDATE personne SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    public static function recordFailedAttempt($id, $attempts, $lockoutMinutes) {
        $conn = Config::getConnexion();
        if ($lockoutMinutes > 0) {
            $sql = "UPDATE personne SET failed_attempts = ?, lockout_time = DATE_ADD(NOW(), INTERVAL ? MINUTE) WHERE id = ?";
            $stmt = $conn->prepare($sql);
            return $stmt->execute([$attempts, $lockoutMinutes, $id]);
        } else {
            $sql = "UPDATE personne SET failed_attempts = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            return $stmt->execute([$attempts, $id]);
        }
    }

    public static function resetFailedAttempts($id) {
        $conn = Config::getConnexion();
        $sql = "UPDATE personne SET failed_attempts = 0, lockout_time = NULL WHERE id = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    public static function updateFaceDescriptor($id, $descriptor) {
        $conn = Config::getConnexion();
        $sql = "UPDATE personne SET face_descriptor = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$descriptor, $id]);
    }

    public static function updateVerificationToken($id, $token) {
        $conn = Config::getConnexion();
        $sql = "UPDATE personne SET verification_token = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$token, $id]);
    }

    public static function verifyUser($token) {
        $conn = Config::getConnexion();
        $sql = "UPDATE personne SET is_verified = 1, verification_token = NULL WHERE verification_token = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$token]);
    }

    public static function setResetToken($email, $token, $expires) {
        $conn = Config::getConnexion();
        $sql = "UPDATE personne SET reset_token = ?, reset_token_expires = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$token, $expires, $email]);
    }

    public static function findByResetToken($token) {
        $conn = Config::getConnexion();
        $sql = "SELECT * FROM personne WHERE reset_token = ? AND reset_token_expires > NOW()";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    public static function updatePassword($id, $hashedPassword) {
        $conn = Config::getConnexion();
        $sql = "UPDATE personne SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$hashedPassword, $id]);
    }
}