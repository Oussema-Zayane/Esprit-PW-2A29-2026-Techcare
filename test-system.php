<?php
/**
 * Test complet du système TechCare
 * URL: http://localhost/web/test-system.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$results = [];

// ===== TEST 1: Connecxion à la base de données =====
try {
    require_once __DIR__ . '/config/database.php';
    $conn = Database::getConnection();
    if ($conn && !$conn->connect_error) {
        $results[] = ['✅ Connexion DB', 'success', 'Connecté à localhost'];
    } else {
        $results[] = ['❌ Connexion DB', 'error', $conn->connect_error ?? 'Erreur inconnue'];
    }
} catch (Exception $e) {
    $results[] = ['❌ Connexion DB', 'error', $e->getMessage()];
}

// ===== TEST 2: Table abonnements existence =====
try {
    $tableCheck = $conn->query("SHOW TABLES LIKE 'abonnements'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $results[] = ['✅ Table abonnements', 'success', 'Table existe'];
    } else {
        $results[] = ['❌ Table abonnements', 'error', 'Table n\'existe pas'];
    }
} catch (Exception $e) {
    $results[] = ['❌ Table abonnements', 'error', $e->getMessage()];
}

// ===== TEST 3: Colonnes vitales =====
$requiredColumns = ['id', 'nom_client', 'email', 'type_abonnement', 'prix', 'date_debut', 'date_fin', 'statut'];
try {
    $columnsResult = $conn->query("DESCRIBE abonnements");
    $existingColumns = [];
    if ($columnsResult) {
        while ($col = $columnsResult->fetch_assoc()) {
            $existingColumns[] = $col['Field'];
        }
    }
    
    $missingColumns = array_diff($requiredColumns, $existingColumns);
    if (empty($missingColumns)) {
        $results[] = ['✅ Colonnes de la table', 'success', 'Toutes les colonnes existent'];
    } else {
        $results[] = ['❌ Colonnes de la table', 'error', 'Colonnes manquantes: ' . implode(', ', $missingColumns)];
    }
} catch (Exception $e) {
    $results[] = ['❌ Colonnes de la table', 'error', $e->getMessage()];
}

// ===== TEST 4: Données de test =====
try {
    $countResult = $conn->query("SELECT COUNT(*) as total FROM abonnements");
    if ($countResult) {
        $row = $countResult->fetch_assoc();
        $count = $row['total'];
        if ($count > 0) {
            $results[] = ['✅ Données de test', 'success', "$count enregistrements trouvés"];
        } else {
            $results[] = ['⚠️ Données de test', 'warning', 'Aucun enregistrement (peutêtre normal)'];
        }
    }
} catch (Exception $e) {
    $results[] = ['❌ Données de test', 'error', $e->getMessage()];
}

// ===== TEST 5: Model Abonnement =====
try {
    require_once __DIR__ . '/app/models/Abonnement.php';
    $model = new Abonnement();
    $abos = $model->getAll();
    $results[] = ['✅ Model Abonnement', 'success', 'Classe chargée et fonctionnelle'];
} catch (Exception $e) {
    $results[] = ['❌ Model Abonnement', 'error', $e->getMessage()];
}

// ===== TEST 6: Controllers =====
try {
    if (file_exists(__DIR__ . '/app/controllers/FrontController.php')) {
        require_once __DIR__ . '/app/controllers/FrontController.php';
        $results[] = ['✅ FrontController', 'success', 'Fichier chargé'];
    } else {
        $results[] = ['❌ FrontController', 'error', 'Fichier not found'];
    }
} catch (Exception $e) {
    $results[] = ['❌ FrontController', 'error', $e->getMessage()];
}

try {
    if (file_exists(__DIR__ . '/app/controllers/AdminController.php')) {
        require_once __DIR__ . '/app/controllers/AdminController.php';
        $results[] = ['✅ AdminController', 'success', 'Fichier chargé'];
    } else {
        $results[] = ['❌ AdminController', 'error', 'Fichier not found'];
    }
} catch (Exception $e) {
    $results[] = ['❌ AdminController', 'error', $e->getMessage()];
}

// ===== TEST 7: Insérer une donnée de test =====
try {
    $testData = [
        'nom_client' => 'Test Auto ' . date('H:i:s'),
        'email' => 'test-' . time() . '@techcare.tn',
        'type_abonnement' => 'mensuel',
        'prix' => 29.90,
        'date_debut' => date('Y-m-d'),
        'date_fin' => date('Y-m-d', strtotime('+1 month')),
    ];
    
    $model = new Abonnement();
    $inserted = $model->create($testData);
    
    if ($inserted) {
        $results[] = ['✅ Création abonnement', 'success', "Enregistrement #$inserted inséré"];
    } else {
        $results[] = ['❌ Création abonnement', 'error', 'Éc heck lors de l\'insertion'];
    }
} catch (Exception $e) {
    $results[] = ['❌ Création abonnement', 'error', $e->getMessage()];
}

// ===== TEST 8: Récupérer par email =====
try {
    $model = new Abonnement();
    $records = $model->getByEmail($testData['email']);
    if (count($records) > 0) {
        $results[] = ['✅ Récupération par email', 'success', count($records) . ' enregistrement(s) trouvé(s)'];
    } else {
        $results[] = ['⚠️ Récupération par email', 'warning', 'Aucun enregistrement'];
    }
} catch (Exception $e) {
    $results[] = ['❌ Récupération par email', 'error', $e->getMessage()];
}

// ===== AFFICHAGE DES RÉSULTATS =====
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Système - TechCare</title>
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
            max-width: 700px;
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
        
        .test-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 12px;
            border-radius: 6px;
            border-left: 4px solid;
            animation: slideIn 0.3s ease-out forwards;
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
        
        .test-item.success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        
        .test-item.error {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        
        .test-item.warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }
        
        .test-name {
            flex: 1;
            font-weight: 600;
        }
        
        .test-detail {
            font-size: 12px;
            opacity: 0.8;
            margin-top: 5px;
        }
        
        .summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 6px;
            margin-top: 30px;
            text-align: center;
            border-top: 2px solid #dee2e6;
        }
        
        .summary-stat {
            display: inline-block;
            margin: 10px 20px;
        }
        
        .summary-stat strong {
            font-size: 18px;
            color: #333;
        }
        
        .actions {
            margin-top: 20px;
            text-align: center;
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 10px 20px;
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
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #545b62;
            transform: translateY(-2px);
        }
        
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 10px;
        }
        
        .status-success {
            background: #28a745;
            color: white;
        }
        
        .status-error {
            background: #dc3545;
            color: white;
        }
        
        .status-warning {
            background: #ffc107;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test Complet du Système</h1>
        <p class="subtitle">Vérification de tous les composants TechCare</p>
        
        <div class="tests">
            <?php foreach ($results as $index => $result): ?>
                <div class="test-item <?php echo $result[1]; ?>" style="animation-delay: <?php echo ($index * 0.05); ?>s">
                    <div>
                        <div class="test-name">
                            <?php echo $result[0]; ?>
                            <span class="status-badge status-<?php echo $result[1]; ?>">
                                <?php echo strtoupper($result[1]); ?>
                            </span>
                        </div>
                        <div class="test-detail"><?php echo $result[2]; ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php 
            $successCount = count(array_filter($results, fn($r) => $r[1] === 'success'));
            $errorCount = count(array_filter($results, fn($r) => $r[1] === 'error'));
            $warningCount = count(array_filter($results, fn($r) => $r[1] === 'warning'));
            $totalTests = count($results);
        ?>
        
        <div class="summary">
            <div class="summary-stat">
                <strong><?php echo $totalTests; ?></strong><br>
                Tests
            </div>
            <div class="summary-stat" style="color: #28a745;">
                <strong><?php echo $successCount; ?></strong><br>
                Succès
            </div>
            <div class="summary-stat" style="color: #ffc107;">
                <strong><?php echo $warningCount; ?></strong><br>
                Avertissements
            </div>
            <div class="summary-stat" style="color: #dc3545;">
                <strong><?php echo $errorCount; ?></strong><br>
                Erreurs
            </div>
        </div>
        
        <div class="actions">
            <a href="setup.php" class="btn btn-secondary">⚙️ Setup</a>
            <a href="diagnostic.php" class="btn btn-secondary">🔍 Diagnostic</a>
            <a href="index.php?page=offres" class="btn btn-primary">🚀 Aller à l'App</a>
        </div>
    </div>
</body>
</html>
