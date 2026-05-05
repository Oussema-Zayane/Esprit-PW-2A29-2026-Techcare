<?php
require_once "config.php";

try {
    $conn = Config::getConnexion();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h3>Diagnostic de la base de données :</h3>";

    // 1. Vérifier si la table existe
    $tableCheck = $conn->query("SHOW TABLES LIKE 'personne'")->rowCount();
    if ($tableCheck == 0) {
        die("❌ Erreur : La table 'personne' n'existe pas !");
    }
    echo "✅ Table 'personne' trouvée.<br>";

    // 2. Vérifier les colonnes existantes
    $columns = $conn->query("DESCRIBE personne")->fetchAll(PDO::FETCH_COLUMN);
    
    // Ajout de google2fa_secret si manquante
    if (!in_array('google2fa_secret', $columns)) {
        $conn->exec("ALTER TABLE personne ADD google2fa_secret VARCHAR(255) DEFAULT NULL");
        echo "✅ Colonne 'google2fa_secret' ajoutée.<br>";
    } else {
        echo "ℹ️ Colonne 'google2fa_secret' déjà présente.<br>";
    }

    // Ajout de two_factor_enabled si manquante
    if (!in_array('two_factor_enabled', $columns)) {
        $conn->exec("ALTER TABLE personne ADD two_factor_enabled TINYINT(1) DEFAULT 0");
        echo "✅ Colonne 'two_factor_enabled' ajoutée.<br>";
    } else {
        echo "ℹ️ Colonne 'two_factor_enabled' déjà présente.<br>";
    }

    // Ajout de linkedin_id si manquante
    if (!in_array('linkedin_id', $columns)) {
        $conn->exec("ALTER TABLE personne ADD linkedin_id VARCHAR(255) DEFAULT NULL UNIQUE");
        echo "✅ Colonne 'linkedin_id' ajoutée.<br>";
    } else {
        echo "ℹ️ Colonne 'linkedin_id' déjà présente.<br>";
    }

    echo "<br><b>Migration terminée avec succès !</b><br>";
    echo "<a href='View/front/profile.php'>Retour au profil</a>";

} catch (Exception $e) {
    echo "❌ Erreur critique : " . $e->getMessage();
}
?>
