<?php
// ╔══════════════════════════════════════════════╗
// ║  UPLOAD IMAGE – Traitement des fichiers      ║
// ╚══════════════════════════════════════════════╝

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Configuration
$upload_dir = __DIR__ . '/../uploads/';
$max_size = 5 * 1024 * 1024; // 5 MB
$allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

// Créer le dossier s'il n'existe pas
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Vérifier la présence du fichier
if (empty($_FILES['image'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Aucun fichier envoyé']);
    exit;
}

$file = $_FILES['image'];

// Vérifications
if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Erreur lors du téléchargement: ' . $file['error']]);
    exit;
}

if ($file['size'] > $max_size) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Fichier trop volumineux (max 5 MB)']);
    exit;
}

if (!in_array($file['type'], $allowed_types)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Type de fichier non autorisé (JPEG, PNG, WebP, GIF)']);
    exit;
}

// Générer un nom unique
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid('product_', true) . '.' . $ext;
$filepath = $upload_dir . $filename;

// Sauvegarder le fichier
if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la sauvegarde du fichier']);
    exit;
}

// Retourner le chemin relatif utilisable depuis le HTML
$relative_path = '../uploads/' . $filename;

http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Image téléchargée avec succès',
    'image' => $relative_path
]);
