<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechCare - Connexion & Inscription</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <style>
        /* Styles spécifiques à la page auth */
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
            max-width: 500px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r-lg);
            box-shadow: var(--sh-md);
            overflow: hidden;
            animation: fadeInUp 0.5s ease-out;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-header {
            padding: 1.8rem 2rem 1rem;
            text-align: center;
            border-bottom: 1px solid var(--border);
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--txt);
            text-decoration: none;
            margin-bottom: 0.5rem;
        }

        .logo i {
            color: var(--green);
            filter: drop-shadow(0 0 8px var(--green));
        }

        .logo span {
            color: var(--green);
        }

        .auth-header p {
            color: var(--txt-muted);
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .auth-tabs {
            display: flex;
            padding: 0 1.5rem;
            gap: 0.5rem;
            border-bottom: 1px solid var(--border);
        }

        .tab-btn {
            flex: 1;
            background: none;
            border: none;
            padding: 1rem 0.5rem;
            font: 600 0.9rem var(--font);
            color: var(--txt-muted);
            cursor: pointer;
            transition: var(--t);
            position: relative;
        }

        .tab-btn.active {
            color: var(--green);
        }

        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--green);
            border-radius: 2px;
        }

        .tab-btn:hover:not(.active) {
            color: var(--txt);
        }

        .auth-form {
            padding: 2rem 1.8rem 2rem;
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .auth-form.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-title {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            color: var(--txt);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-title i {
            color: var(--green);
        }

        .input-group {
            margin-bottom: 1.2rem;
        }

        .input-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--txt-muted);
            font-size: 0.8rem;
            font-weight: 500;
        }

        .input-group label i {
            margin-right: 0.4rem;
            color: var(--green);
            font-size: 0.75rem;
        }

        .input-group input {
            width: 100%;
            padding: 0.85rem 1rem;
            background: var(--surface2);
            border: 1px solid var(--border);
            border-radius: var(--r-md);
            color: var(--txt);
            font: 0.9rem var(--font);
            transition: var(--t);
            outline: none;
        }

        .input-group input:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(0,168,107,0.1);
        }

        .input-group input:invalid {
            border-color: var(--border);
        }

        .input-group input.error {
            border-color: var(--danger);
        }

        .input-group input.valid {
            border-color: var(--green);
        }

        .input-group input::placeholder {
            color: var(--txt-muted);
            opacity: 0.5;
        }

        /* Indicateur de force du mot de passe */
        .password-strength {
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .strength-bars {
            display: flex;
            gap: 0.3rem;
            flex: 1;
        }

        .strength-bar {
            height: 4px;
            flex: 1;
            background: var(--border);
            border-radius: 2px;
            transition: all 0.2s ease;
        }

        .strength-bar.weak {
            background: #e74c3c;
        }

        .strength-bar.medium {
            background: #f39c12;
        }

        .strength-bar.strong {
            background: #00A86B;
        }

        .strength-text {
            font-size: 0.7rem;
            font-weight: 600;
            min-width: 70px;
            text-align: right;
        }

        .strength-text.weak { color: #e74c3c; }
        .strength-text.medium { color: #f39c12; }
        .strength-text.strong { color: #00A86B; }

        /* Message d'erreur sous le champ */
        .field-error {
            font-size: 0.7rem;
            color: #e74c3c;
            margin-top: 0.3rem;
            display: none;
        }

        .field-error.show {
            display: block;
        }

        .field-success {
            font-size: 0.7rem;
            color: #00A86B;
            margin-top: 0.3rem;
            display: none;
        }

        .field-success.show {
            display: block;
        }

        .password-hint {
            font-size: 0.7rem;
            color: var(--txt-muted);
            margin-top: 0.4rem;
        }

        .btn-submit {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(135deg, var(--green), var(--green-dk));
            color: white;
            border: none;
            border-radius: var(--r-md);
            font: 600 0.95rem var(--font);
            cursor: pointer;
            transition: var(--t);
            box-shadow: var(--sh-grn);
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .message {
            margin-top: 1.2rem;
            padding: 0.8rem 1rem;
            border-radius: var(--r-md);
            text-align: left;
            font-size: 0.85rem;
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .message.success {
            background: rgba(0,168,107,0.1);
            color: #00A86B;
            border: 1px solid rgba(0,168,107,0.2);
            display: block;
        }

        .message.error {
            background: rgba(231,76,60,0.1);
            color: #e74c3c;
            border: 1px solid rgba(231,76,60,0.2);
            display: block;
        }

        .premium-alert {
            background: #fff5f5;
            border: 1px solid #feb2b2;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            color: #c53030;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .premium-alert i {
            margin-top: 0.2rem;
            font-size: 1.1rem;
        }

        .premium-alert-content {
            font-weight: 500;
            line-height: 1.4;
        }

        .greeting-title {
            font-size: 2rem;
            font-weight: 800;
            color: var(--txt);
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .greeting-subtitle {
            color: var(--txt-muted);
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
        }

        .auth-footer {
            padding: 1rem 1.8rem 1.5rem;
            text-align: center;
            border-top: 1px solid var(--border);
            color: var(--txt-muted);
            font-size: 0.75rem;
        }

        .auth-footer a {
            color: var(--green);
            text-decoration: none;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 550px) {
            body { padding: 1rem; }
            .auth-form { padding: 1.5rem; }
            .auth-header { padding: 1.2rem 1.5rem; }
        }

        /* Styles Social Login & Face Recognition */
        .social-login {
            margin-top: 1.5rem;
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
            color: var(--txt-muted);
            font-size: 0.8rem;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--border);
        }

        .divider span {
            padding: 0 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .social-buttons {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: center;
        }

        .btn-face {
            width: 100%;
            padding: 0.75rem;
            background: rgba(0, 168, 107, 0.05);
            border: 1px solid var(--green);
            color: var(--green);
            border-radius: var(--r-md);
            font: 600 0.9rem var(--font);
            cursor: pointer;
            transition: var(--t);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
        }

        .btn-face:hover {
            background: var(--green);
            color: white;
            box-shadow: 0 4px 12px rgba(0, 168, 107, 0.2);
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <div class="logo">
                <i class="fas fa-bolt"></i>
                Tech<span>Care</span>
            </div>
            <p>Votre solution technologique premium</p>
        </div>

        <?php if ($isLoggedIn): ?>
            <div style="padding: 3rem 2rem; text-align: center;">
                <div style="font-size: 4rem; color: #e74c3c; margin-bottom: 1.5rem; animation: pulse 2s infinite;">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h2 style="color: var(--txt); margin-bottom: 1rem; font-weight: 800; font-size: 1.5rem;">Session déjà active !</h2>
                <p style="color: var(--txt-muted); margin-bottom: 2.5rem; line-height: 1.6;">
                    Une session est déjà ouverte dans ce navigateur.<br>
                    L'accès à la connexion multiple est restreint pour votre sécurité.
                </p>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <a href="profile.php" class="btn-submit" style="text-decoration: none; background: linear-gradient(135deg, var(--green), var(--green-dk));">
                        <i class="fas fa-home"></i> Retourner au profil
                    </a>
                    <a href="../../Controller/logout.php" onclick="localStorage.setItem('techcare_logout', Date.now())" style="color: #e74c3c; font-size: 0.9rem; text-decoration: none; font-weight: 600; padding: 0.8rem; border: 1px solid rgba(231,76,60,0.2); border-radius: var(--r-md); background: rgba(231,76,60,0.05); transition: all 0.2s;" onmouseover="this.style.background='rgba(231,76,60,0.1)'" onmouseout="this.style.background='rgba(231,76,60,0.05)'">
                        <i class="fas fa-sign-out-alt"></i> Se déconnecter du compte actuel
                    </a>
                </div>
            </div>
            <style>
                @keyframes pulse {
                    0% { transform: scale(1); opacity: 1; }
                    50% { transform: scale(1.05); opacity: 0.8; }
                    100% { transform: scale(1); opacity: 1; }
                }
            </style>
        <?php else: ?>
        <div class="auth-tabs">
            <button class="tab-btn active" onclick="switchTab('login')">
                <i class="fas fa-key"></i> Connexion
            </button>
            <button class="tab-btn" onclick="switchTab('register')">
                <i class="fas fa-user-plus"></i> Inscription
            </button>
        </div>

        <!-- FORMULAIRE LOGIN -->
        <div id="login-form" class="auth-form active">
            <div id="login-header-dynamic">
                <div class="form-title">
                    <i class="fas fa-sign-in-alt"></i>
                    Bienvenue
                </div>
            </div>
            <div id="login-alert-container"></div>
            <form action="../../Controller/UserController.php?action=login" method="POST" onsubmit="return validateLogin()">
                <div class="input-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" id="login-email" placeholder="exemple@email.com" required>
                </div>
                <div class="input-group" style="margin-bottom: 0.5rem;">
                    <label><i class="fas fa-lock"></i> Mot de passe</label>
                    <input type="password" name="password" id="login-password" placeholder="Votre mot de passe" required>
                </div>
                <div style="text-align: right; margin-bottom: 1.2rem;">
                    <a href="forgot_password.php" style="font-size: 0.8rem; color: var(--txt-muted); text-decoration: none; transition: var(--t);" onmouseover="this.style.color='var(--green)'" onmouseout="this.style.color='var(--txt-muted)'">Mot de passe oublié ?</a>
                </div>
                <!-- Google reCAPTCHA -->
                <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI" style="margin-bottom: 1rem; display: flex; justify-content: center;"></div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-arrow-right-to-bracket"></i> Se connecter
                </button>
            </form>

            <div class="social-login">
                <div class="divider">
                    <span>OU</span>
                </div>
                <div class="social-buttons">
                    <div id="g_id_onload"
                         data-client_id="298158060437-c9b2bt9j9kt5tm3r34vahltomldfmo42.apps.googleusercontent.com"
                         data-context="signin"
                         data-ux_mode="popup"
                         data-callback="handleGoogleLogin"
                         data-auto_prompt="false">
                    </div>
                    <div class="g_id_signin"
                         data-type="standard"
                         data-shape="rectangular"
                         data-theme="outline"
                         data-text="signin_with"
                         data-size="large"
                         data-logo_alignment="left"
                         data-width="400">
                    </div>
                    
                    <button type="button" class="btn-face" onclick="startFaceLogin()">
                        <i class="fas fa-user-astronaut"></i> Connexion Faciale
                    </button>

                    <button type="button" class="btn-linkedin" onclick="startLinkedInLogin()" style="width: 100%; padding: 0.75rem; background: #0077b5; color: white; border: none; border-radius: var(--r-md); font: 600 0.9rem var(--font); cursor: pointer; transition: var(--t); display: flex; align-items: center; justify-content: center; gap: 0.6rem;">
                        <i class="fab fa-linkedin"></i> Se connecter avec LinkedIn
                    </button>
                </div>
            </div>

            <div id="login-message" class="message"></div>
        </div>

        <!-- FORMULAIRE REGISTER -->
        <div id="register-form" class="auth-form">
            <div class="form-title">
                <i class="fas fa-user-plus"></i>
                Créer un compte
            </div>
            <form action="../../Controller/UserController.php?action=register" method="POST" id="registerForm" onsubmit="return validateRegister()">
                <div class="input-group">
                    <label><i class="fas fa-user"></i> Nom</label>
                    <input type="text" name="nom" id="reg-nom" placeholder="Votre nom" required>
                </div>
                <div class="input-group">
                    <label><i class="fas fa-user"></i> Prénom</label>
                    <input type="text" name="prenom" id="reg-prenom" placeholder="Votre prénom" required>
                </div>
                <div class="input-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" id="reg-email" placeholder="exemple@email.com" required>
                    <div id="email-error" class="field-error"></div>
                </div>
                <div class="input-group">
                    <label><i class="fas fa-phone"></i> Téléphone</label>
                    <input type="tel" name="telephone" id="reg-telephone" placeholder="0612345678" maxlength="10" required>
                    <div id="phone-error" class="field-error">❌ Le téléphone doit contenir exactement 8 chiffres</div>
                    <div id="phone-success" class="field-success">✅ Numéro valide</div>
                </div>
                <div class="input-group">
                    <label><i class="fas fa-lock"></i> Mot de passe</label>
                    <input type="password" name="password" id="reg-password" placeholder="Minimum 8 caractères" required>
                    <div class="password-strength">
                        <div class="strength-bars">
                            <div class="strength-bar" id="bar1"></div>
                            <div class="strength-bar" id="bar2"></div>
                            <div class="strength-bar" id="bar3"></div>
                        </div>
                        <span class="strength-text" id="strengthText">Faible</span>
                    </div>
                    <div id="password-error" class="field-error">❌ Le mot de passe doit contenir au moins 8 caractères</div>
                    <div id="password-success" class="field-success">✅ Mot de passe valide</div>
                </div>
                <div class="input-group">
                    <label><i class="fas fa-lock"></i> Confirmer le mot de passe</label>
                    <input type="password" id="reg-confirm-password" placeholder="Retapez votre mot de passe" required>
                    <div id="confirm-error" class="field-error">❌ Les mots de passe ne correspondent pas</div>
                    <div id="confirm-success" class="field-success">✅ Les mots de passe correspondent</div>
                </div>
                
                <!-- Google reCAPTCHA -->
                <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI" style="margin-bottom: 1rem; display: flex; justify-content: center;"></div>
                
                <button type="submit" class="btn-submit" id="registerBtn">
                    <i class="fas fa-check-circle"></i> S'inscrire
                </button>
            </form>
            <div id="register-message" class="message"></div>
        </div>
        <?php endif; ?>

        <div class="auth-footer">
            <i class="fas fa-shield-alt"></i> TechCare - Sécurisé et fiable
        </div>
    </div>

    <!-- Modal pour la caméra -->
    <div id="camera-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 9999; align-items: center; justify-content: center; flex-direction: column;">
        <div style="background: var(--surface); padding: 1.5rem; border-radius: var(--r-lg); position: relative; max-width: 90%; text-align: center; border: 1px solid var(--border);">
            <h3 style="margin-bottom: 1rem; color: var(--txt);">Scan Facial de Connexion</h3>
            <div style="position: relative; overflow: hidden; border-radius: var(--r-md); background: #000; width: 400px; height: 300px; max-width: 100%;">
                <video id="video" width="400" height="300" autoplay muted style="transform: scaleX(-1);"></video>
            </div>
            <p id="modal-status" style="margin-top: 1rem; font-size: 0.9rem; color: var(--txt-muted);">Initialisation...</p>
            <button id="btn-close-modal" style="margin-top: 1.5rem; padding: 0.6rem 1.2rem; background: var(--surface2); border: 1px solid var(--border); border-radius: var(--r-md); color: var(--txt); cursor: pointer;">Annuler</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.min.js"></script>
    <script>
        // Éléments DOM
        const phoneInput = document.getElementById('reg-telephone');
        const passwordInput = document.getElementById('reg-password');
        const confirmInput = document.getElementById('reg-confirm-password');
        const registerBtn = document.getElementById('registerBtn');
        
        // Éléments pour les messages
        const phoneError = document.getElementById('phone-error');
        const phoneSuccess = document.getElementById('phone-success');
        const passwordError = document.getElementById('password-error');
        const passwordSuccess = document.getElementById('password-success');
        const confirmError = document.getElementById('confirm-error');
        const confirmSuccess = document.getElementById('confirm-success');
        
        // Éléments pour la force du mot de passe
        const bar1 = document.getElementById('bar1');
        const bar2 = document.getElementById('bar2');
        const bar3 = document.getElementById('bar3');
        const strengthText = document.getElementById('strengthText');
        
        // ========== VALIDATION TÉLÉPHONE (8 chiffres exactement) ==========
        function validatePhone() {
            const phone = phoneInput.value.trim();
            // Supprimer tous les caractères non numériques pour la validation
            const numbersOnly = phone.replace(/\D/g, '');
            const isValid = numbersOnly.length === 8;
            
            if (phone === '') {
                phoneError.classList.remove('show');
                phoneSuccess.classList.remove('show');
                phoneInput.classList.remove('error', 'valid');
                return false;
            }
            
            if (isValid) {
                phoneError.classList.remove('show');
                phoneSuccess.classList.add('show');
                phoneInput.classList.remove('error');
                phoneInput.classList.add('valid');
                return true;
            } else {
                phoneError.classList.add('show');
                phoneSuccess.classList.remove('show');
                phoneInput.classList.remove('valid');
                phoneInput.classList.add('error');
                return false;
            }
        }
        
        // Formater le téléphone en temps réel (supprime lettres, garde chiffres)
        phoneInput.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 8) value = value.slice(0, 8);
            this.value = value;
            validatePhone();
        });
        
        phoneInput.addEventListener('blur', validatePhone);
        
        // ========== VALIDATION MOT DE PASSE AVEC FORCE ==========
        function getPasswordStrength(password) {
            const length = password.length;
            if (length === 0) return { level: 0, text: '', color: '' };
            if (length >= 8 && length < 12) return { level: 1, text: 'Faible', color: 'weak' };
            if (length >= 12 && length <= 14) return { level: 2, text: 'Medium', color: 'medium' };
            if (length > 14) return { level: 3, text: 'Fort', color: 'strong' };
            return { level: 0, text: '', color: '' };
        }
        
        function updateStrengthBars(password) {
            const strength = getPasswordStrength(password);
            
            // Reset all bars
            bar1.classList.remove('weak', 'medium', 'strong');
            bar2.classList.remove('weak', 'medium', 'strong');
            bar3.classList.remove('weak', 'medium', 'strong');
            
            if (strength.level === 0) {
                strengthText.textContent = '';
                strengthText.className = 'strength-text';
                return;
            }
            
            strengthText.textContent = strength.text;
            strengthText.className = `strength-text ${strength.color}`;
            
            if (strength.level >= 1) {
                bar1.classList.add(strength.color);
            }
            if (strength.level >= 2) {
                bar2.classList.add(strength.color);
            }
            if (strength.level >= 3) {
                bar3.classList.add(strength.color);
            }
        }
        
        function validatePassword() {
            const password = passwordInput.value;
            const isValidLength = password.length >= 8;
            
            updateStrengthBars(password);
            
            if (password === '') {
                passwordError.classList.remove('show');
                passwordSuccess.classList.remove('show');
                passwordInput.classList.remove('error', 'valid');
                return false;
            }
            
            if (isValidLength) {
                passwordError.classList.remove('show');
                passwordSuccess.classList.add('show');
                passwordInput.classList.remove('error');
                passwordInput.classList.add('valid');
                return true;
            } else {
                passwordError.classList.add('show');
                passwordSuccess.classList.remove('show');
                passwordInput.classList.remove('valid');
                passwordInput.classList.add('error');
                return false;
            }
        }
        
        passwordInput.addEventListener('input', function() {
            validatePassword();
            validateConfirm(); // Revalider la confirmation quand le mot de passe change
        });
        
        passwordInput.addEventListener('blur', validatePassword);
        
        // ========== VALIDATION CONFIRMATION MOT DE PASSE ==========
        function validateConfirm() {
            const password = passwordInput.value;
            const confirm = confirmInput.value;
            
            if (confirm === '') {
                confirmError.classList.remove('show');
                confirmSuccess.classList.remove('show');
                confirmInput.classList.remove('error', 'valid');
                return false;
            }
            
            const isMatch = password === confirm && password.length >= 8;
            
            if (isMatch) {
                confirmError.classList.remove('show');
                confirmSuccess.classList.add('show');
                confirmInput.classList.remove('error');
                confirmInput.classList.add('valid');
                return true;
            } else {
                confirmError.classList.add('show');
                confirmSuccess.classList.remove('show');
                confirmInput.classList.remove('valid');
                confirmInput.classList.add('error');
                return false;
            }
        }
        
        confirmInput.addEventListener('input', validateConfirm);
        confirmInput.addEventListener('blur', validateConfirm);
        
        // ========== VALIDATION EMAIL ==========
        function validateEmail() {
            const email = document.getElementById('reg-email').value;
            const emailError = document.getElementById('email-error');
            const isValid = email.includes('@') && email.includes('.') && email.length > 5;
            
            if (email === '') {
                emailError.classList.remove('show');
                return false;
            }
            
            if (!isValid) {
                emailError.textContent = '❌ Email invalide (ex: nom@domaine.com)';
                emailError.classList.add('show');
                return false;
            } else {
                emailError.classList.remove('show');
                return true;
            }
        }
        
        document.getElementById('reg-email').addEventListener('input', validateEmail);
        document.getElementById('reg-email').addEventListener('blur', validateEmail);
        
        // ========== VALIDATION CHAMPS TEXTES ==========
        function validateTextFields() {
            const nom = document.getElementById('reg-nom').value.trim();
            const prenom = document.getElementById('reg-prenom').value.trim();
            return nom !== '' && prenom !== '';
        }
        
        // ========== ACTIVER/DÉSACTIVER BOUTON ==========
        function updateRegisterButton() {
            const isPhoneValid = validatePhone();
            const isPasswordValid = passwordInput.value.length >= 8;
            const isConfirmValid = passwordInput.value === confirmInput.value && passwordInput.value.length >= 8;
            const isEmailValid = validateEmail();
            const isTextValid = validateTextFields();
            
            const allValid = isPhoneValid && isPasswordValid && isConfirmValid && isEmailValid && isTextValid;
            registerBtn.disabled = !allValid;
            
            if (allValid) {
                registerBtn.style.opacity = '1';
            } else {
                registerBtn.style.opacity = '0.6';
            }
        }
        
        // Ajouter les écouteurs pour le bouton
        phoneInput.addEventListener('input', updateRegisterButton);
        passwordInput.addEventListener('input', updateRegisterButton);
        confirmInput.addEventListener('input', updateRegisterButton);
        document.getElementById('reg-email').addEventListener('input', updateRegisterButton);
        document.getElementById('reg-nom').addEventListener('input', updateRegisterButton);
        document.getElementById('reg-prenom').addEventListener('input', updateRegisterButton);
        
        // Initialisation        // ========== FONCTIONS PRINCIPALES ==========
        function switchTab(tab) {
            const tabs = document.querySelectorAll('.tab-btn');
            tabs.forEach(btn => btn.classList.remove('active'));
            
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            
            if (tab === 'login') {
                tabs[0].classList.add('active');
                loginForm.classList.add('active');
                registerForm.classList.remove('active');
                document.getElementById('login-message').style.display = 'none';
            } else {
                tabs[1].classList.add('active');
                registerForm.classList.add('active');
                loginForm.classList.remove('active');
                document.getElementById('register-message').style.display = 'none';
            }
        }
        
        function showMessage(type, text, containerId = 'login-message') {
            const div = document.getElementById(containerId);
            div.className = `message ${type}`;
            div.innerHTML = text;
            div.style.display = 'block';
        }

        function validateLogin() {
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            const messageDiv = document.getElementById('login-message');
            
            if (!email || !password) {
                showMessage('error', '<i class="fas fa-exclamation-triangle"></i> Veuillez remplir tous les champs', 'login-message');
                return false;
            }
            return true;
        }
        
        function validateRegister() {
            const nom = document.getElementById('reg-nom').value.trim();
            const prenom = document.getElementById('reg-prenom').value.trim();
            const email = document.getElementById('reg-email').value;
            const telephone = document.getElementById('reg-telephone').value;
            const password = passwordInput.value;
            const confirm = confirmInput.value;
            
            if (!nom || !prenom || !email || !telephone || !password || !confirm) {
                showMessage('error', '<i class="fas fa-exclamation-triangle"></i> Veuillez remplir tous les champs', 'register-message');
                return false;
            }
            
            if (password !== confirm) {
                showMessage('error', '<i class="fas fa-exclamation-triangle"></i> Les mots de passe ne correspondent pas', 'register-message');
                return false;
            }
            
            return true;
        }

        // Gestion des paramètres URL au chargement
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');
            const success = urlParams.get('success');
            const seconds = urlParams.get('seconds');
            const email = urlParams.get('email');

            if (error) {
                if (error === 'locked' || error === 'locked_now' || error === 'banned') {
                    const header = document.getElementById('login-header-dynamic');
                    const alertContainer = document.getElementById('login-alert-container');
                    
                    header.innerHTML = `
                        <h1 class="greeting-title">Bon retour 👋</h1>
                        <p class="greeting-subtitle">Connectez-vous à votre espace TechCare</p>
                    `;

                    let alertHtml = '';
                    if (error === 'banned') {
                        alertHtml = `
                            <div class="premium-alert">
                                <i class="fas fa-exclamation-triangle"></i>
                                <div class="premium-alert-content">
                                    Votre compte est définitivement désactivé. Veuillez contacter le support pour plus d'informations.
                                </div>
                            </div>
                        `;
                    } else {
                        // Calculer la date de réactivation
                        const now = new Date();
                        const timeLeft = parseInt(seconds) || 0;
                        const reactivationDate = new Date(now.getTime() + (timeLeft * 1000));
                        const options = { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' };
                        const formattedDate = reactivationDate.toLocaleDateString('fr-FR', options);

                        alertHtml = `
                            <div class="premium-alert">
                                <i class="fas fa-exclamation-triangle"></i>
                                <div class="premium-alert-content">
                                    Votre compte est temporairement désactivé. Réactivation prévue le ${formattedDate.replace(',', ' à')}
                                </div>
                            </div>
                        `;
                    }
                    alertContainer.innerHTML = alertHtml;
                } else {
                    let text = "Une erreur est survenue.";
                    let container = 'login-message';
                    
                    if (error === 'login') text = "Email ou mot de passe incorrect.";
                    if (error === 'recaptcha') text = "Veuillez valider le reCAPTCHA.";
                    if (error === 'not_verified') text = `Votre compte n'est pas encore vérifié. <a href="../../Controller/UserController.php?action=resend_verify&email=${email}" style="color: var(--green); font-weight: bold;">Renvoyer l'email</a>`;
                    if (error === 'invalid_token') text = "Lien de vérification invalide ou expiré.";
                    if (error === 'email_exists') { text = "Cet email est déjà utilisé."; container = 'register-message'; switchTab('register'); }
                    if (error === 'password_court') { text = "Le mot de passe est trop court."; container = 'register-message'; switchTab('register'); }
                    if (error === 'telephone_invalide') { text = "Numéro de téléphone invalide."; container = 'register-message'; switchTab('register'); }
                    if (error === 'multiple_sessions') text = "Une autre session a été ouverte. Vous avez été déconnecté.";
                    
                    showMessage('error', text, container);
                }
            }

            if (success) {
                let text = "";
                let container = 'login-message';
                
                if (success === 'register_verify') { text = "Inscription réussie ! Veuillez vérifier votre boîte mail pour activer votre compte."; container = 'register-message'; switchTab('register'); }
                if (success === 'verified') text = "Votre compte a été activé avec succès ! Vous pouvez maintenant vous connecter.";
                if (success === 'reset_sent') text = "Email de réinitialisation envoyé ! Vérifiez votre boîte mail.";
                if (success === 'password_reset') text = "Votre mot de passe a été réinitialisé avec succès.";
                if (success === 'account_deleted') text = "Votre compte a été supprimé avec succès.";
                
                showMessage('success', text, container);
            }

            // Nettoyer l'URL
            if (window.history.replaceState && (error || success)) {
                const url = new URL(window.location.href);
                url.search = '';
                window.history.replaceState({}, document.title, url.toString());
            }
        });

        // ========== GOOGLE LOGIN HANDLER ==========
        function handleGoogleLogin(response) {
            const idToken = response.credential;
            const messageDiv = document.getElementById('login-message');
            
            showMessage('success', '<i class="fas fa-spinner fa-spin"></i> Authentification avec Google...');
            
            fetch('../../Controller/UserController.php?action=google_login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id_token=' + encodeURIComponent(idToken)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'profile.php';
                } else {
                    showMessage('error', '<i class="fas fa-times-circle"></i> ' + (data.error || 'Erreur Google'));
                }
            })
            .catch(err => {
                console.error(err);
                showMessage('error', '<i class="fas fa-times-circle"></i> Erreur de connexion au serveur');
            });
        }

        // ========== FACE LOGIN HANDLER ==========
        const cameraModal = document.getElementById('camera-modal');
        const video = document.getElementById('video');
        const modalStatus = document.getElementById('modal-status');
        const btnClose = document.getElementById('btn-close-modal');
        let isModelsLoaded = false;

        async function loadModels() {
            if (isModelsLoaded) return;
            modalStatus.innerText = "Chargement des modèles d'IA...";
            const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
            ]);
            isModelsLoaded = true;
        }

        async function startFaceLogin() {
            cameraModal.style.display = 'flex';
            await loadModels();
            
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: {} });
                video.srcObject = stream;
                modalStatus.innerText = "Positionnez votre visage devant la caméra...";
                
                const detectionInterval = setInterval(async () => {
                    const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
                        .withFaceLandmarks()
                        .withFaceDescriptor();
                    
                    if (detection) {
                        clearInterval(detectionInterval);
                        modalStatus.innerText = "Analyse faciale en cours...";
                        
                        const descriptor = Array.from(detection.descriptor);
                        
                        const response = await fetch('../../Controller/UserController.php?action=face_login', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'descriptor=' + JSON.stringify(descriptor)
                        });
                        
                        const data = await response.json();
                        if (data.success) {
                            modalStatus.innerText = "Identification réussie ! Redirection...";
                            setTimeout(() => window.location.href = 'profile.php', 1000);
                        } else {
                            modalStatus.innerText = "Erreur: " + data.error;
                            setTimeout(() => closeModal(), 3000);
                        }
                    }
                }, 1000);
            } catch (err) {
                modalStatus.innerText = "Accès caméra refusé ou non disponible.";
                console.error(err);
            }
        }

        function closeModal() {
            const stream = video.srcObject;
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
            video.srcObject = null;
            cameraModal.style.display = 'none';
        }

        function startLinkedInLogin() {
            window.location.href = "../../Controller/UserController.php?action=linkedin_login";
        }

        btnClose.addEventListener('click', closeModal);
    </script>
</body>
</html>
