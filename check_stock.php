<?php
require_once 'config/config.php';
$pdo = new PDO(DB_DSN, DB_USER, DB_PASS, DB_OPTIONS);
$rows = $pdo->query('SELECT id, nom, stock FROM produit')->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
