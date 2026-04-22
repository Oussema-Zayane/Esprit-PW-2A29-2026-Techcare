<?php
// ╔════════════════════════════════════════════════════╗
// ║  API – Récupérer les produits en JSON             ║
// ╚════════════════════════════════════════════════════╝

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/model/model.php';

try {
    $model = new Model();
    $produits = $model->getProductsWithCategories();
    
    // Transformer les données pour l'API
    $response = array_map(function($produit) {
        return [
            'id' => $produit['id'],
            'nom' => $produit['nom'],
            'description' => $produit['description'],
            'prix' => (float)$produit['prix'],
            'stock' => (int)$produit['stock'],
            'marque' => $produit['marque'] ?? 'Non spécifiée',
            'categorie' => $produit['categorie_nom'] ?? 'Sans catégorie',
            'image' => $produit['image'] ?? 'https://via.placeholder.com/300?text=Produit',
            'etat' => $produit['etat'] ?? 'neuf',
            'date_ajout' => $produit['date_ajout'] ?? date('Y-m-d H:i:s')
        ];
    }, $produits);
    
    echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la récupération des produits']);
}
?>
