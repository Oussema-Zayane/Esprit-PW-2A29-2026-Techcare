@echo off
REM Script de test d'installation TechCare pour Windows

echo ==========================================
echo 🧪 Test d'Installation TechCare
echo ==========================================
echo.

REM Vérifier que nous sommes dans le bon répertoire
if not exist "config\database.php" (
    echo ❌ Erreur: Vous n'êtes pas dans le repertoire Web TechCare
    echo Allez dans: c:\xampp\htdocs\web
    pause
    exit /b 1
)

echo ✅ Repertoire correct: %CD%
echo.

echo 1️⃣  Vérification des fichiers...
setlocal enabledelayedexpansion

set files=config\database.php app\models\Abonnement.php app\controllers\FrontController.php app\controllers\AdminController.php setup.php diagnostic.php index.php

for %%f in (%files%) do (
    if exist "%%f" (
        echo   ✅ %%f
    ) else (
        echo   ❌ %%f MANQUANT
    )
)

echo.
echo 2️⃣  Vérification PHP...
php -v >nul 2>&1
if %errorlevel% equ 0 (
    for /f "tokens=*" %%i in ('php -v ^| findstr /r "PHP"') do set php_version=%%i
    echo   ✅ !php_version!
) else (
    echo   ❌ PHP non trouve
)

echo.
echo ==========================================
echo ✨ Prochaines etapes:
echo ==========================================
echo.
echo 1. Lancez XAMPP (Apache + MySQL)
echo 2. Accedez a: http://localhost/web/setup.php
echo 3. Verifiez a: http://localhost/web/diagnostic.php  
echo 4. Testez app: http://localhost/web/index.php
echo.
echo Appuyez sur une touche pour continuer...
pause >nul
