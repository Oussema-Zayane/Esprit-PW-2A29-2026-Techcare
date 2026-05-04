<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechCare - Réinitialisation</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: var(--bg);
        }
        .auth-container {
            width: 100%;
            max-width: 450px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r-lg);
            box-shadow: var(--sh-md);
            overflow: hidden;
            padding: 2.5rem;
        }
        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--txt);
            margin-bottom: 2rem;
        }
        .logo i { color: var(--green); }
        .logo span { color: var(--green); }
        
        h2 { text-align: center; margin-bottom: 1rem; color: var(--txt); }
        p { text-align: center; color: var(--txt-muted); font-size: 0.9rem; margin-bottom: 2rem; }
        
        .input-group { margin-bottom: 1.5rem; }
        .input-group label { display: block; margin-bottom: 0.5rem; color: var(--txt-muted); font-size: 0.85rem; }
        .input-group input {
            width: 100%;
            padding: 0.9rem 1rem;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: var(--r-md);
            color: var(--txt);
            outline: none;
        }
        .input-group input:focus { border-color: var(--green); }
        
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--green), var(--green-dk));
            color: white;
            border: none;
            border-radius: var(--r-md);
            font-weight: 600;
            cursor: pointer;
            transition: var(--t);
        }
        .btn-submit:hover { transform: translateY(-2px); filter: brightness(1.1); }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="logo">
            <i class="fas fa-bolt"></i>
            Tech<span>Care</span>
        </div>
        <h2>Nouveau mot de passe</h2>
        <p>Veuillez entrer votre nouveau mot de passe ci-dessous.</p>
        
        <form action="../../Controller/UserController.php?action=reset_password" method="POST" onsubmit="return validateReset()">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
            
            <div class="input-group">
                <label><i class="fas fa-lock"></i> Nouveau mot de passe</label>
                <input type="password" name="password" id="password" placeholder="Minimum 8 caractères" required>
            </div>
            
            <div class="input-group">
                <label><i class="fas fa-lock"></i> Confirmer le mot de passe</label>
                <input type="password" id="confirm" placeholder="Retapez le mot de passe" required>
            </div>
            
            <button type="submit" class="btn-submit">Mettre à jour le mot de passe</button>
        </form>
    </div>

    <script>
        function validateReset() {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm').value;
            
            if (password.length < 8) {
                alert("Le mot de passe doit contenir au moins 8 caractères.");
                return false;
            }
            
            if (password !== confirm) {
                alert("Les mots de passe ne correspondent pas.");
                return false;
            }
            
            return true;
        }
    </script>
</body>
</html>
