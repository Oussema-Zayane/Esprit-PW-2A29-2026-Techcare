# 📋 Exemple d'utilisation des JOINTUREs

## Structure de la base de données

### Table `categorie`
```
id (INT) - Clé primaire
├─ nom (VARCHAR 100) - Nom de la catégorie
├─ description (TEXT) - Description
├─ image (VARCHAR 500) - Image de la catégorie
├─ date_ajout (TIMESTAMP)
└─ date_modif (TIMESTAMP)
```

### Table `produit`
```
id (INT) - Clé primaire
├─ nom (VARCHAR 255)
├─ description (TEXT)
├─ prix (DECIMAL 10,2)
├─ stock (INT)
├─ categorie_id (INT) - 🔗 Clé étrangère vers categorie.id
├─ marque (VARCHAR 100)
├─ image (VARCHAR 500)
├─ etat (ENUM)
├─ date_ajout (TIMESTAMP)
└─ date_modif (TIMESTAMP)
```

---

## 🔗 Requêtes SQL de JOINTURE

### 1️⃣ JOINTURE simple (INNER JOIN) - Produits avec catégories
Retourne uniquement les produits qui ont une catégorie assignée.

```sql
SELECT p.*, c.nom as categorie_nom, c.description as categorie_description
FROM produit p
INNER JOIN categorie c ON p.categorie_id = c.id
ORDER BY p.date_ajout DESC;
```

### 2️⃣ JOINTURE gauche (LEFT JOIN) - Tous les produits
Retourne tous les produits, même ceux sans catégorie (categorie_id NULL).

```sql
SELECT p.*, c.nom as categorie_nom, c.description as categorie_description
FROM produit p
LEFT JOIN categorie c ON p.categorie_id = c.id
ORDER BY p.date_ajout DESC;
```

### 3️⃣ JOINTURE avec agrégation
Compter le nombre de produits par catégorie.

```sql
SELECT c.nom, c.description, COUNT(p.id) as nombre_produits
FROM categorie c
LEFT JOIN produit p ON p.categorie_id = c.id
GROUP BY c.id
ORDER BY nombre_produits DESC;
```

### 4️⃣ JOINTURE avec filtrage
Récupérer tous les produits Apple dans la catégorie Smartphone.

```sql
SELECT p.*, c.nom as categorie_nom
FROM produit p
LEFT JOIN categorie c ON p.categorie_id = c.id
WHERE p.marque = 'Apple' AND c.nom = 'Smartphone'
ORDER BY p.prix DESC;
```

---

## 💻 Utilisation en PHP (Model)

### Récupérer tous les produits avec leurs catégories

```php
// Dans votre contrôleur
require_once __DIR__ . '/../model/model.php';
$model = new Model();

// Utiliser la nouvelle méthode de jointure
$produits = $model->getProductsWithCategories();

// Afficher les résultats
foreach ($produits as $produit) {
    echo "Produit: " . $produit['nom'] . "\n";
    echo "Catégorie: " . ($produit['categorie_nom'] ?? 'Sans catégorie') . "\n";
    echo "---\n";
}
```

### Récupérer un produit spécifique avec sa catégorie

```php
$model = new Model();

// Utiliser la nouvelle méthode pour un produit
$produit = $model->getProductByIdWithCategorie(1);

if ($produit) {
    echo "Nom: " . $produit['nom'] . "\n";
    echo "Catégorie: " . ($produit['categorie_nom'] ?? 'Sans catégorie') . "\n";
    echo "Marque: " . $produit['marque'] . "\n";
    echo "Prix: " . $produit['prix'] . " DT\n";
}
```

---

## 📝 Nouvelles Méthodes dans Model

### 1. `getProductsWithCategories()`
Récupère tous les produits avec leurs informations de catégories.

**Retour:** `array` - Tableau de produits avec données de catégories

```php
$model->getProductsWithCategories();
```

---

### 2. `getProductByIdWithCategorie(int $id)`
Récupère un produit spécifique avec ses informations de catégorie.

**Paramètres:**
- `$id` (int) - ID du produit

**Retour:** `?array` - Produit avec données de catégories ou NULL

```php
$model->getProductByIdWithCategorie(1);
```

---

## 🗂️ Structure JSON retournée

```json
{
  "id": 1,
  "nom": "iPhone 15 Pro",
  "description": "Dernier smartphone Apple avec écran OLED",
  "prix": "1499.99",
  "stock": 10,
  "categorie_id": 1,
  "marque": "Apple",
  "image": "https://via.placeholder.com/300?text=iPhone15",
  "etat": "neuf",
  "date_ajout": "2024-04-21 10:30:45",
  "date_modif": "2024-04-21 10:30:45",
  "categorie_nom": "Smartphone",
  "categorie_description": "Téléphones mobiles et smartphones",
  "categorie_image": "https://via.placeholder.com/300?text=Smartphone"
}
```

---

## ⚠️ Points importants

1. **Clé étrangère:** La colonne `categorie_id` en table `produit` référence `id` en table `categorie`
2. **LEFT JOIN:** Utilisé par défaut pour inclure les produits sans catégorie
3. **ON DELETE SET NULL:** Si une catégorie est supprimée, `categorie_id` du produit devient NULL
4. **Index:** Les colonnes `categorie_id` et `nom` sont indexées pour optimiser les requêtes

---

## 🚀 Exemple complet d'utilisation

```php
<?php
require_once __DIR__ . '/../model/model.php';

$model = new Model();

// 1. Récupérer tous les produits avec catégories
$produits = $model->getProductsWithCategories();

// 2. Filtrer par catégorie
$produits_smartphones = array_filter($produits, function($p) {
    return $p['categorie_nom'] === 'Smartphone';
});

// 3. Afficher les produits
foreach ($produits_smartphones as $produit) {
    echo json_encode($produit, JSON_PRETTY_PRINT);
}
?>
```

