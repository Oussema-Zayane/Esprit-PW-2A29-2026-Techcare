<?php
// ╔══════════════════════════════════════════════╗
// ║  CONTROLLER – API REST PHP (Simplifié)       ║
// ╚══════════════════════════════════════════════╝

// 1. Initialisation (La configuration API / CORS est incluse via model/config)
require_once __DIR__ . '/../model/model.php';

$model  = new Model();
$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;
$body   = json_decode(file_get_contents('php://input'), true) ?? [];

// 2. Utilitaire pour simplifier les réponses HTTP
function sendJSON($code, $success, $message = null, $data = null) {
    http_response_code($code);
    $res = ['success' => $success];
    if ($message) $res['message'] = $message;
    if ($data)    $res['data'] = $data;
    echo json_encode($res);
    exit;
}

// 3. Routage principal de l'API
try {
    switch ($method) {
        case 'GET':
            if ($id) {
                $product = $model->getProductById($id);
                $product ? sendJSON(200, true, null, $product) : sendJSON(404, false, 'Produit introuvable.');
            } else {
                sendJSON(200, true, null, $model->getProducts());
            }
            break;

        case 'POST':
            if (empty($body['nom']) || empty($body['prix']) || empty($body['image'])) {
                sendJSON(400, false, 'Champs obligatoires manquants (nom, prix, image).');
            }
            sendJSON(201, true, 'Produit ajouté avec succès.', $model->addProduct($body));
            break;

        case 'PUT':
            if (!$id) sendJSON(400, false, 'ID requis.');
            if (empty($body['nom']) || empty($body['prix']) || empty($body['image'])) {
                sendJSON(400, false, 'Champs obligatoires manquants.');
            }
            $updated = $model->updateProduct($id, $body);
            $updated ? sendJSON(200, true, 'Produit modifié.', $updated) : sendJSON(404, false, 'Produit introuvable.');
            break;

        case 'DELETE':
            if (!$id) sendJSON(400, false, 'ID requis.');
            $model->deleteProduct($id) ? sendJSON(200, true, 'Produit supprimé.') : sendJSON(404, false, 'Produit introuvable.');
            break;

        default:
            sendJSON(405, false, 'Méthode non autorisée.');
    }
} catch (Exception $e) {
    sendJSON(500, false, 'Erreur serveur : ' . $e->getMessage());
}
