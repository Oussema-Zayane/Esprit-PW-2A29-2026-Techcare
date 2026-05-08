# 📚 DOCUMENTATION - TECHCARE

## Vue d'ensemble

TechCare est une application de **gestion d'abonnements** avec deux parties distinctes :
- **Front Office** : Site public où les clients souscrivent aux abonnements
- **Back Office** : Zone admin où les administrateurs gèrent les abonnements

---

## 🏗️ Architecture MVC

Le projet suit le pattern **MVC** (Modèle-Vue-Contrôleur) :

```
index.php (Routeur principal)
    ↓
├─ page=xxx → FrontController (Pages publiques)
│   ├─ accueil()
│   ├─ offres()
│   ├─ souscrire()
│   └─ mesAbonnements()
│
└─ action=xxx → AdminController (Pages admin)
    ├─ login() [Page de connexion]
    ├─ dashboard() [Statistiques]
    ├─ listeAbonnements() [Liste complète]
    ├─ ajouterAbonnement() [Créer]
    ├─ modifierAbonnement() [Éditer]
    ├─ detailAbonnement() [Voir détails]
    └─ supprimerAbonnement() [Supprimer]
```

---

## 📁 Structure des fichiers

```
web/
├── index.php                          [ROUTEUR - Point d'entrée]
├── config/
│   └── database.php                  [Classe Database - Connexion MySQL]
├── app/
│   ├── controllers/
│   │   ├── FrontController.php        [Logique pages publiques]
│   │   └── AdminController.php        [Logique administration]
│   ├── models/
│   │   └── Abonnement.php            [Modèle - CRUD abonnements]
│   └── views/
│       ├── front/                     [Pages publiques]
│       │   ├── layout/
│       │   │   ├── header.php
│       │   │   └── footer.php
│       │   └── abonnements/
│       │       ├── index.php
│       │       ├── souscrire.php
│       │       └── suivi.php
│       └── back/                      [Pages admin]
│           ├── layout/
│           │   ├── header.php
│           │   ├── sidebar.php
│           │   └── footer.php
│           └── abonnements/
│               ├── liste.php
│               ├── ajouter.php
│               ├── modifier.php
│               └── detail.php
└── assets/
    ├── css/
    │   └── style.css
    └── js/
        └── abonnements.js
```

---

## 🔄 Flux de l'Application

### 1️⃣ FRONT OFFICE (Public) - Pour les CLIENTS

```
Utilisateur arrive
        ↓
index.php?page=accueil
        ↓
FrontController::accueil()
        ↓
Affiche front/layout/header.php + page + footer.php
```

**Page d'accueil → Offres → Souscrire → Mes abonnements**

#### Exemple : Souscrire à un plan

```
1. Utilisateur clique "Souscrire"
   ↓
2. GET index.php?page=souscrire
   ↓
3. FrontController::souscrire() affiche le formulaire
   ↓
4. Utilisateur remplit (nom, email, type)
   ↓
5. POST au même formulaire
   ↓
6. FrontController::souscrire() valide les données
   ↓
7. Appel Abonnement::create($data)
   ↓
8. INSERT en BDD
   ↓
9. Redirection vers "Mes abonnements" avec succès
```

### 2️⃣ BACK OFFICE (Admin) - Pour les ADMINISTRATEURS

**Protégé par authentification** : Seuls les utilisateurs connectés (session_id) accèdent.

```
Admin arrive
        ↓
index.php?action=login  [PAS connecté]
        ↓
AdminController::login() affiche formulaire
        ↓
Admin rentre : admin / admin
        ↓
POST le formulaire
        ↓
Validation réussie : $_SESSION['user_id'] = créée
        ↓
Redirection vers dashboard
        ↓
index.php?action=dashboard  [CONNECTÉ]
        ↓
AdminController::dashboard() affiche statistiques
        ↓
Affiche back/layout/header + dashboard.php + footer.php
```

**Fonctionnalités admin :**
- Voir liste complète des abonnements
- Ajouter un abonnement
- Modifier un abonnement
- Supprimer un abonnement
- Consulter statistiques (revenu total, nombre Client actifs)

---

## 🔐 Authentification & Session

### Comment fonctionne l'authentification ?

1. **Démarrage de session** (index.php ligne 8)
   ```php
   session_start();  // Lance PHP_SESSION
   ```

2. **Connexion** (AdminController::login())
   ```php
   $_SESSION['user_id'] = 1;        // Marquer comme connecté
   $_SESSION['username'] = 'admin';
   ```

3. **Vérification** (index.php avant action admin)
   ```php
   if (!isset($_SESSION['user_id'])) {
       header('Location: index.php?action=login');
       exit();
   }
   ```

4. **Déconnexion** (AdminController::logout())
   ```php
   session_destroy();  // Détruit la session
   ```

### Identifiants
- **Username**: `admin`
- **Password**: `admin`

> 💡 **À MODIFIER EN PRODUCTION** : Les identifiants sont en dur dans le code. Il faut les mettre dans une BDD sécurisée avec hash de mot de passe.

---

## 🗄️ Base de Données

### Création automatique

La classe **Database** crée automatiquement :
1. La base de données `techcare` (si elle n'existe pas)
2. La table `abonnements` (si elle n'existe pas)
3. Ajoute les colonnes manquantes
4. Crée les indexes
5. Insère des données de test

### Schéma de la table `abonnements`

```sql
CREATE TABLE abonnements (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    nom_client          VARCHAR(100) NOT NULL,
    email               VARCHAR(100),
    type_abonnement     ENUM('mensuel', 'annuel', 'ponctuel'),
    prix                DECIMAL(10,2) NOT NULL,
    date_debut          DATE NOT NULL,
    date_fin            DATE NOT NULL,
    statut              ENUM('actif', 'suspendu', 'expiré', 'annulé'),
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_statut (statut)
)
```

### Colonnes

| Colonne | Type | Rôle |
|---------|------|------|
| `id` | INT | Identifiant unique |
| `nom_client` | VARCHAR(100) | Nom du client |
| `email` | VARCHAR(100) | Email pour retrouver ses abonnements |
| `type_abonnement` | ENUM | Mensuel / Annuel / Ponctuel |
| `prix` | DECIMAL(10,2) | Prix en dinars |
| `date_debut` | DATE | Date de début |
| `date_fin` | DATE | Date d'expiration |
| `statut` | ENUM | actif / suspendu / expiré / annulé |
| `created_at` | TIMESTAMP | Date de création (auto) |
| `updated_at` | TIMESTAMP | Date de dernière modification (auto) |

---

## 💰 Types d'abonnements

### Mensuel
- **Prix** : 29.90 DT
- **Durée** : 1 mois
- **Date fin** : calculée automatiquement

### Annuel
- **Prix** : 299.00 DT
- **Durée** : 1 an
- **Date fin** : calculée automatiquement

### Ponctuel
- **Prix** : variable (défini par l'admin)
- **Durée** : variable

---

## 🛡️ Sécurité

### Protections implémentées

1. **Prepared Statements** (Anti-injection SQL)
   ```php
   $stmt = $this->conn->prepare("SELECT * FROM abonnements WHERE id = ?");
   $stmt->bind_param("i", $id);  // "i" pour INTEGER
   ```

2. **Sanitization des inputs**
   ```php
   htmlspecialchars()        // Échappe les caractères HTML
   filter_var(FILTER_SANITIZE_EMAIL)  // Net toie l'email
   floatval()                // Convertit en nombre
   ```

3. **Session-based Authentication**
   - Vérification de `$_SESSION['user_id']` avant accès admin

4. **Validation d'emails**
   ```php
   filter_var($email, FILTER_VALIDATE_EMAIL)
   ```

---

## 🔧 Utilisation des CRUD

### CREATE (Créer)

```php
$data = [
    'nom_client' => 'Jean Dupont',
    'email' => 'jean@example.com',
    'type_abonnement' => 'mensuel',
    'prix' => 29.90,
    'date_debut' => '2026-04-12',
    'date_fin' => '2026-05-12'
];

$abonnement = new Abonnement();
$id = $abonnement->create($data);  // Retourne l'ID si succès
```

### READ (Lire)

```php
// Tous les abonnements
$tous = $abonnement->getAll();

// Un abonnement par ID
$un = $abonnement->getById(1);

// Abonnements d'un client
$utilisateur = $abonnement->getByEmail('jean@example.com');
```

### UPDATE (Modifier)

```php
$data['nom_client'] = 'Jean Martin';  // Nouvelle valeur
$abonnement->update(1, $data);  // Met à jour l'ID 1
```

### DELETE (Supprimer)

```php
$abonnement->delete(1);  // Supprime l'ID 1
```

---

## 📊 Statistiques

### Dashboard Admin

Automatiquement calculées depuis la BDD :

```php
$stats = [
    'total_abonnements' => $model->count(),           // Tous
    'abonnements_actifs' => $model->countActive(),    // Que actifs
    'revenue_total' => $model->getTotalRevenue(),     // CA total
];
```

**Affichage :** `index.php?action=dashboard`

---

## 🎨 Design & Styling

### CSS Variables (root)
```css
--bg-primary: #0b0f1a;      /* Fond principal - noir profond */
--bg-secondary: #1a1f32;    /* Fond secondaire */
--accent: #00A86B;          /* Accent vert */
--txt: white;               /* Texte blanc */
--txt-secondary: #333;      /* Texte foncé */
--border: #2a3547;          /* Bordures */
```

### Thème
- **Couleur principale** : Bleu foncé (#0A1F44)
- **Accent** : Vert (#00A86B)
- **Arrière-plan** : Noir profond (#0b0f1a)
- **Texte** : Noir pour lisibilité (#333)

---

## 🚀 Déploiement

### Vérification avant go-live

1. Modifier les identifiants admin dans **AdminController.php**
2. Utiliser une BDD sécurisée avec authentification
3. Implémenter un vrai système de login (BDD + hash)
4. Mettre en HTTPS
5. Supprimer les données de test
6. Configurer les variables d'environnement

---

## 📝 Fichiers de Configuration

### config/database.php

```php
private static $host = 'localhost';  // Serveur MySQL
private static $user = 'root';       // Utilisateur MySQL
private static $pass = '';           // Mot de passe
private static $db = 'techcare';     // Nom BDD
```

---

## 🐛 Debugging

### Logs des erreurs

Les logs de ce projet sont préfixés `[TECHCARE]` :

```php
error_log("[TECHCARE] Message d'erreur");
```

**Localisation** : 
- Linux/Mac : `/var/log/php-errors.log`
- Windows XAMPP : `xampp/php/logs/php_error.log`

---

## ✅ Checklist Installation

- [ ] XAMPP démarré (Apache + MySQL)
- [ ] Dossier `/web` dans `xampp/htdocs/`
- [ ] Accéder à `http://localhost/web/`
- [ ] Page d'accueil affichée
- [ ] Bouton Admin visible (coin haut droit)
- [ ] Se connecter avec `admin`/`admin`
- [ ] Dashboard admin visible
- [ ] Créer un abonnement
- [ ] Vérifier la BDD dans PhpMyAdmin

---

## 📞 Support

Pour toutes questions, consulter le code commenté :
- `index.php` - Routage
- `AdminController.php` - Logique admin
- `FrontController.php` - Logique publique
- `Abonnement.php` - Modèle de données
- `database.php` - Gestion BDD

**Chaque méthode est documentée avec :`
- Description du rôle
- Paramètres attendus
- Valeurs retournées
