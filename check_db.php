<?php
require_once "config.php";
$conn = Config::getConnexion();
$stmt = $conn->query("DESCRIBE personne");
print_r($stmt->fetchAll());
require_once 'config/database.php';
$conn = Database::getConnection();
$res = $conn->query("SELECT * FROM type_abonnement");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
