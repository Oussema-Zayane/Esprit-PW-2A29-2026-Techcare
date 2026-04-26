<?php
require_once __DIR__ . '/../../config/database.php';

class Utilisateur {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    /**
     * Authentifier un utilisateur
     */
    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT id, nom, email, role FROM utilisateurs WHERE email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        }
        return false;
    }

    /**
     * Enregistrer un nouvel utilisateur
     */
    public function register($nom, $email, $password) {
        // Vérifier si l'email existe déjà
        $stmt = $this->conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return false; // Email déjà utilisé
        }

        // Insérer le nouvel utilisateur
        $stmt = $this->conn->prepare("INSERT INTO utilisateurs (nom, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->bind_param("sss", $nom, $email, $password);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    /**
     * Récupérer un utilisateur par son ID
     */
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT id, nom, email, role FROM utilisateurs WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Récupérer tous les utilisateurs
     */
    public function getAll() {
        $result = $this->conn->query("SELECT id, nom, email, role, created_at FROM utilisateurs ORDER BY created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}