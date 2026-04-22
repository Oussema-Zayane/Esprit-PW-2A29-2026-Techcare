<?php
// ╔════════════════════════════════════════════════════╗
// ║  TEST JOINTURE – Afficher les produits avec catégories
// ╚════════════════════════════════════════════════════╝

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/model/model.php';

$model = new Model();

// ═ Récupérer tous les produits avec jointure ═
$produits = $model->getProductsWithCategories();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Jointure Produit ↔ Catégorie</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        h1 {
            color: white;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .stat-card .number {
            font-size: 2em;
            font-weight: bold;
            color: #764ba2;
        }
        
        .table-wrapper {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        
        tbody tr:hover {
            background: #f5f5f5;
        }
        
        tbody tr:last-child td {
            border-bottom: none;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }
        
        .badge-smartphone {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .badge-laptop {
            background: #fff3e0;
            color: #e65100;
        }
        
        .badge-tablette {
            background: #f3e5f5;
            color: #6a1b9a;
        }
        
        .badge-accessoires {
            background: #e8f5e9;
            color: #2e7d32;
        }
        
        .badge-montre {
            background: #fce4ec;
            color: #c2185b;
        }
        
        .price {
            color: #2e7d32;
            font-weight: 600;
            font-size: 1.1em;
        }
        
        .stock {
            text-align: center;
            font-weight: 600;
        }
        
        .stock.low {
            color: #d32f2f;
        }
        
        .stock.medium {
            color: #f57c00;
        }
        
        .stock.high {
            color: #2e7d32;
        }
        
        .query-info {
            background: #fff9c4;
            border-left: 4px solid #fbc02d;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            color: #333;
        }
        
        .query-info h3 {
            margin-bottom: 10px;
            color: #f57f17;
        }
        
        .query-code {
            background: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            overflow-x: auto;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔗 Test de Jointure PRODUIT ↔ CATEGORIE</h1>
        
        <div class="query-info">
            <h3>📋 Requête SQL exécutée:</h3>
            <div class="query-code">
SELECT p.*, c.id as categorie_id, c.nom as categorie_nom, c.description as categorie_description<br>
FROM produit p<br>
LEFT JOIN categorie c ON p.categorie_id = c.id<br>
ORDER BY p.date_ajout DESC
            </div>
        </div>
        
        <div class="stats">
            <div class="stat-card">
                <h3>Total Produits</h3>
                <div class="number"><?= count($produits) ?></div>
            </div>
            <div class="stat-card">
                <h3>Catégories Utilisées</h3>
                <div class="number"><?= count(array_unique(array_column($produits, 'categorie_nom'))) ?></div>
            </div>
            <div class="stat-card">
                <h3>Prix Moyen</h3>
                <div class="number"><?= number_format(array_sum(array_column($produits, 'prix')) / count($produits), 2) ?> DT</div>
            </div>
            <div class="stat-card">
                <h3>Stock Total</h3>
                <div class="number"><?= array_sum(array_column($produits, 'stock')) ?></div>
            </div>
        </div>
        
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Produit</th>
                        <th>Catégorie</th>
                        <th>Marque</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>État</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produits as $produit): ?>
                    <tr>
                        <td><?= $produit['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($produit['nom']) ?></strong>
                            <br><small style="color: #999;"><?= htmlspecialchars(substr($produit['description'], 0, 50)) ?>...</small>
                        </td>
                        <td>
                            <?php
                                $categorie = strtolower(str_replace(' ', '_', $produit['categorie_nom']));
                                $badge_class = 'badge badge-' . $categorie;
                            ?>
                            <span class="<?= $badge_class ?>">
                                <?= htmlspecialchars($produit['categorie_nom']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($produit['marque']) ?></td>
                        <td class="price"><?= number_format($produit['prix'], 2, ',', ' ') ?> DT</td>
                        <td class="stock <?= $produit['stock'] < 5 ? 'low' : ($produit['stock'] < 15 ? 'medium' : 'high') ?>">
                            <?= $produit['stock'] ?> 📦
                        </td>
                        <td>
                            <span class="badge" style="background: <?= $produit['etat'] === 'neuf' ? '#c8e6c9' : '#ffe0b2' ?>; color: <?= $produit['etat'] === 'neuf' ? '#2e7d32' : '#e65100' ?>">
                                <?= ucfirst($produit['etat']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div style="text-align: center; color: white; margin-top: 30px;">
            <p>✅ La jointure entre <strong>produit</strong> et <strong>categorie</strong> est active!</p>
            <p style="font-size: 0.9em; margin-top: 10px;">Chaque produit est maintenant lié à une catégorie via la clé étrangère <code style="background: rgba(0,0,0,0.2); padding: 2px 6px; border-radius: 3px;">categorie_id</code></p>
        </div>
    </div>
</body>
</html>
