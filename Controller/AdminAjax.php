<?php
session_start();
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../Model/User.php";

// Vérifier que c'est un admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    http_response_code(403);
    exit('Accès non autorisé');
}

$action = $_GET['action'] ?? '';

if ($action == 'dashboard_stats') {
    $conn = Config::getConnexion();
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM personne");
    $totalUsers = $stmt->fetch()['total'];
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM produits");
    $totalProducts = $stmt->fetch()['total'] ?? 0;
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM commandes");
    $totalOrders = $stmt->fetch()['total'] ?? 0;
    
    header('Content-Type: application/json');
    echo json_encode([
        'totalUsers' => $totalUsers,
        'totalProducts' => $totalProducts,
        'totalOrders' => $totalOrders
    ]);
}

// Statistiques des rôles pour le pie chart
if ($action == 'role_stats') {
    $conn = Config::getConnexion();
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM personne WHERE role = 'admin'");
    $adminCount = $stmt->fetch()['total'];
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM personne WHERE role = 'user'");
    $userCount = $stmt->fetch()['total'];
    
    header('Content-Type: application/json');
    echo json_encode([
        'adminCount' => (int)$adminCount,
        'userCount' => (int)$userCount
    ]);
}

if ($action == 'get_users') {
    $users = User::getAllUsers();
    ?>
    <style>
        .admin-input-group {
            position: relative;
            margin-bottom: 0.5rem;
        }
        .admin-field-error {
            font-size: 0.7rem;
            color: #e74c3c;
            margin-top: 0.25rem;
            display: none;
        }
        .admin-field-error.show {
            display: block;
        }
        .admin-field-success {
            font-size: 0.7rem;
            color: #00A86B;
            margin-top: 0.25rem;
            display: none;
        }
        .admin-field-success.show {
            display: block;
        }
        .admin-input-group input.error, 
        .admin-input-group select.error {
            border-color: #e74c3c !important;
        }
        .admin-input-group input.valid, 
        .admin-input-group select.valid {
            border-color: #00A86B !important;
        }
        .password-strength-admin {
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
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
        .strength-bar-admin.weak { background: #e74c3c; }
        .strength-bar-admin.medium { background: #f39c12; }
        .strength-bar-admin.strong { background: #00A86B; }
        .strength-text-admin { font-size: 0.7rem; font-weight: 600; min-width: 70px; text-align: right; }
        .strength-text-admin.weak { color: #e74c3c; }
        .strength-text-admin.medium { color: #f39c12; }
        .strength-text-admin.strong { color: #00A86B; }
        .admin-submit-btn {
            background: linear-gradient(135deg, #00A86B, #007a4e);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.7rem 1.5rem;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        .admin-submit-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .admin-submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }
    </style>

    <!-- Formulaire d'ajout d'utilisateur -->
    <div class="admin-add-form" style="margin-bottom: 2rem; padding: 1.5rem; background: var(--surface2); border-radius: var(--r-md);">
        <h4 style="margin-bottom: 1rem; color: var(--green);"><i class="fas fa-user-plus"></i> Ajouter un utilisateur</h4>
        <form id="addUserForm" onsubmit="event.preventDefault(); submitAddUser();">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div class="admin-input-group">
                    <input type="text" name="nom" id="add_nom" placeholder="Nom" required style="padding: 0.7rem; border-radius: var(--r-sm); border: 1px solid var(--border); background: var(--surface); color: var(--txt); width: 100%;">
                    <div id="add_nom_error" class="admin-field-error">❌ Le nom est requis</div>
                </div>
                <div class="admin-input-group">
                    <input type="text" name="prenom" id="add_prenom" placeholder="Prénom" required style="padding: 0.7rem; border-radius: var(--r-sm); border: 1px solid var(--border); background: var(--surface); color: var(--txt); width: 100%;">
                    <div id="add_prenom_error" class="admin-field-error">❌ Le prénom est requis</div>
                </div>
                <div class="admin-input-group">
                    <input type="email" name="email" id="add_email" placeholder="Email" required style="padding: 0.7rem; border-radius: var(--r-sm); border: 1px solid var(--border); background: var(--surface); color: var(--txt); width: 100%;">
                    <div id="add_email_error" class="admin-field-error">❌ Email invalide (ex: nom@domaine.com)</div>
                </div>
                <div class="admin-input-group">
                    <input type="tel" name="telephone" id="add_telephone" placeholder="Téléphone (8 chiffres)" maxlength="10" required style="padding: 0.7rem; border-radius: var(--r-sm); border: 1px solid var(--border); background: var(--surface); color: var(--txt); width: 100%;">
                    <div id="add_phone_error" class="admin-field-error">❌ Le téléphone doit contenir exactement 8 chiffres</div>
                    <div id="add_phone_success" class="admin-field-success">✅ Numéro valide</div>
                </div>
                <div class="admin-input-group">
                    <input type="password" name="password" id="add_password" placeholder="Mot de passe (min 8 caractères)" required style="padding: 0.7rem; border-radius: var(--r-sm); border: 1px solid var(--border); background: var(--surface); color: var(--txt); width: 100%;">
                    <div class="password-strength-admin">
                        <div class="strength-bars-admin">
                            <div class="strength-bar-admin" id="add_bar1"></div>
                            <div class="strength-bar-admin" id="add_bar2"></div>
                            <div class="strength-bar-admin" id="add_bar3"></div>
                        </div>
                        <span class="strength-text-admin" id="add_strengthText"></span>
                    </div>
                    <div id="add_password_error" class="admin-field-error">❌ Le mot de passe doit contenir au moins 8 caractères</div>
                    <div id="add_password_success" class="admin-field-success">✅ Mot de passe valide</div>
                </div>
                <div class="admin-input-group">
                    <select name="role" id="add_role" style="padding: 0.7rem; border-radius: var(--r-sm); border: 1px solid var(--border); background: var(--surface); color: var(--txt); width: 100%;">
                        <option value="user">Utilisateur</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="admin-submit-btn" id="addUserBtn">
                <i class="fas fa-plus"></i> Ajouter l'utilisateur
            </button>
        </form>
    </div>
    
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr><th>ID</th><th>Nom</th><th>Prénom</th><th>Email</th><th>Téléphone</th><th>Rôle</th><th>Actions</th></tr>
            </thead>
            <tbody id="users-table-body">
                <?php foreach($users as $user): ?>
                <tr id="user-row-<?= $user['id'] ?>">
                    <td><?= $user['id'] ?></td>
                    <td><input type="text" id="nom_<?= $user['id'] ?>" value="<?= htmlspecialchars($user['nom']) ?>" class="edit-input"></td>
                    <td><input type="text" id="prenom_<?= $user['id'] ?>" value="<?= htmlspecialchars($user['prenom']) ?>" class="edit-input"></td>
                    <td><input type="email" id="email_<?= $user['id'] ?>" value="<?= htmlspecialchars($user['email']) ?>" class="edit-input"></td>
                    <td><input type="text" id="telephone_<?= $user['id'] ?>" value="<?= htmlspecialchars($user['telephone']) ?>" class="edit-input"></td>
                    <td>
                        <select id="role_<?= $user['id'] ?>" class="edit-select">
                            <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                            <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                     </td>
                    <td class="tbl-actions">
                        <button class="btn-edit" onclick="updateUser(<?= $user['id'] ?>)"><i class="fas fa-edit"></i> Modifier</button>
                        <button class="btn-del" onclick="deleteUser(<?= $user['id'] ?>)"><i class="fas fa-trash"></i> Supprimer</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Validation en temps réel pour le formulaire d'ajout admin
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
            const passwordSuccess = document.getElementById('add_password_success');
            
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
            
            // Format téléphone
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
                    passwordSuccess.classList.remove('show');
                    addPassword.classList.remove('valid', 'error');
                    return false;
                }
                
                if (isValidLength) {
                    passwordError.classList.remove('show');
                    passwordSuccess.classList.add('show');
                    addPassword.classList.remove('error');
                    addPassword.classList.add('valid');
                    return true;
                } else {
                    passwordError.classList.add('show');
                    passwordSuccess.classList.remove('show');
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
        })();
    </script>
    <?php
}
?>