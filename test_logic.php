<?php
require_once "Controller/UserController.php";
require_once "Model/User.php";

// Simulate registration
$_POST['nom'] = "Test";
$_POST['prenom'] = "User";
$_POST['email'] = "test" . time() . "@example.com";
$_POST['telephone'] = "12345678";
$_POST['password'] = "password123";
$_POST['g-recaptcha-response'] = "mock_token"; // We'll need to mock verifyRecaptcha or bypass it

// Since I can't easily bypass session_start and header() in a CLI script that requires the controller,
// I'll just check the DB state after a manual call to User::register.

$email = "test" . time() . "@example.com";
User::register("Test", "User", $email, "12345678", password_hash("password123", PASSWORD_DEFAULT), 'user');

$user = User::findByEmail($email);
echo "New user verified status: " . $user['is_verified'] . " (Should be 0)\n";

// Test login check
if ($user['is_verified'] == 0) {
    echo "Login would be blocked: SUCCESS\n";
} else {
    echo "Login would NOT be blocked: FAILURE\n";
}
?>
