<?php
/**
 * Setup PHPMailer automatically
 */
$libsDir = __DIR__ . '/libs';
if (!is_dir($libsDir)) mkdir($libsDir);

$phpmailerDir = $libsDir . '/PHPMailer';
if (!is_dir($phpmailerDir)) mkdir($phpmailerDir);

$files = [
    'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/PHPMailer.php' => $phpmailerDir . '/PHPMailer.php',
    'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/SMTP.php' => $phpmailerDir . '/SMTP.php',
    'https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/Exception.php' => $phpmailerDir . '/Exception.php',
];

echo "Installation de PHPMailer...\n";

foreach ($files as $url => $path) {
    echo "Téléchargement de " . basename($path) . "...\n";
    $content = @file_get_contents($url);
    if ($content) {
        file_put_contents($path, $content);
        echo "Succès.\n";
    } else {
        echo "Échec du téléchargement de $url. Vérifiez votre connexion internet.\n";
    }
}

echo "\nInstallation terminée. Vous pouvez maintenant configurer vos identifiants dans Controller/Mailer.php\n";
?>
