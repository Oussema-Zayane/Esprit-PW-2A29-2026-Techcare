<?php
require_once 'config/database.php';
$conn = Database::getConnection();
$res = $conn->query("SELECT * FROM offre");
if($res) {
    while($row = $res->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "Error: " . $conn->error;
}
?>
