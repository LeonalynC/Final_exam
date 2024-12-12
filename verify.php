<?php
include_once __DIR__ . '/models.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    if ($user->verifyEmail($token)) {
        echo "Your email has been verified! You can now log in.";
    
    } else {
        echo "Invalid or expired token.";
    }
} else {
    echo "No token provided.";
}
?>