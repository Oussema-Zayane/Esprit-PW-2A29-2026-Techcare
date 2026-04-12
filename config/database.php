<?php
class Database {
    private static $conn = null;
    private static $host = 'localhost';
    private static $user = 'root';
    private static $pass = '';
    private static $db = 'techcare';
    private static $charset = 'utf8mb4';

    public static function getConnection() {
        if (self::$conn === null) {
            // Créer la base de données si elle n'existe pas
            self::ensureDatabaseExists();
            
            // Connexion à la base de données
            self::$conn = new mysqli(self::$host, self::$user, self::$pass, self::$db);
            
            if (self::$conn->connect_error) {
                error_log("Erreur de connexion DB TechCare: " . self::$conn->connect_error);
                die("Erreur de connexion à la base de données. Veuillez vérifier la configuration.");
            }
            
            // Définir le charset
            if (!self::$conn->set_charset(self::$charset)) {
                error_log("Erreur charset TechCare: " . self::$conn->error);
            }
        }
        return self::$conn;
    }

    private static function ensureDatabaseExists() {
        // Connexion sans base de données pour créer la DB si elle n'existe pas
        $connNoDB = new mysqli(self::$host, self::$user, self::$pass);
        
        if ($connNoDB->connect_error) {
            error_log("Erreur première connexion: " . $connNoDB->connect_error);
            return;
        }
        
        // Créer la base de données
        $sql = "CREATE DATABASE IF NOT EXISTS " . self::$db . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if (!$connNoDB->query($sql)) {
            error_log("Erreur création DB: " . $connNoDB->error);
        }
        
        $connNoDB->close();
    }

    public static function closeConnection() {
        if (self::$conn !== null) {
            self::$conn->close();
            self::$conn = null;
        }
    }

    public static function isConnected() {
        return self::$conn !== null && !self::$conn->connect_error;
    }
}
?>