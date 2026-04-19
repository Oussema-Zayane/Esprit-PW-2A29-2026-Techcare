# ⚡ GUIDE RAPIDE - TECHCARE

## 🎯 Objectif du projet

Gérer les **abonnements clients** avec interface admin (back office) et comptoir public (front office).

---

## 🚀 Démarrage rapide

### 1. Accéder au site
```
http://localhost/web/
```

### 2. Pages principales

#### Front Office (PUBLIC - Libre accès)
| URL | Page | Rôle |
|-----|------|------|
| `?page=accueil` | Accueil | Bienvenue TechCare |
| `?page=offres` | Offres | Voir les plans |
| `?page=souscrire` | Souscrire | Créer un abonnement |
| `?page=mes-abonnements` | Mes abon. | Voir mes abonnements |

#### Back Office (ADMIN - Protégé)
| URL | Page | Action |
|-----|------|--------|
| `?action=login` | Login | Se connecter |
| `?action=dashboard` | Dashboard | Voir stats |
| `?action=liste` | Liste | Voir tous les abon. |
| `?action=ajouter` | Ajouter | Créer un abon. |
| `?action=modifier` | Modifier | Éditer un abon. |
| `?action=detail` | Détail | Voir les infos |
| `?action=supprimer` | Supprimer | Effacer un abon. |

---

## 🔑 Identifiants

**Admin Back Office:**
- Username: `admin`
- Password: `admin`

---

## 💡 Concepts clés

### 1. Routeur (index.php)

**Un seul fichier d'entrée** pour toute l'application.

```
Requête HTTP
    ↓
Les paramètres GET sont lus
    ↓
page=xxx   → FrontController (public)
action=xxx → AdminController (admin - protégé)
    ↓
La bonne méthode est appelée
    ↓
Affiche la vue appropriée
```

### 2. Session et Authentification

**Flux connexion:**
```
1. utilisateur@admin clique Admin → Login
2. Affichage du formulaire
3. Admin rentre "admin" / "admin"
4. Validation : $_SESSION['user_id'] = 1
5. Redirection → Dashboard
6. Les pages admin vérifient $_SESSION['user_id']
7. Si absent → redirection Login
```

### 3. Modèle (Abonnement.php)

**Toutes les opérations sur la BDD :**

```php
$model = new Abonnement();

// Lire
$tous = $model->getAll();
$un = $model->getById(1);
$du_client = $model->getByEmail('email@...');

// Créer
$id = $model->create([...]);

// Modifier
$model->update($id, [...]);

// Supprimer
$model->delete($id);

// Statistiques
$nb = $model->count();
$actifs = $model->countActive();
$revenus = $model->getTotalRevenue();
```

### 4. Contrôleurs (AdminController, FrontController)

**Contiennent la logique métier :**

```
Reçois la requête
    ↓
Valide les données
    ↓
Appelle le modèle pour BDD
    ↓
Charge la vue (template HTML)
    ↓
Affiche le résultat
```

---

## 📊 CRUD Expliqué

### CREATE - Créer un abonnement

**Parcours client:**
1. Clique "Souscrire" (page=souscrire)
2. Remplit le formulaire (nom, email, type)
3. Valide
4. Abonnement créé ✓

**Backend:**
```php
// FrontController::souscrire() [POST]
$data = [
    'nom_client' => $_POST['nom_client'],
    'email' => $_POST['email'],
    'type_abonnement' => $_POST['type_abonnement'],
    'prix' => 29.90 ou 299,  // Auto selon type
    'date_debut' => date('Y-m-d'),
    'date_fin' => date_fin_calculee
];

$this->model->create($data);
// → INSERT en BDD
```

### READ - Lire des abonnements

**Admin voit la liste:**
```
AdminController::listeAbonnements()
    ↓
$abonnements = $this->model->getAll();
    ↓
SELECT * FROM abonnements
    ↓
Affiche dans un tableau
```

**Client retrouve ses abon:**
```
FrontController::mesAbonnements()
    ↓
$email = $_SESSION['user_email'];
$abonnements = $this->model->getByEmail($email);
    ↓
SELECT * FROM abonnements WHERE email = ?
    ↓
Affiche ses abonnements
```

### UPDATE - Modifier un abonnement

**Admin édite:**
1. Clique "Modifier" sur un abonnement
2. Formulaire pré-rempli
3. Change les données
4. Valide
5. Abonnement mis à jour ✓

**Backend:**
```php
AdminController::modifierAbonnement() [POST]
    ↓
Récupère l'ID via GET
    ↓
$this->model->getById($id)  // Charge l'abon
    ↓
Affiche le formulaire pré-rempli
    ↓
Si POST reçu :
    Valide les données
    $this->model->update($id, $data);
    // UPDATE en BDD
```

### DELETE - Supprimer un abonnement

**Admin supprime:**
1. Clique "Supprimer" sur un abonnement
2. (Optionnel: confirmation)
3. Abonnement supprimé ✓

**Backend:**
```php
AdminController::supprimerAbonnement()
    ↓
$id = $_GET['id'];
    ↓
$this->model->delete($id);
    ↓
DELETE FROM abonnements WHERE id = ?
    ↓
Redirection vers liste
```

---

## 🔒 Sécurité - Points clés

### 1. Prepared Statements

**Prévient l'injection SQL :**
```php
// ✓ CORRECT (sûr)
$stmt = $this->conn->prepare("SELECT * FROM abonnements WHERE id = ?");
$stmt->bind_param("i", $id);

// ✗ DANGEREUX (vulnérable)
$sql = "SELECT * FROM abonnements WHERE id = " . $_GET['id'];
```

### 2. Sanitization

**Nettoie les inputs :**
```php
htmlspecialchars($_POST['nom'])                    // HTML escape
filter_var($email, FILTER_SANITIZE_EMAIL)         // Email clean
floatval($_POST['prix'])                            // Nombre sûr
```

### 3. Validation

**Vérifie les données :**
```php
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Email invalide
}
```

### 4. Authentification

**Protège les pages admin :**
```php
// Si pas connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit();
}
```

---

## 📱 Fichiers HTML (Vues)

### Structure Front Office
```
front/
├── abonnements/
│   ├── index.php       ← Liste des offres (page=offres)
│   ├── souscrire.php   ← Formulaire souscription
│   └── suivi.php       ← Suivi du client
└── layout/
    ├── header.php      ← Navigation + Logo
    └── footer.php      ← Footer général
```

### Structure Back Office
```
back/
├── abonnements/
│   ├── liste.php       ← Table complète
│   ├── ajouter.php     ← Formulaire +
│   ├── modifier.php    ← Formulaire édition
│   └── detail.php      ← Fiche détail
└── layout/
    ├── header.php      ← Titre admin
    ├── sidebar.php     ← Menu latéral
    └── footer.php      ← Footer
```

### Forms POST

**Tous les formulaires envoient via POST :**

```html
<form method="POST" action="index.php?action=ajouter">
    <input name="nom_client" required />
    <input name="email" type="email" required />
    <select name="type_abonnement">
        <option>mensuel</option>
        <option>annuel</option>
    </select>
    <button type="submit">Valider</button>
</form>
```

**Le contrôleur récupère via :**
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom_client'] ?? '';
    // traiter...
}
```

---

## 💾 Base de Données

### Connexion (config/database.php)

```php
// Credentials
localhost, root, (empty), techcare

// Charset : utf8mb4 (accents, emojis)
```

### Table abonnements

```sql
-- Colonnes clés :
id                  [PK]
nom_client          [VARCHAR]
email               [VARCHAR] - retrouver pour client
type_abonnement     [ENUM: mensuel|annuel|ponctuel]
prix                [DECIMAL]
date_debut          [DATE]
date_fin            [DATE]
statut              [ENUM: actif|suspendu|expiré|annulé]
created_at          [TIMESTAMP]
updated_at          [TIMESTAMP]
```

### Indexes

```sql
INDEX idx_email (email)     ← Retrouver par email
INDEX idx_statut (statut)   ← Filtrer par statut
```

**Performance**: Les requêtes utilisant email ou statut sont rapides.

---

## 🧪 Test de flux complet

### Scenario 1: Client se subscrit (Front)

```
1. Accès http://localhost/web/
   ↓
2. Clique "Dénouvertes offres" → page=offres
   ↓
3. Liste des plans affichée
   ↓
4. Clique "Souscrire" → page=souscrire
   ↓
5. Remplir : Jean Dupont, jean@mail.tn, Mensuel
   ↓
6. POST envoie les données
   ↓
7. FrontController::souscrire() valide
   ↓
8. Abonnement::create() insère en BDD
   ↓
9. Redirection vers page=mes-abonnements
   ↓
10. ✓ Abonnement visible dans "Mes abonnements"
```

### Scenario 2: Admin gère (Back)

```
1. Clique Admin → action=login
   ↓
2. Rentre admin / admin
   ↓
3. POST valide → $_SESSION['user_id'] = 1
   ↓
4. Redirection action=dashboard
   ↓
5. ✓ Dashboard visible avec stats
   ↓
6. Clique "Liste" → action=liste
   ↓
7. ✓ Tous les abonnements listés
   ↓
8. Clique "Modifier" sur un → action=modifier&id=1
   ↓
9. Formulaire pré-rempli
   ↓
10. Modifie le statut à "suspendu"
   ↓
11. POST mets à jour
   ↓
12. ✓ Table mise à jour
```

---

## 🎨 CSS & Styling

### Variables

```css
:root {
    --bg-primary: #0b0f1a;      /* Fond noir */
    --bg-secondary: #1a1f32;    /* Fond gris foncé */
    --accent: #00A86B;          /* Accent vert */
    --txt: white;               /* Texte blanc */
    --txt-secondary: #333;      /* Texte noir */
    --border: #2a3547;
}
```

### Utilisation

```css
body {
    background-color: var(--bg-primary);  /* Noir */
    color: #333;                          /* Texte noir */
}

button, .btn-primary {
    background: var(--accent);  /* Vert */
    color: white;
}

.active {
    border-color: var(--accent);  /* Bordure verte */
}
```

---

## 📝 Exemple: Ajouter un abonnement (Admin)

### Étape 1: Affichage formulaire

**URL:** `http://localhost/web/index.php?action=ajouter`

**Backend:**
```php
// index.php ligne 53
action = 'ajouter';
$controller = new AdminController();
$controller->ajouterAbonnement();  // GET = affiche formulaire
```

### Étape 2: Formulaire rempli

```html
<form method="POST" action="index.php?action=ajouter">
    Nom: <input name="nom_client" value="Ahmed Ben Ali" />
    Email: <input name="email" value="ahmed@mail.tn" />
    Type: <select name="type_abonnement">
        <option selected>mensuel</option>
    </select>
    <button>Ajouter</button>
</form>
```

### Étape 3: Soumission POST

**Backend:**
```php
// AdminController::ajouterAbonnement [POST]
$data = [
    'nom_client' => 'Ahmed Ben Ali',
    'email' => 'ahmed@mail.tn',
    'type_abonnement' => 'mensuel',
    'prix' => 29.90,
    'date_debut' => date('Y-m-d'),
    'date_fin' => date('Y-m-d', strtotime('+1 month'))
];

$id = $this->model->create($data);

if ($id) {
    // ✓ Succès → Redirection
    header('Location: index.php?action=liste&success=1');
}
```

### Étape 4: BDD - Insertion

```sql
INSERT INTO abonnements (nom_client, email, ...)
VALUES ('Ahmed Ben Ali', 'ahmed@mail.tn', ...);
-- ✓ ID 1 créé
```

### Étape 5: Admin voit la liste

**URL:** `http://localhost/web/index.php?action=liste`

```php
// AdminController::listeAbonnements()
$abonnements = $this->model->getAll();
// SELECT * FROM abonnements
// → Array avec tous les abonnements

require 'back/abonnements/liste.php';
// Affiche un tableau HTML
```

---

## ❌ Erreurs courantes

### Erreur 1: "Call to undefined method"

**Cause:** Mauvais nom de méthode dans le contrôleur

**Solution:**
```php
// index.php doit appeler le bon nom
$controller->ajouterAbonnement()  // ✓
// PAS : $controller->ajouter()   // ✗
```

### Erreur 2: "Failed to open stream"

**Cause:** Include path incorrecte

**Solution:**
```php
require_once __DIR__ . '/../models/Abonnement.php';  // ✓
// PAS : require 'models/Abonnement.php';            // ✗
```

### Erreur 3: "Warning: Undefined variable"

**Cause:** Variable non initialisée

**Solution:**
```php
$nom = $_POST['nom'] ?? 'Défaut';  // ✓ - sûr
// PAS : $nom = $_POST['nom'];      // ✗ - erreur si absent
```

### Erreur 4: Pas de base de données

**Solution:**
1. Lancer MySQL (XAMPP)
2. Accéder à `?page=offres`
3. La BDD `techcare` est créée automatiquement
4. Vérifier dans PhpMyAdmin

---

## 🔗 Liens rapides

| Action | URL |
|--------|-----|
| Accueil | `?page=accueil` |
| Offres | `?page=offres` |
| Souscrire | `?page=souscrire` |
| Mes abon. | `?page=mes-abonnements` |
| Login | `?action=login` |
| Dashboard | `?action=dashboard` |
| Liste (admin) | `?action=liste` |
| Ajouter (admin) | `?action=ajouter` |

---

## 📞 Questions ?

Consulter:
1. **DOCUMENTATION.md** - Vue d'ensemble complète
2. **Commentaires dans le code** - Explication détaillée
3. **PhpMyAdmin** - Vérifier la BDD
4. **Console du navigateur** - Voir les erreurs

**Format des logs:**
```
[TECHCARE] Message - Projet TechCare
```

