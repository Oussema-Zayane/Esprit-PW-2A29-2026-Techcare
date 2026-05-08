<?php
session_start();
if (!isset($_SESSION['temp_2fa_user_id'])) {
    header("Location: auth.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification 2FA - TechCare</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: 'Inter', sans-serif;
        }
        .verify-card {
            background: #1e293b;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 400px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            background: rgba(59, 130, 246, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: #3b82f6;
            font-size: 2rem;
        }
        h2 { color: white; margin-bottom: 0.5rem; }
        p { color: #94a3b8; margin-bottom: 2rem; font-size: 0.9rem; }
        .input-group { margin-bottom: 1.5rem; text-align: left; }
        .input-group label { display: block; color: #94a3b8; margin-bottom: 0.5rem; font-size: 0.8rem; }
        .input-group input {
            width: 100%;
            padding: 1rem;
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 12px;
            color: white;
            font-size: 1.2rem;
            letter-spacing: 0.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        .input-group input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
        .btn-verify {
            width: 100%;
            padding: 1rem;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-verify:hover { background: #2563eb; transform: translateY(-2px); }
        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="verify-card">
        <div class="icon-circle">
            <i class="fas fa-shield-alt"></i>
        </div>
        <h2>Vérification 2FA</h2>
        <p>Entrez le code à 6 chiffres généré par votre application Google Authenticator.</p>

        <?php if (isset($_GET['error'])): ?>
            <div class="error-msg">
                <i class="fas fa-exclamation-circle"></i> Code invalide. Veuillez réessayer.
            </div>
        <?php endif; ?>

        <form action="../../Controller/UserController.php?action=verify_2fa" method="POST">
            <div class="input-group">
                <label>Code de vérification</label>
                <input type="text" name="code" maxlength="6" pattern="\d{6}" required autofocus placeholder="000000">
            </div>
            <button type="submit" class="btn-verify">Vérifier et se connecter</button>
        </form>
        
        <div style="margin-top: 1.5rem;">
            <a href="auth.php" style="color: #94a3b8; text-decoration: none; font-size: 0.85rem;">
                <i class="fas fa-arrow-left"></i> Retour à la connexion
            </a>
        </div>
    </div>
</body>
</html>
