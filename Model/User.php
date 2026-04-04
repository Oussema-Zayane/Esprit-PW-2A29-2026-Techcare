<?php
require_once "../config.php";

class User {

    public static function findByEmail($email) {
        $conn = Config::getConnexion();
        $sql = "SELECT * FROM personne WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public static function register($nom, $prenom, $email, $telephone, $password) {
        $conn = Config::getConnexion();
        $sql = "INSERT INTO personne (nom, prenom, email, telephone, password) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$nom, $prenom, $email, $telephone, $password]);
    }
}
?>