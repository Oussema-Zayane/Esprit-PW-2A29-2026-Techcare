#!/bin/bash
# Script de test de l'installation TechCare

echo "=========================================="
echo "🧪 Test d'Installation TechCare"
echo "=========================================="
echo ""

# Vérifier que nous sommes dans le bon répertoire
if [ ! -f "config/database.php" ]; then
    echo "❌ Erreur: Vous n'êtes pas dans le répertoire Web TechCare"
    echo "Allez dans: c:\xampp\htdocs\web"
    exit 1
fi

echo "✅ Répertoire correct: $(pwd)"
echo ""

echo "1️⃣  Vérification des fichiers..."
files=(
    "config/database.php"
    "app/models/Abonnement.php"
    "app/controllers/FrontController.php"
    "app/controllers/AdminController.php"
    "setup.php"
    "diagnostic.php"
    "index.php"
)

for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "  ✅ $file"
    else
        echo "  ❌ $file MANQUANT"
    fi
done

echo ""
echo "2️⃣  Vérification PHP..."
if command -v php &> /dev/null; then
    php_version=$(php -v | head -n 1)
    echo "  ✅ PHP: $php_version"
else
    echo "  ❌ PHP non trouvé"
fi

echo ""
echo "=========================================="
echo "✨ Prochaines étapes:"
echo "=========================================="
echo ""
echo "1. Lancez XAMPP (Apacheserver + MySQL)"
echo "2. Allez à: http://localhost/web/setup.php"
echo "3. Initialis à: http://localhost/web/diagnostic.php"
echo "4. Testez app: http://localhost/web/index.php"
echo ""
echo "=========================================="
