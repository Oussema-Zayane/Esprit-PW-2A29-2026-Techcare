<?php
// Simple redirector to the controller
$token = $_GET['token'] ?? '';
header("Location: ../../Controller/UserController.php?action=verify&token=" . urlencode($token));
exit();
