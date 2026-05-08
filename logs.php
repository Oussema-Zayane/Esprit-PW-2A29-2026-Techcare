<?php
/**
 * Viewer Logs PHP - Pour déboguer le système
 * URL: http://localhost/web/logs.php
 */

require_once __DIR__ . '/config/database.php';

// Récupérer les erreurs loggées au cours de cette session
$sessionLogs = [];

// Chercher les fichiers de log PHP
$phpErrorLogPath = ini_get('error_log');
$possibleLogPaths = [
    ini_get('error_log'),
    'C:/xampp/apache/logs/error.log',
    'C:/xampp/apache/logs/access.log',
    'C:/xampp/php/error.log',
    '/var/log/apache2/error.log',
    '/var/log/php-errors.log',
];

$logsToShow = [];
foreach ($possibleLogPaths as $path) {
    if ($path && file_exists($path)) {
        $logsToShow[$path] = file_get_contents($path, false, null, -2000);  // Derniers 2000 caractères
    }
}

// Si pas de logs trouvés, créer un log de test
if (empty($logsToShow)) {
    error_log("[TECHCARE] Log viewer loaded at " . date('Y-m-d H:i:s'));
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs TechCare</title>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #0b0f1a;
            color: #e0e0e0;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            color: white;
        }
        
        header h1 {
            font-size: 32px;
            margin-bottom: 5px;
        }
        
        header p {
            opacity: 0.9;
        }
        
        .controls {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 10px 20px;
            background: #00A86B;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .btn:hover {
            background: #007a4e;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #667eea;
        }
        
        .btn-secondary:hover {
            background: #5568d3;
        }
        
        .btn-danger {
            background: #dc3545;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .log-container {
            background: #1a1f2e;
            border: 1px solid #2d3748;
            border-radius: 8px;
            margin-bottom: 20px;
            overflow: hidden;
        }
        
        .log-header {
            background: #2d3748;
            padding: 15px;
            border-bottom: 1px solid #3f4754;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .log-header h2 {
            font-size: 14px;
            font-weight: 600;
            color: #00A86B;
            font-family: 'JetBrains Mono', monospace;
            margin: 0;
        }
        
        .log-stats {
            font-size: 12px;
            color: #a0aec0;
        }
        
        .log-content {
            padding: 0;
            max-height: 400px;
            overflow-y: auto;
            background: #0b0f1a;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            line-height: 1.5;
        }
        
        .log-line {
            padding: 8px 15px;
            border-bottom: 1px solid #2d3748;
            color: #a0aec0;
        }
        
        .log-line:last-child {
            border-bottom: none;
        }
        
        .log-line.error {
            background-color: rgba(220, 53, 69, 0.1);
            color: #ff6b6b;
            border-left: 3px solid #dc3545;
        }
        
        .log-line.warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
            border-left: 3px solid #ffc107;
        }
        
        .log-line.info {
            background-color: rgba(13, 110, 253, 0.1);
            color: #79c0ff;
            border-left: 3px solid #0d6efd;
        }
        
        .log-line.success {
            background-color: rgba(0, 168, 107, 0.1);
            color: #51cf66;
            border-left: 3px solid #00A86B;
        }
        
        .log-empty {
            padding: 30px;
            text-align: center;
            color: #718096;
        }
        
        .status-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .status-card {
            background: #1a1f2e;
            padding: 20px;
            border-radius: 8px;
            border-left: 3px solid #00A86B;
        }
        
        .status-card h3 {
            color: #a0aec0;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .status-card .value {
            color: #00A86B;
            font-size: 20px;
            font-weight: 600;
            font-family: 'JetBrains Mono', monospace;
        }
        
        .scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        
        .scrollbar::-webkit-scrollbar-track {
            background: #2d3748;
        }
        
        .scrollbar::-webkit-scrollbar-thumb {
            background: #00A86B;
            border-radius: 4px;
        }
        
        .scrollbar::-webkit-scrollbar-thumb:hover {
            background: #007a4e;
        }
        
        .log-content {
            scrollbar-color: #00A86B #2d3748;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📊 Log Viewer TechCare</h1>
            <p>Suivi en temps réel des erreurs et événements système</p>
        </header>
        
        <div class="controls">
            <button class="btn" onclick="location.reload();">
                🔄 Rafraîchir
            </button>
            <button class="btn btn-secondary" onclick="document.querySelector('.log-content').textContent = ''">
                🗑️ Effacer les logs
            </button>
            <a href="test-system.php" class="btn btn-secondary">
                🧪 Lancer les tests
            </a>
            <a href="index.php" class="btn btn-secondary">
                🏠 Accueil
            </a>
        </div>
        
        <div class="status-bar">
            <div class="status-card">
                <h3>Serveur</h3>
                <div class="value"><?php echo $_SERVER['SERVER_ADDR']; ?>:<?php echo $_SERVER['SERVER_PORT']; ?></div>
            </div>
            <div class="status-card">
                <h3>PHP Version</h3>
                <div class="value"><?php echo PHP_VERSION; ?></div>
            </div>
            <div class="status-card">
                <h3>Heure Serveur</h3>
                <div class="value"><?php echo date('H:i:s'); ?></div>
            </div>
            <div class="status-card">
                <h3>Mémoire Utilisée</h3>
                <div class="value"><?php echo round(memory_get_usage() / 1024 / 1024, 2); ?> MB</div>
            </div>
        </div>
        
        <?php if (!empty($logsToShow)): ?>
            <?php foreach ($logsToShow as $path => $content): ?>
            <div class="log-container">
                <div class="log-header">
                    <h2>📄 <?php echo basename($path); ?></h2>
                    <div class="log-stats">
                        Derniers événements
                    </div>
                </div>
                <div class="log-content scrollbar">
                    <?php if ($content): ?>
                        <?php foreach (array_reverse(explode("\n", trim($content))) as $line): ?>
                            <?php if (empty($line)) continue; ?>
                            <div class="log-line <?php 
                                if (stripos($line, '[error]') !== false || stripos($line, 'ERROR') !== false) echo 'error';
                                elseif (stripos($line, '[warning]') !== false || stripos($line, 'WARNING') !== false) echo 'warning';
                                elseif (stripos($line, '[TECHCARE]') !== false) echo 'info';
                                elseif (stripos($line, '✅') !== false) echo 'success';
                            ?>">
                                <?php echo htmlspecialchars($line); ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="log-empty">Aucun événement</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="log-container">
                <div class="log-header">
                    <h2>📄 Logs PHP</h2>
                </div>
                <div class="log-empty">
                    <p>Aucun fichier log trouvé</p>
                    <p style="font-size: 12px; opacity: 0.7; margin-top: 10px;">
                        Les logs s'afficheront ici quand des erreurs PHP seront générées
                    </p>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Informations de configuration -->
        <div class="log-container" style="margin-top: 30px;">
            <div class="log-header">
                <h2>⚙️ Configuration PHP</h2>
            </div>
            <div class="log-content scrollbar" style="max-height: 200px;">
                <div class="log-line">error_reporting: <?php echo error_reporting(); ?></div>
                <div class="log-line">display_errors: <?php echo ini_get('display_errors') ? 'On' : 'Off'; ?></div>
                <div class="log-line">log_errors: <?php echo ini_get('log_errors') ? 'On' : 'Off'; ?></div>
                <div class="log-line">max_execution_time: <?php echo ini_get('max_execution_time'); ?>s</div>
                <div class="log-line">memory_limit: <?php echo ini_get('memory_limit'); ?></div>
                <div class="log-line">post_max_size: <?php echo ini_get('post_max_size'); ?></div>
                <div class="log-line">upload_max_filesize: <?php echo ini_get('upload_max_filesize'); ?></div>
                <div class="log-line">Database: <?php echo Database::isConnected() ? '✅ Connected' : '❌ Disconnected'; ?></div>
            </div>
        </div>
    </div>
</body>
</html>
