<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechCare - Mot de passe oublié</title>
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
            margin-bottom: 1rem;
        }
        .btn-submit:hover { transform: translateY(-2px); filter: brightness(1.1); }
        
        .back-link { text-align: center; }
        .back-link a { color: var(--txt-muted); text-decoration: none; font-size: 0.85rem; transition: var(--t); }
        .back-link a:hover { color: var(--green); }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="logo">
            <i class="fas fa-bolt"></i>
            Tech<span>Care</span>
        </div>
        <h2>Mot de passe oublié ?</h2>
        <p>Entrez votre adresse email pour recevoir un lien de réinitialisation.</p>
        
        <form action="../../Controller/UserController.php?action=forgot_password" method="POST">
            <div class="input-group">
                <label><i class="fas fa-envelope"></i> Email</label>
                <input type="email" name="email" placeholder="exemple@email.com" required>
            </div>
            <button type="submit" class="btn-submit">Envoyer le lien</button>
        </form>
        
        <div class="back-link">
            <a href="auth.php"><i class="fas fa-arrow-left"></i> Retour à la connexion</a>
        </div>
    </div>
</body>
</html>
