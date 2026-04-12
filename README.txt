╔═══════════════════════════════════════════════════════════════╗
║                     🚀 TECHCARE SETUP                         ║
║            Système de Gestion des Abonnements en Ligne        ║
╚═══════════════════════════════════════════════════════════════╝

📋 AVANT DE COMMENCER
═════════════════════
1. Installez XAMPP (Apache + PHP + MySQL)
   Télécharger: https://www.apachefriends.org/

2. Placez ce dossier dans: c:\xampp\htdocs\web
   (L'application est accessible à http://localhost/web/)

3. Démarrez XAMPP et activez:
   ✅ Apache
   ✅ MySQL

4. Ouvrez votre navigateur et allez à: http://localhost/web/

═════════════════════════════════════════════════════════════════

🎯 ÉTAPES D'INSTALLATION (3 ÉTAPES)
════════════════════════════════════

ÉTAPE 1️⃣ : Initialiser la base de données
   URL: http://localhost/web/setup.php
   
   Cette page va:
   • Créer la base de données "techcare"
   • Créer la table "abonnements"
   • Ajouter des colonnes (email, indexes)
   • Insérer des données de test
   
   ✅ TODO: Accédez à cette page en PREMIER


ÉTAPE 2️⃣ : Vérifier l'installation
   URL: http://localhost/web/diagnostic.php
   
   Cette page montre:
   • État de la connexion à la base de données
   • Structure de la table
   • Les enregistrements dans la table
   • Des exemples de données
   
   ✅ TODO: Vérifiez que tout est ✅ (vert)


ÉTAPE 3️⃣ : Utiliser l'application
   URL: http://localhost/web/index.php
   
   • Page d'accueil avec offres
   • Formulaire de souscription
   • Page "Mes abonnements"
   • Zone admin (back-office)

═════════════════════════════════════════════════════════════════

🧪 PAGES UTILES
═══════════════

setup.php
   Initialisation automatique de la base de données
   Lancez cette page en premier!

diagnostic.php
   Affiche l'état complet du système
   Pour avoir du diagnostic

test-system.php
   Test complet de tous les composants (8 tests)
   Vérifie la connexion, les tables, les models, etc.

INIT_GUIDE.md
   Guide détaillé d'initialisation (consulter si besoin)

═════════════════════════════════════════════════════════════════

🌐 NAVIGATION DE L'APPLICATION
███████████████████████████████

FRONT OFFICE (Client Public)
──────────────────────────
http://localhost/web/
   ↓
Offres & Souscription
   ↓
Formulaire de souscrire
   ↓ Envoyer
   ↓
Confirmé → Voir "Mes abonnements"
   ↓
Gérer vos abonnements (Voir/Modifier/Résilier)


BACK OFFICE (Admin)
───────────────────
http://localhost/web/index.php?admin
   ↓
Dashboard (statistiques)
   ↓
Liste des abonnements
   ↓
Ajouter/Modifier/Supprimer abonnements

═════════════════════════════════════════════════════════════════

🛠️ PARAMÈTRES DE LA BASE DE DONNÉES
████████████████════════════════

Serveur:  localhost
User:     root
Password: (vide)
Database: techcare
Port:     3306

Fichier de config: config/database.php
(Modifiez les identifiants utilisateur/password là dedans)

═════════════════════════════════════════════════════════════════

📊 FLUX DE DONNÉES
██████████████████

1. SOUSCRIRE À UN ABONNEMENT
   └─ POST formulaire (index.php?page=souscrire)
      └─ FrontController::souscrire()
         └─ Abonnement::create($data)
            └─ INSERT dans base de données
               └─ Email reçu en paramètre
               └─ Dates calculées automatiquement
               └─ Créer confirmé


2. CONSULTER SES ABONNEMENTS (USER)
   └─ GET page (index.php?page=mes-abonnements)
      └─ FrontController::mesAbonnements()
         └─ Charge email depuis SESSION
         └─ Abonnement::getByEmail($email)
            └─ SELECT * FROM abonnements WHERE email = ?
               └─ Affiche les abonnements de l'utilisateur


3. GÉRER LES ABONNEMENTS (ADMIN)
   └─ GET page (index.php?admin&action=liste)
      └─ AdminController::listeAbonnements()
         └─ Abonnement::getAll()
            └─ SELECT * FROM abonnements
               └─ Affiche tous les abonnements du système

═════════════════════════════════════════════════════════════════

❓ PROBLÈMES COURANTS & SOLUTIONS
════════════════════════════════

❌ "Erreur lors de la souscription"
   → Allez à diagnostic.php pour vérifier la DB
   → Vérifiez que MySQL est démarré
   → Vérifiez que les logs ne signalent pas d'erreur


❌ "Table n'existe pas"
   → Allez à setup.php pour créer la table
   → Vérifiez la base de données existe


❌ "Connexion refused"
   → Vérifiez MySQL est démarré dans XAMPP
   → Vérifiez les paramètres dans config/database.php


❌ "Pas de données"
   → Allez à setup.php pour insérer les données de test
   → Testez un formulaire de souscription pour créer des données


❌ "Fichier not found"
   → Vérifiez l'installation complète de tous les fichiers
   → Vérifiez que le dossier est bien in c:\xampp\htdocs\web


📞 Consultez INIT_GUIDE.md pour plus de détails

═════════════════════════════════════════════════════════════════

✨ FONCTIONNALITÉS
████████████════

✅ Création d'abonnements (clients et admin)
✅ Lecture/Affichage des abonnements
✅ Modification d'abonnements
✅ Suppression d'abonnements
✅ Dashboard avec statistiques
✅ Calcul automatique des dates de fin
✅ Validation des e-mails
✅ Design moderne et responsive
✅ Gestion des sessions

═════════════════════════════════════════════════════════════════

📁 STRUCTURE DE FICHIERS
████████════════════════

web/
├── index.php                      Main router
├── setup.php                      Initialisation DB
├── diagnostic.php                 Diagnostic système
├── test-system.php                Tests
├── test.bat                        Test batch (Windows)
├── test.sh                         Test script (Linux)
├── INIT_GUIDE.md                   Guide détaillé
├── README.txt                      Ce fichier
├── config/
│   └── database.php                Connexion DB
├── app/
│   ├── controllers/
│   │   ├── FrontController.php     Routes client
│   │   └── AdminController.php     Routes admin
│   ├── models/
│   │   └── Abonnement.php          Logique métier
│   └── views/
│       ├── front/                  Pages client
│       └── back/                   Pages admin
├── assets/
│   ├── css/                        Feuilles de style
│   ├── js/                         Scripts JavaScript
│   └── images/                     Images/logos
└── sql/
    └── abonnements.sql             Schéma SQL

═════════════════════════════════════════════════════════════════

🎨 DESIGN & TECHNOLOGIES
═════════════════════════

Frontend:
  • HTML5
  • CSS3 moderne (Flexbox, Grid)
  • JavaScript vanille
  • Font Awesome 6.5.1 (icons)
  • Google Fonts (Inter)
  • Design responsive (mobile, tablet, desktop)

Backend:
  • PHP 7+
  • MySQLi (prepared statements)
  • Pattern MVC (Models, Views, Controllers)
  • Gestion des sessions
  • Validation des formulaires

═════════════════════════════════════════════════════════════════

📞 SUPPORT
═════════

Pour plus d'informations:
1. Consultez INIT_GUIDE.md
2. Lancez test-system.php pour diagnostiquer les problèmes
3. Vérifiez les logs Apache/PHP en cas d'erreur
4. Vérifiez la console JavaScript pour les erreurs front-end

═════════════════════════════════════════════════════════════════

🎉 PRÊT À DÉMARRER?
═══════════════════

1. Ouvrez: http://localhost/web/setup.php
2. Cliquez les boutons ✅ jusqu'au bout
3. Vérifiez tout est bon: http://localhost/web/diagnostic.php
4. Testez: http://localhost/web/
5. Créez un abonnement!
6. Consultez vos abonnements
7. Accédez à l'admin: http://localhost/web/index.php?admin

═════════════════════════════════════════════════════════════════

Version: 1.0
Dernière mise à jour: 2024
Auteur: TechCare System Development

Bonne utilisation! 🚀

═════════════════════════════════════════════════════════════════
