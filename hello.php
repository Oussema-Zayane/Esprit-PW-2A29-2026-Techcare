<?php
/**
 * Page d'accueil TechCare - Initialisation et bienvenue
 * Auto-redirect vers setup.php si pas de données
 */

// Charger la configuration
require_once __DIR__ . '/config/database.php';

// Vérifier l'état du système
$systemReady = false;
$dbConnected = false;
$tableExists = false;
$hasData = false;

try {
    $conn = Database::getConnection();
    $dbConnected = ($conn && !$conn->connect_error);
    
    if ($dbConnected) {
        $tableCheck = $conn->query("SHOW TABLES LIKE 'abonnements'");
        $tableExists = ($tableCheck && $tableCheck->num_rows > 0);
        
        if ($tableExists) {
            $dataCheck = $conn->query("SELECT COUNT(*) as count FROM abonnements");
            if ($dataCheck) {
                $row = $dataCheck->fetch_assoc();
                $hasData = ($row['count'] > 0);
            }
        }
    }
    
    $systemReady = ($dbConnected && $tableExists && $hasData);
} catch (Exception $e) {
    // Système pas prêt
}

// Si le système n'est pas prêt, rediriger vers setup après un court délai
if (!$systemReady && !isset($_GET['skip_setup'])) {
    // Auto-redirect vers setup après 3 secondes
    header('Refresh: 3; url=setup.php');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechCare - Bienvenue</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            width: 100%;
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #00A86B 0%, #009a5f 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .logo {
            font-size: 40px;
            margin-bottom: 15px;
        }
        
        .header h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px;
        }
        
        .status {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .status-item {
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .status-item i {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .status-item.ok {
            background: #d4edda;
            color: #155724;
        }
        
        .status-item.loading {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-item.ko {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-item h3 {
            font-size: 14px;
            font-weight: 600;
        }
        
        .status-item p {
            font-size: 12px;
            opacity: 0.8;
        }
        
        .info-box {
            background: #f0f7ff;
            border-left: 4px solid #0d6efd;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
        }
        
        .info-box h3 {
            color: #0d6efd;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-box p {
            color: #333;
            line-height: 1.6;
            font-size: 14px;
        }
        
        .action-buttons {
            display: grid;
            grid-template-columns: auto auto;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            font-size: 14px;
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
        
        .loading-animation {
            text-align: center;
            padding: 20px;
        }
        
        .spinner {
            display: inline-block;
            width: 30px;
            height: 30px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #00A86B;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .redirect-notice {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            font-size: 14px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 30px 0;
        }
        
        .feature {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
            text-align: center;
            border: 1px solid #dee2e6;
        }
        
        .feature i {
            font-size: 24px;
            color: #00A86B;
            margin-bottom: 10px;
        }
        
        .feature p {
            font-size: 12px;
            color: #666;
            font-weight: 500;
        }
        
        @media (max-width: 600px) {
            .header {
                padding: 30px 20px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .content {
                padding: 20px;
            }
            
            .action-buttons {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">🔧</div>
            <h1>Bienvenue dans TechCare</h1>
            <p>Système de Gestion des Abonnements en Ligne</p>
        </div>
        
        <!-- Content -->
        <div class="content">
            <!-- Status System -->
            <div class="status">
                <div class="status-item <?php echo $dbConnected ? 'ok' : 'ko'; ?>">
                    <i class="fas fa-database"></i>
                    <h3>Base de Données</h3>
                    <p><?php echo $dbConnected ? 'Connectée' : 'Déconnectée'; ?></p>
                </div>
                
                <div class="status-item <?php echo $tableExists ? 'ok' : 'ko'; ?>">
                    <i class="fas fa-table"></i>
                    <h3>Table</h3>
                    <p><?php echo $tableExists ? 'Existe' : 'Créer'; ?></p>
                </div>
                
                <div class="status-item <?php echo $hasData ? 'ok' : 'loading'; ?>">
                    <i class="fas <?php echo $hasData ? 'fa-check-circle' : 'fa-hourglass'; ?>"></i>
                    <h3>Données</h3>
                    <p><?php echo $hasData ? 'Présentes' : 'aucune'; ?></p>
                </div>
                
                <div class="status-item <?php echo $systemReady ? 'ok' : 'loading'; ?>">
                    <i class="fas <?php echo $systemReady ? 'fa-check-circle' : 'fa-spinner'; ?>" style="animation: <?php echo !$systemReady ? 'spin 1s linear infinite' : 'none'; ?>"></i>
                    <h3>Système</h3>
                    <p><?php echo $systemReady ? 'Prêt' : 'Configuration'; ?></p>
                </div>
            </div>
            
            <!-- Auto-redirect notice -->
            <?php if (!$systemReady): ?>
            <div class="redirect-notice">
                <i class="fas fa-info-circle"></i> Redirection vers l'installation en cours...
                <button style="background: none; border: none; color: #155724; cursor: pointer; text-decoration: underline; font-weight: 600;" onclick="location.href='setup.php';">
                    Allez à setup.php maintenant
                </button>
            </div>
            <?php endif; ?>
            
            <!-- Info Box -->
            <div class="info-box">
                <h3>
                    <i class="fas fa-lightbulb"></i>
                    <?php echo $systemReady ? '✨ Système Prêt!' : '⚙️ Configuration Nécessaire'; ?>
                </h3>
                <p>
                    <?php if ($systemReady): ?>
                        Votre système TechCare est maintenant prêt à être utilisé. Cliquez sur le bouton ci-dessous pour accéder à l'application.
                    <?php else: ?>
                        Le système détecte que la configuration initiale n'a pas été complétée. Cliquez sur "Configuration" ci-dessous pour initialiser la base de données et les tables.
                    <?php endif; ?>
                </p>
            </div>
            
            <!-- Features -->
            <div class="features">
                <div class="feature">
                    <i class="fas fa-check"></i>
                    <p>Gestion Complète</p>
                </div>
                <div class="feature">
                    <i class="fas fa-mobile-alt"></i>
                    <p>Responsive Design</p>
                </div>
                <div class="feature">
                    <i class="fas fa-shield-alt"></i>
                    <p>Sécurisé</p>
                </div>
                <div class="feature">
                    <i class="fas fa-tachometer-alt"></i>
                    <p>Rapide</p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div style="text-align: center; display: flex; justify-content: center; flex-wrap: wrap; gap: 15px;">
                <?php if (!$systemReady): ?>
                    <a href="setup.php" class="btn btn-primary">
                        <i class="fas fa-wrench"></i> Configuration
                    </a>
                <?php else: ?>
                    <a href="index.php?page=offres" class="btn btn-primary">
                        <i class="fas fa-rocket"></i> Accéder à l'App
                    </a>
                <?php endif; ?>
                
                <a href="diagnostic.php" class="btn btn-secondary">
                    <i class="fas fa-stethoscope"></i> Diagnostic
                </a>
                <a href="test-system.php" class="btn btn-secondary">
                    <i class="fas fa-flask"></i> Tests
                </a>
            </div>
            
            <?php if ($systemReady): ?>
            <div style="text-align: center; margin-top: 30px;">
                <a href="index.php?admin" style="color: #00A86B; text-decoration: none; font-weight: 600; font-size: 14px;">
                    <i class="fas fa-lock"></i> Accès Admin
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
