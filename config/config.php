<?php
// ╔══════════════════════════════════════════════════╗
// ║  CONFIG – Configuration de l'API & Base de       ║
// ║           données MySQL                          ║
// ╚══════════════════════════════════════════════════╝

// ── Configuration de l'API (CORS & Headers) ──────────
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ── Paramètres de connexion MySQL ────────────────────
define('DB_HOST',    'localhost');
define('DB_NAME',    'techcare');
define('DB_USER',    'root');
define('DB_PASS',    '');           // Mot de passe XAMPP par défaut = vide
define('DB_CHARSET', 'utf8mb4');

// ── Options PDO ──────────────────────────────────────
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);

// ── DSN (Data Source Name) ───────────────────────────
define('DB_DSN', 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET);
