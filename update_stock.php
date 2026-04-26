<?php
$dbFile = 'techcare.db';
if (!file_exists($dbFile)) {
    echo 'Base de données non trouvée';
    exit(1);
}
try {
    $pdo = new PDO('sqlite:' . $dbFile);
    $stmt = $pdo->prepare('UPDATE produit SET stock = 10 WHERE stock <= 0');
    $stmt->execute();
    $count = $stmt->rowCount();
    echo "Produits mis à jour: " . $count;
} catch (Exception $e) {
    echo 'Erreur: ' . $e->getMessage();
}
?>
