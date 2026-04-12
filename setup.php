<?php
/**
 * Script d'installation et de setup de TechCare
 * Accès: http://localhost/web/setup.php
 */

// Pas besoin de session pour le setup
$host = 'localhost';
$db = 'techcare';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// Connexion sans base de données d'abord (pour créer la DB si elle n'existe pas)
$connNoDB = new mysqli($host, $user, $pass);
if ($connNoDB->connect_error) {
    die('Erreur de connexion: ' . $connNoDB->connect_error);
}

$steps = [];

// Étape 1: Créer la base de données
$sql = "CREATE DATABASE IF NOT EXISTS $db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($connNoDB->query($sql)) {
    $steps[] = ['✅ Base de données créée/existe', 'good'];
} else {
    $steps[] = ['❌ Erreur création base: ' . $connNoDB->error, 'error'];
}

$connNoDB->close();

// Étape 2: Connexion à la base de données
$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset($charset);

if ($conn->connect_error) {
    die('Erreur de connexion à la base: ' . $conn->connect_error);
}

$steps[] = ['✅ Connexion à la base de données', 'good'];

// Étape 3: Créer la table si elle n'existe pas
$createTableSQL = "CREATE TABLE IF NOT EXISTS abonnements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_client VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    type_abonnement ENUM('mensuel', 'annuel', 'ponctuel') DEFAULT 'mensuel',
    prix DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    statut ENUM('actif', 'suspendu', 'expiré', 'annulé') DEFAULT 'actif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci";

if ($conn->query($createTableSQL)) {
    $steps[] = ['✅ Table créée/existe', 'good'];
} else {
    $steps[] = ['❌ Erreur création table: ' . $conn->error, 'error'];
}

// Étape 4: Vérifier et ajouter la colonne email si elle manque
$checkEmailColumn = $conn->query("SHOW COLUMNS FROM abonnements LIKE 'email'");
if ($checkEmailColumn && $checkEmailColumn->num_rows === 0) {
    if ($conn->query("ALTER TABLE abonnements ADD COLUMN email VARCHAR(100) AFTER nom_client")) {
        $steps[] = ['✅ Colonne email ajoutée', 'good'];
    } else {
        $steps[] = ['❌ Erreur ajout colonne email: ' . $conn->error, 'error'];
    }
} else {
    $steps[] = ['✅ Colonne email existe', 'good'];
}

// Étape 5: Vérifier et ajouter les indexes si manquants
$checkIndex = $conn->query("SHOW INDEX FROM abonnements WHERE Column_name = 'email'");
if (!$checkIndex || $checkIndex->num_rows === 0) {
    if ($conn->query("ALTER TABLE abonnements ADD INDEX idx_email (email)")) {
        $steps[] = ['✅ Index email ajouté', 'good'];
    } else {
        $steps[] = ['⚠️ Index email peut déjà exister', 'warning'];
    }
} else {
    $steps[] = ['✅ Index email existe', 'good'];
}

// Étape 6: Vérifier le nombre de lignes et insérer des données de test si vide
$countResult = $conn->query("SELECT COUNT(*) as count FROM abonnements");
$row = $countResult->fetch_assoc();
$count = $row['count'];

if ($count == 0) {
    $today = date('Y-m-d');
    $nextMonth = date('Y-m-d', strtotime($today . ' +1 month'));
    $nextYear = date('Y-m-d', strtotime($today . ' +1 year'));
    
    $insertSQL = "INSERT INTO abonnements (nom_client, email, type_abonnement, prix, date_debut, date_fin, statut) VALUES
        ('Ahmed Ben Ali', 'ahmed@example.com', 'mensuel', 29.90, '$today', '$nextMonth', 'actif'),
        ('Fatima Khouja', 'fatima@example.com', 'annuel', 299.00, '$today', '$nextYear', 'actif'),
        ('Mohamed Salah', 'mohamed@example.com', 'mensuel', 29.90, '$today', '$nextMonth', 'actif')";
    
    if ($conn->query($insertSQL)) {
        $steps[] = ['✅ Données de test insérées', 'good'];
    } else {
        $steps[] = ['❌ Erreur insertion données: ' . $conn->error, 'error'];
    }
} else {
    $steps[] = ["✅ Table a déjà $count enregistrements", 'good'];
}

// Afficher les résultats
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup - TechCare</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
            font-size: 28px;
        }
        
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .steps {
            list-style: none;
        }
        
        .step {
            padding: 12px 16px;
            margin-bottom: 10px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            font-weight: 500;
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .step.good {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .step.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .step.warning {
            background: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
        }
        
        .actions {
            margin-top: 30px;
            text-align: center;
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn-primary {
            background: #00A86B;
            color: white;
        }
        
        .btn-primary:hover {
            background: #007a4e;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 168, 107, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #545b62;
            transform: translateY(-2px);
        }
        
        .summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
            border-left: 4px solid #0d6efd;
        }
        
        .summary strong {
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Installation TechCare</h1>
        <p class="subtitle">Configuration de la base de données</p>
        
        <ul class="steps">
            <?php foreach ($steps as $index => $step): ?>
                <li class="step <?php echo $step[1]; ?>">
                    <?php echo $step[0]; ?>
                </li>
            <?php endforeach; ?>
        </ul>
        
        <div class="summary">
            <strong>Base de données:</strong> <?php echo $db; ?><br>
            <strong>Utilisateur:</strong> <?php echo $user; ?><br>
            <strong>Serveur:</strong> <?php echo $host; ?>
        </div>
        
        <div class="actions">
            <a href="index.php?page=offres" class="btn btn-primary">🚀 Aller au site</a>
            <a href="diagnostic.php" class="btn btn-secondary">🔍 Diagnostic</a>
            <button class="btn btn-secondary" onclick="location.reload()">🔄 Rafraîchir</button>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
