<?php
// ╔══════════════════════════════════════════════════╗
// ║  CATEGORIE CONTROLLER – API REST pour catégories ║
// ╚══════════════════════════════════════════════════╝

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../model/categorie_model.php';

$categorieModel = new CategorieModel();
$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Variables pour les données
$nom = '';
$description = '';
$image = '';

// Traiter les données selon le type de requête
if ($method === 'POST' || $method === 'PUT') {
    if (!empty($_FILES['image']['name'])) {
        $nom = isset($_POST['nom']) ? sanitize($_POST['nom']) : '';
        $description = isset($_POST['description']) ? sanitize($_POST['description']) : '';
        
        if (!empty($_FILES['image']['name'])) {
            $image = handleImageUpload($_FILES['image']);
        }
    } else {
        $body = json_decode(file_get_contents('php://input'), true) ?? [];
        $nom = isset($body['nom']) ? sanitize($body['nom']) : '';
        $description = isset($body['description']) ? sanitize($body['description']) : '';
        
        if ($method === 'PUT' && !$image) {
            $existing = $categorieModel->getCategorieById($id);
            $image = $existing['image'] ?? '';
        }
    }
}

// Utilitaire pour simplifier les réponses HTTP
function sendJSON($code, $success, $message = null, $data = null) {
    http_response_code($code);
    $res = ['success' => $success];
    if ($message) $res['message'] = $message;
    if ($data)    $res['data'] = $data;
    echo json_encode($res);
    exit;
}

// Utilitaire pour nettoyer les données
function sanitize($value) {
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}

// Utilitaire pour gérer l'upload d'image
function handleImageUpload($file) {
    $uploadDir = __DIR__ . '/../uploads/categories/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        sendJSON(400, false, 'Type de fichier non autorisé. Utilisez JPEG, PNG, GIF ou WebP.');
    }
    
    if ($file['size'] > 5 * 1024 * 1024) {
        sendJSON(400, false, 'Fichier trop volumineux. Maximum 5MB.');
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'categorie_' . time() . '_' . uniqid() . '.' . $ext;
    $filepath = $uploadDir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        sendJSON(500, false, 'Erreur lors de l\'upload du fichier.');
    }
    
    return $filename;
}

// Routage principal de l'API
try {
    switch ($method) {
        case 'GET':
            if ($id) {
                $categorie = $categorieModel->getCategorieById($id);
                $categorie ? sendJSON(200, true, null, $categorie) : sendJSON(404, false, 'Catégorie introuvable.');
            } else {
                sendJSON(200, true, null, $categorieModel->getCategories());
            }
            break;

        case 'POST':
            if (empty($nom)) {
                sendJSON(400, false, 'Le champ "nom" est obligatoire.');
            }
            sendJSON(201, true, 'Catégorie ajoutée avec succès.', $categorieModel->addCategorie([
                'nom' => $nom,
                'description' => $description,
                'image' => $image
            ]));
            break;

        case 'PUT':
            if (!$id) sendJSON(400, false, 'ID requis.');
            if (empty($nom)) {
                sendJSON(400, false, 'Le champ "nom" est obligatoire.');
            }
            
            if (empty($image)) {
                $existing = $categorieModel->getCategorieById($id);
                $image = $existing['image'] ?? '';
            }
            
            $updated = $categorieModel->updateCategorie($id, [
                'nom' => $nom,
                'description' => $description,
                'image' => $image
            ]);
            $updated ? sendJSON(200, true, 'Catégorie modifiée.', $updated) : sendJSON(404, false, 'Catégorie introuvable.');
            break;

        case 'DELETE':
            if (!$id) sendJSON(400, false, 'ID requis.');
            $categorieModel->deleteCategorie($id) ? sendJSON(200, true, 'Catégorie supprimée.') : sendJSON(404, false, 'Catégorie introuvable.');
            break;

        default:
            sendJSON(405, false, 'Méthode non autorisée.');
    }
} catch (Exception $e) {
    sendJSON(500, false, 'Erreur serveur : ' . $e->getMessage());
}
