<?php
/**
 * ====================================
 * CLASSE DATABASE - GESTION CONNEXION
 * ====================================
 * 
 * Cette classe utilise le pattern SINGLETON :
 * - Une seule instancede connexion pour toute l'application
 * - Connexion réutilisée entre les pages
 * - Crée automatiquement la BDD s'elle n'existe pas
 * 
 * CONFIGURATION MYSQL:
 * - Host: localhost (serveur local)
 * - User: root (utilisateur par défaut XAMPP)
 * - Password: vide (pas de mot de passe en dev)
 * - Database: techcare (crée auto si manquante)
 * - Charset: utf8mb4 (support caractères spéciaux)
 */
class Database {
    // Stocke l'unique connexion (null au départ)
    private static $conn = null;
    
    // CREDENTIALS MySQL - À adapter pour la production
    private static $host = 'localhost';
    private static $user = 'root';
    private static $pass = '';
    private static $db = 'techcare';
    private static $charset = 'utf8mb4'; // Support des accents, émojis, etc.

    /**
     * Retourner la connexion MysqliOU la créer si elle n'existe pas
     * (Pattern Singleton)
     */
    public static function getConnection() {
        // Vérifier si connexion déjà établie
        if (self::$conn === null) {
            // ÉTAPE 1: S'assurer que la base de données existe
            // Crée 'techcare' automatiquement si elle manque
            self::ensureDatabaseExists();
            
            // ÉTAPE 2: Établir la connexion MySQL à la BDD
            // mysqli = interface PHP pour MySQL (moderne et sécurisée)
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

    /**
     * Vérifier et créer la base de données si elle n'existe pas
     * Appelée automatiquement au premier getConnection()
     */
    private static function ensureDatabaseExists() {
        // Se connecter sans spécifier de BDD (pour pouvoir en créer une)
        $connNoDB = new mysqli(self::$host, self::$user, self::$pass);
        
        if ($connNoDB->connect_error) {
            // Log l'erreur for débogage (visible dans les error logs du serveur)
            error_log("Erreur première connexion: " . $connNoDB->connect_error);
            return;
        }
        
        // Créer la base de données avec le bon charset
        // IF NOT EXISTS = ne génère pas d'erreur si BDD existe déjà
        $sql = "CREATE DATABASE IF NOT EXISTS " . self::$db . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if (!$connNoDB->query($sql)) {
            error_log("Erreur création DB: " . $connNoDB->error);
        }
        
        // Fermer cette connexion temporaire
        $connNoDB->close();
    }

    public static function closeConnection() {
        if (self::$conn !== null) {
            self::$conn->close();
            self::$conn = null;
        }
    }

    /**
     * Créer les tables manquantes si elles n'existent pas
     */
    public static function ensureTablesExist() {
        $conn = self::getConnection();
        
        // Table sms_logs
        $conn->query("CREATE TABLE IF NOT EXISTS sms_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            abonnement_id INT,
            telephone VARCHAR(20) NOT NULL,
            nom_client VARCHAR(100),
            message TEXT NOT NULL,
            type_sms ENUM('bienvenue', 'rappel_j7', 'rappel_j3', 'manuel') NOT NULL DEFAULT 'manuel',
            statut ENUM('envoye', 'echoue', 'simule') NOT NULL DEFAULT 'simule',
            api_response TEXT,
            message_sid VARCHAR(100),
            date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_abonnement_id (abonnement_id),
            INDEX idx_telephone (telephone),
            INDEX idx_statut (statut),
            INDEX idx_type_sms (type_sms),
            INDEX idx_date_envoi (date_envoi)
        )");
        
        // Ajouter la colonne telephone si elle n'existe pas dans abonnements
        $result = $conn->query("SHOW COLUMNS FROM abonnements LIKE 'telephone'");
        if ($result && $result->num_rows === 0) {
            $conn->query("ALTER TABLE abonnements ADD COLUMN telephone VARCHAR(20) AFTER email");
        }

        // Table offre_type_abonnement (pivot pour la relation plusieurs-à-plusieurs)
        $conn->query("CREATE TABLE IF NOT EXISTS offre_type_abonnement (
            id_offre INT(11) NOT NULL,
            id_type INT(11) NOT NULL,
            PRIMARY KEY (id_offre, id_type),
            FOREIGN KEY (id_offre) REFERENCES offre(id) ON DELETE CASCADE,
            FOREIGN KEY (id_type) REFERENCES type_abonnement(id_type) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    }

    public static function isConnected() {
        return self::$conn !== null && !self::$conn->connect_error;
    }
}
?>