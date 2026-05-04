<?php
require_once "config.php";
$conn = Config::getConnexion();
$stmt = $conn->query("DESCRIBE personne");
print_r($stmt->fetchAll());
?>
