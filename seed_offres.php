<?php
require_once 'config/database.php';
$conn = Database::getConnection();

$sql = "INSERT IGNORE INTO offre_type_abonnement (id_offre, id_type) VALUES 
    (1, 1), (2, 1), (7, 1),
    (8, 2), (9, 2), (10, 2), (6, 2),
    (2, 3), (5, 3)
";

if($conn->query($sql)) {
    echo "Succès ! Les offres ont été liées aux types d'abonnement.";
} else {
    echo "Erreur : " . $conn->error;
}
?>
