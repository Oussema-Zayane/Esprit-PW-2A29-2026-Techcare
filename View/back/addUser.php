<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../front/auth.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un utilisateur - TechCare Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        .admin-section {
            margin: 2rem auto;
            max-width: 800px;
            background: var(--surface);
            border-radius: var(--r-lg);
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: var(--sh-lg);
        }
        .admin-header {
            padding: 1.5rem 2rem;
            background: linear-gradient(135deg, rgba(0,168,107,0.1), transparent);
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-header h3 {
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--txt);
            margin: 0;
        }
        .admin-content {
            padding: 2rem;
            background: var(--surface2);
        }
        .btn-back {
            background: rgba(255,255,255,0.05);
            color: var(--txt-muted);
            border: 1px solid var(--border);
            border-radius: var(--r-sm);
            padding: 0.5rem 1rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
            text-decoration: none;
            transition: var(--t);
        }
        .btn-back:hover {
            background: rgba(255,255,255,0.1);
            color: var(--txt);
        }

        .admin-input-group {
            position: relative;
            margin-bottom: 1.2rem;
        }
        .admin-input-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--txt-muted);
            font-size: 0.9rem;
            font-weight: 500;
        }
        .admin-input-group input, .admin-input-group select {
            width: 100%;
            padding: 0.8rem 1rem;
            border-radius: var(--r-sm);
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--txt);
            font-family: var(--font);
            transition: var(--t);
        }
        .admin-input-group input:focus, .admin-input-group select:focus {
            outline: none;
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(0,168,107,0.15);
        }
        
        .admin-field-error {
            font-size: 0.8rem;
            color: var(--danger);
            margin-top: 0.4rem;
            display: none;
        }
        .admin-field-error.show { display: block; }
        
        .admin-field-success {
            font-size: 0.8rem;
            color: var(--green);
            margin-top: 0.4rem;
            display: none;
        }
        .admin-field-success.show { display: block; }
        
        .admin-input-group input.error, .admin-input-group select.error {
            border-color: var(--danger) !important;
        }
        .admin-input-group input.valid, .admin-input-group select.valid {
            border-color: var(--green) !important;
        }

        .password-strength-admin {
            margin-top: 0.6rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .strength-bars-admin {
            display: flex;
            gap: 0.3rem;
            flex: 1;
        }
        .strength-bar-admin {
            height: 4px;
            flex: 1;
            background: var(--border);
            border-radius: 2px;
            transition: all 0.2s ease;
        }
        .strength-bar-admin.weak { background: var(--danger); }
        .strength-bar-admin.medium { background: var(--warning); }
        .strength-bar-admin.strong { background: var(--green); }
        .strength-text-admin { 
            font-size: 0.8rem; 
            font-weight: 600; 
            min-width: 60px; 
            text-align: right; 
        }
        .strength-text-admin.weak { color: var(--danger); }
        .strength-text-admin.medium { color: var(--warning); }
        .strength-text-admin.strong { color: var(--green); }

        .admin-submit-btn {
            background: linear-gradient(135deg, var(--green), var(--green-dk));
            color: white;
            border: none;
            border-radius: var(--r-sm);
            padding: 0.9rem 2rem;
            font-size: 1rem;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            transition: var(--t);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
            box-shadow: var(--sh-grn);
        }
        .admin-submit-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: var(--surface3);
            box-shadow: none;
        }
        .admin-submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }
    </style>
</head>
<body class="front-body">

    <!-- Barre de navigation simplifiée pour Admin -->
    <nav class="navbar" style="justify-content: center;">
        <a href="dashboard.php" class="navbar-brand">
            <i class="fas fa-bolt brand-icon"></i>
            Tech<span class="brand-accent">Care</span>
        </a>
    </nav>

    <div class="main-content" style="padding: 2rem;">
        <div class="admin-section">
            <div class="admin-header">
                <h3><i class="fas fa-user-plus" style="color: var(--green);"></i> Ajouter un nouvel utilisateur</h3>
                <a href="../front/profile.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Retour à l'acceuil
                </a>
            </div>
            
            <div class="admin-content">
                <form action="../../Controller/UserController.php?action=add" method="POST" id="addUserForm" novalidate onsubmit="return validateFormSumbit(event)">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="admin-input-group">
                            <label for="add_nom">Nom</label>
                            <input type="text" name="nom" id="add_nom" placeholder="Saisir le nom" required>
                            <div id="add_nom_error" class="admin-field-error"><i class="fas fa-exclamation-circle"></i> Le nom est requis</div>
                        </div>
                        
                        <div class="admin-input-group">
                            <label for="add_prenom">Prénom</label>
                            <input type="text" name="prenom" id="add_prenom" placeholder="Saisir le prénom" required>
                            <div id="add_prenom_error" class="admin-field-error"><i class="fas fa-exclamation-circle"></i> Le prénom est requis</div>
                        </div>
                    </div>
                    
                    <div class="admin-input-group">
                        <label for="add_email">Adresse Email</label>
                        <input type="email" name="email" id="add_email" placeholder="Saisir l'adresse email" required>
                        <div id="add_email_error" class="admin-field-error"><i class="fas fa-exclamation-circle"></i> Email invalide (ex: nom@domaine.com)</div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="admin-input-group">
                            <label for="add_telephone">Téléphone (8 chiffres)</label>
                            <input type="tel" name="telephone" id="add_telephone" placeholder="Saisir le numéro" maxlength="10">
                            <div id="add_phone_error" class="admin-field-error"><i class="fas fa-exclamation-circle"></i> Exactement 8 chiffres</div>
                            <div id="add_phone_success" class="admin-field-success"><i class="fas fa-check-circle"></i> Format valide</div>
                        </div>
                        
                        <div class="admin-input-group">
                            <label for="add_role">Rôle</label>
                            <select name="role" id="add_role">
                                <option value="user">Utilisateur</option>
                                <option value="admin">Administrateur</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="admin-input-group">
                        <label for="add_password">Mot de passe</label>
                        <input type="password" name="password" id="add_password" placeholder="Minimum 8 caractères" required>
                        <div class="password-strength-admin">
                            <div class="strength-bars-admin">
                                <div class="strength-bar-admin" id="add_bar1"></div>
                                <div class="strength-bar-admin" id="add_bar2"></div>
                                <div class="strength-bar-admin" id="add_bar3"></div>
                            </div>
                            <span class="strength-text-admin" id="add_strengthText"></span>
                        </div>
                        <div id="add_password_error" class="admin-field-error"><i class="fas fa-exclamation-circle"></i> Au moins 8 caractères requis</div>
                    </div>
                    
                    <!-- Google reCAPTCHA -->
                    <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI" style="margin-bottom: 1.5rem; display: flex; justify-content: center;"></div>
                    
                    <button type="submit" class="admin-submit-btn" id="addUserBtn">
                        <i class="fas fa-check"></i> Enregistrer l'utilisateur
                    </button>
                    
                </form>
            </div>
        </div>
    </div>

    <script>
        // Validation en temps réel exactement comme dans TechCare Admin
        (function() {
            const addNom = document.getElementById('add_nom');
            const addPrenom = document.getElementById('add_prenom');
            const addEmail = document.getElementById('add_email');
            const addTelephone = document.getElementById('add_telephone');
            const addPassword = document.getElementById('add_password');
            const addUserBtn = document.getElementById('addUserBtn');
            
            // Éléments d'erreur/succès
            const nomError = document.getElementById('add_nom_error');
            const prenomError = document.getElementById('add_prenom_error');
            const emailError = document.getElementById('add_email_error');
            const phoneError = document.getElementById('add_phone_error');
            const phoneSuccess = document.getElementById('add_phone_success');
            const passwordError = document.getElementById('add_password_error');
            
            // Éléments force mot de passe
            const addBar1 = document.getElementById('add_bar1');
            const addBar2 = document.getElementById('add_bar2');
            const addBar3 = document.getElementById('add_bar3');
            const addStrengthText = document.getElementById('add_strengthText');
            
            // Validation Nom
            function validateAddNom() {
                const isValid = addNom.value.trim() !== '';
                if (addNom.value === '') {
                    nomError.classList.add('show');
                    addNom.classList.remove('valid');
                    addNom.classList.add('error');
                } else {
                    nomError.classList.remove('show');
                    addNom.classList.remove('error');
                    addNom.classList.add('valid');
                }
                return isValid;
            }
            
            // Validation Prénom
            function validateAddPrenom() {
                const isValid = addPrenom.value.trim() !== '';
                if (addPrenom.value === '') {
                    prenomError.classList.add('show');
                    addPrenom.classList.remove('valid');
                    addPrenom.classList.add('error');
                } else {
                    prenomError.classList.remove('show');
                    addPrenom.classList.remove('error');
                    addPrenom.classList.add('valid');
                }
                return isValid;
            }
            
            // Validation Email
            function validateAddEmail() {
                const email = addEmail.value;
                const isValid = email.includes('@') && email.includes('.') && email.length > 5;
                if (!isValid && email !== '') {
                    emailError.classList.add('show');
                    addEmail.classList.remove('valid');
                    addEmail.classList.add('error');
                } else if (email === '') {
                    emailError.classList.remove('show');
                    addEmail.classList.remove('valid', 'error');
                } else {
                    emailError.classList.remove('show');
                    addEmail.classList.remove('error');
                    addEmail.classList.add('valid');
                }
                return isValid;
            }
            
            // Validation Téléphone (8 chiffres)
            function validateAddPhone() {
                const phone = addTelephone.value;
                const numbersOnly = phone.replace(/\D/g, '');
                const isValid = numbersOnly.length === 8;
                
                if (phone === '') {
                    phoneError.classList.remove('show');
                    phoneSuccess.classList.remove('show');
                    addTelephone.classList.remove('valid', 'error');
                    return false;
                }
                
                if (isValid) {
                    phoneError.classList.remove('show');
                    phoneSuccess.classList.add('show');
                    addTelephone.classList.remove('error');
                    addTelephone.classList.add('valid');
                    return true;
                } else {
                    phoneError.classList.add('show');
                    phoneSuccess.classList.remove('show');
                    addTelephone.classList.remove('valid');
                    addTelephone.classList.add('error');
                    return false;
                }
            }
            
            // Format téléphone (autoriser uniquement les chiffres dynamiquement)
            addTelephone.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value.length > 8) value = value.slice(0, 8);
                this.value = value;
                validateAddPhone();
            });
            
            // Force mot de passe
            function getPasswordStrength(password) {
                const length = password.length;
                if (length === 0) return { level: 0, text: '', color: '' };
                if (length >= 8 && length < 12) return { level: 1, text: 'Faible', color: 'weak' };
                if (length >= 12 && length <= 14) return { level: 2, text: 'Medium', color: 'medium' };
                if (length > 14) return { level: 3, text: 'Fort', color: 'strong' };
                return { level: 0, text: '', color: '' };
            }
            
            function updateAddStrengthBars(password) {
                const strength = getPasswordStrength(password);
                
                addBar1.classList.remove('weak', 'medium', 'strong');
                addBar2.classList.remove('weak', 'medium', 'strong');
                addBar3.classList.remove('weak', 'medium', 'strong');
                
                if (strength.level === 0) {
                    addStrengthText.textContent = '';
                    addStrengthText.className = 'strength-text-admin';
                    return;
                }
                
                addStrengthText.textContent = strength.text;
                addStrengthText.className = `strength-text-admin ${strength.color}`;
                
                if (strength.level >= 1) addBar1.classList.add(strength.color);
                if (strength.level >= 2) addBar2.classList.add(strength.color);
                if (strength.level >= 3) addBar3.classList.add(strength.color);
            }
            
            function validateAddPassword() {
                const password = addPassword.value;
                const isValidLength = password.length >= 8;
                
                updateAddStrengthBars(password);
                
                if (password === '') {
                    passwordError.classList.remove('show');
                    addPassword.classList.remove('valid', 'error');
                    return false;
                }
                
                if (isValidLength) {
                    passwordError.classList.remove('show');
                    addPassword.classList.remove('error');
                    addPassword.classList.add('valid');
                    return true;
                } else {
                    passwordError.classList.add('show');
                    addPassword.classList.remove('valid');
                    addPassword.classList.add('error');
                    return false;
                }
            }
            
            addPassword.addEventListener('input', validateAddPassword);
            
            // Activation du bouton
            function updateAddButtonState() {
                const isNomValid = addNom.value.trim() !== '';
                const isPrenomValid = addPrenom.value.trim() !== '';
                const isEmailValid = addEmail.value.includes('@') && addEmail.value.includes('.') && addEmail.value.length > 5;
                const isPhoneValid = addTelephone.value.replace(/\D/g, '').length === 8;
                const isPasswordValid = addPassword.value.length >= 8;
                
                const allValid = isNomValid && isPrenomValid && isEmailValid && isPhoneValid && isPasswordValid;
                addUserBtn.disabled = !allValid;
            }
            
            addNom.addEventListener('input', () => { validateAddNom(); updateAddButtonState(); });
            addPrenom.addEventListener('input', () => { validateAddPrenom(); updateAddButtonState(); });
            addEmail.addEventListener('input', () => { validateAddEmail(); updateAddButtonState(); });
            addTelephone.addEventListener('input', () => { updateAddButtonState(); });
            addPassword.addEventListener('input', () => { updateAddButtonState(); });
            
            // Initialisation
            validateAddNom();
            validateAddPrenom();
            validateAddEmail();
            validateAddPhone();
            validateAddPassword();
            updateAddButtonState();

            window.validateFormSumbit = function(e) {
                const isNomValid = validateAddNom();
                const isPrenomValid = validateAddPrenom();
                const isEmailValid = validateAddEmail();
                const isPhoneValid = validateAddPhone();
                const isPasswordValid = validateAddPassword();

                const allValid = isNomValid && isPrenomValid && isEmailValid && isPhoneValid && isPasswordValid;

                if (!allValid) {
                    e.preventDefault();
                    return false;
                }
                
                if(!confirm('Êtes-vous sûr de vouloir ajouter cet utilisateur ?')) {
                    e.preventDefault();
                    return false;
                }
                return true;
            };
        })();
    </script>
</body>
</html>
