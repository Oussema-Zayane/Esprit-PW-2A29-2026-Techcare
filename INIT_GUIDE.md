# 🚀 Guide d'Initialisation TechCare

## Pages Utiles

### 1. **Page Setup** (Initialisation automatique)
**URL:** `http://localhost/web/setup.php`

Cette page va :
- ✅ Créer la base de données `techcare`
- ✅ Créer la table `abonnements` avec tous les champs
- ✅ Ajouter les colonnes manquantes (email)
- ✅ Créer les indexes
- ✅ Insérer les données de teste

**À faire en premier !**

### 2. **Page Diagnostic** (Vérification de l'état)
**URL:** `http://localhost/web/diagnostic.php`

Montre :
- État de la connexion DB
- Structure de la table
- Nombre de records
- Exemples de données

### 3. **Application TechCare**
**URL:** `http://localhost/web/index.php`
- Page d'accueil avec les offres
- Front office pour les clients
- Back office pour admin (avec `/admin`)

**Admin:**
`http://localhost/web/index.php?admin&action=dashboard`

---

## 🔧 Ordre d'Initialisation Recommandé

1. **Première visite** → Allez à `setup.php`
2. **Vérifier l'état** → Allez à `diagnostic.php`
3. **Utiliser l'app** → Allez à `index.php`

---

## 📝 Flux de l'Application

### Front Office (Client)
```
index.php            → Affiche la page d'accueil avec offres
index.php?page=souscrire     → Formulaire de souscription
index.php?page=mes-abonnements → Liste des abonnements  
  ↓ (requête POST)
FrontController::souscrire() 
  ↓
Abonnement::create($data)    → Insère dans la DB
```

### Back Office (Admin)
```
index.php?admin&action=dashboard → Dashboard
index.php?admin&action=liste      → Liste tous les abonnements
index.php?admin&action=ajouter    → Ajouter un abonnement
index.php?admin&action=modifier   → Éditer un abonnement
```

---

## 🐛 Dépannage

### "Erreur lors de la souscription"
1. Vérifiez `diagnostic.php` pour voir l'état de la DB
2. Vérifiez que la colonne `email` existe
3. Vérifiez les logs Apache: `php_error.log` ou `error.log`

### "Table n'existe pas"
1. Accédez à `setup.php`
2. Vérifiez que la base de données est bien créée
3. Accédez à `diagnostic.php` pour confirmer

### "Pas de données"
1. Vérifiez `diagnostic.php`
2. Testez un formulaire de souscription

---

## 📊 Base de Données

**Database:** `techcare`
**Table:** `abonnements`

### Colonnes
| Colonne | Type | Descri action |
|---------|------|---|
| id | INT AUTO_INCREMENT | ID unique |
| nom_client | VARCHAR(100) | Nom du client |
| email | VARCHAR(100) | Email du client |
| type_abonnement | ENUM | mensuel/annuel/ponctuel |
| prix | DECIMAL(10,2) | Prix de l'abonnement |
| date_debut | DATE | Date de début |
| date_fin | DATE | Date de fin |
| statut | ENUM | actif/suspendu/expiré/annulé |
| created_at | TIMESTAMP | Date de création |
| updated_at | TIMESTAMP | Date de dernière modification |

---

## ⚙️ Configuration

**Fichier:** `/config/database.php`

```php
Database::getConnection();
// Serveur: localhost
// User: root
// Password: (vide)
// DB: techcare
```

Si vous changez les paramètres, modifiez les variables dans `database.php`

---

## ✅ Checklist de Démarrage

- [ ] Xamp est lancé (Apache + MySQL)
- [ ] Ouvrez `http://localhost/web/setup.php`
- [ ] Vérifiez que tous les ✅ s'affichent
- [ ] Ouvrez `http://localhost/web/diagnostic.php`
- [ ] Vérifiez la structure de la table
- [ ] Testez l'app: `http://localhost/web/index.php?page=offres`
- [ ] Testez une souscription
- [ ] Accédez à vos abonnements: `index.php?page=mes-abonnements`
- [ ] Testez le back office: `index.php?admin`

---

## 📚 Fichiers Importants

```
/
├── setup.php                      ← Initialisation auto
├── diagnostic.php                 ← Diagnostic DB
├── index.php                      ← Point d'entrée
├── config/
│   └── database.php               ← Config DB
├── app/
│   ├── controllers/
│   │   ├── FrontController.php    ← Routes public
│   │   └── AdminController.php    ← Routes admin
│   ├── models/
│   │   └── Abonnement.php         ← Logique DB
│   └── views/
│       ├── front/                 ← Pages public
│       └── back/                  ← Pages admin
└── assets/
    ├── css/                       ← Styles
    └── js/                        ← Scripts
```

---

**Auteur:** TechCare System  
**Version:** 1.0  
**Last Updated:** 2024
