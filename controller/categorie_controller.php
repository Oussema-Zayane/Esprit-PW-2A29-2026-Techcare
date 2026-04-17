<?php
// ╔══════════════════════════════════════════════════╗
// ║  CATEGORIE CONTROLLER – API REST pour catégories ║
// ╚══════════════════════════════════════════════════╝

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../model/categorie_model.php';

$categorieModel = new CategorieModel();
$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;
$body   = json_decode(file_get_contents('php://input'), true) ?? [];

// Utilitaire pour simplifier les réponses HTTP
function sendJSON($code, $success, $message = null, $data = null) {
    http_response_code($code);
    $res = ['success' => $success];
    if ($message) $res['message'] = $message;
    if ($data)    $res['data'] = $data;
    echo json_encode($res);
    exit;
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
            if (empty($body['nom'])) {
                sendJSON(400, false, 'Le champ "nom" est obligatoire.');
            }
            sendJSON(201, true, 'Catégorie ajoutée avec succès.', $categorieModel->addCategorie($body));
            break;

        case 'PUT':
            if (!$id) sendJSON(400, false, 'ID requis.');
            if (empty($body['nom'])) {
                sendJSON(400, false, 'Le champ "nom" est obligatoire.');
            }
            $updated = $categorieModel->updateCategorie($id, $body);
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
