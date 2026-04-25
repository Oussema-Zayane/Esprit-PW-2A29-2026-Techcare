<?php
require_once "c:/xampp/htdocs/Userv4/config.php";
try {
    $conn = Config::getConnexion();
    $conn->exec("ALTER TABLE personne ADD COLUMN google_id VARCHAR(255) UNIQUE NULL");
    echo "Added google_id column.\n";
    $conn->exec("ALTER TABLE personne ADD COLUMN face_descriptor TEXT NULL");
    echo "Added face_descriptor column.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
