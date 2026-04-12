<?php
/**
 * Page de diagnostic de la base de données TechCare
 */

require_once __DIR__ . '/config/database.php';

$conn = Database::getConnection();

echo "<!DOCTYPE html>";
echo "<html lang='fr'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>Diagnostic - TechCare</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }";
echo ".container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }";
echo "h1 { color: #333; }";
echo ".status { padding: 10px; margin: 10px 0; border-radius: 4px; }";
echo ".good { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }";
echo ".error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }";
echo ".table { width: 100%; border-collapse: collapse; margin: 20px 0; }";
echo ".table th, .table td { padding: 10px; text-align: left; border: 1px solid #ddd; }";
echo ".table th { background: #f9f9f9; font-weight: bold; }";
echo ".btn { padding: 10px 20px; background: #00A86B; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; }";
echo ".btn:hover { background: #007a4e; }";
echo "</style>";
echo "</head>";
echo "<body>";
echo "<div class='container'>";

echo "<h1>🔍 Diagnostic TechCare</h1>";

// 1. Vérifier la connexion
if ($conn && !$conn->connect_error) {
    echo "<div class='status good'>✅ Connexion à la base de données: OK</div>";
} else {
    echo "<div class='status error'>❌ Erreur de connexion: " . ($conn ? $conn->connect_error : 'Impossible de se connecter') . "</div>";
    exit;
}

// 2. Vérifier la table
$tableCheck = $conn->query("SHOW TABLES LIKE 'abonnements'");
if ($tableCheck && $tableCheck->num_rows > 0) {
    echo "<div class='status good'>✅ Table 'abonnements': EXISTS</div>";
} else {
    echo "<div class='status error'>❌ Table 'abonnements': NOT FOUND</div>";
}

// 3. Vérifier la structure de la table
echo "<h2>Structure de la table</h2>";
$columnsResult = $conn->query("DESCRIBE abonnements");
if ($columnsResult) {
    echo "<table class='table'>";
    echo "<tr><th>Colonne</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th></tr>";
    while ($column = $columnsResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . ($column['Null'] === 'YES' ? 'Oui' : 'Non') . "</td>";
        echo "<td>" . ($column['Key'] ?: '-') . "</td>";
        echo "<td>" . ($column['Default'] ?: '-') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='status error'>❌ Erreur lors de la récupération de la structure: " . $conn->error . "</div>";
}

// 4. Vérifier les données
$countResult = $conn->query("SELECT COUNT(*) as total FROM abonnements");
if ($countResult) {
    $row = $countResult->fetch_assoc();
    echo "<h2>Données</h2>";
    echo "<div class='status good'>✅ Nombres d'abonnements: " . $row['total'] . "</div>";
} else {
    echo "<div class='status error'>❌ Erreur lors du comptage: " . $conn->error . "</div>";
}

// 5. Afficher un exemple de données
echo "<h2>Exemples de données</h2>";
$dataResult = $conn->query("SELECT * FROM abonnements LIMIT 5");
if ($dataResult && $dataResult->num_rows > 0) {
    echo "<table class='table'>";
    echo "<tr><th>ID</th><th>Nom</th><th>Email</th><th>Type</th><th>Prix</th><th>Début</th><th>Fin</th><th>Statut</th></tr>";
    while ($row = $dataResult->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['nom_client']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email'] ?? '-') . "</td>";
        echo "<td>" . $row['type_abonnement'] . "</td>";
        echo "<td>" . number_format($row['prix'], 2) . " DT</td>";
        echo "<td>" . $row['date_debut'] . "</td>";
        echo "<td>" . $row['date_fin'] . "</td>";
        echo "<td>" . $row['statut'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<div class='status error'>❌ Pas de données ou erreur: " . ($dataResult ? "Aucun résultat" : $conn->error) . "</div>";
}

// 6. Actions utiles
echo "<h2>Actions</h2>";
echo "<p>";
echo "<a href='index.php?page=offres' class='btn'>← Retour au site</a> ";
echo "<button class='btn' onclick='location.reload()'>Rafraîchir</button>";
echo "</p>";

echo "</div>";
echo "</body>";
echo "</html>";
?>
