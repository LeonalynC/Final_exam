<?php
include_once __DIR__ . '/models.php';

$database = new Database();
$db = $database->getConnection();

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['register'])) {
        // Sanitize user inputs
        $username = htmlspecialchars(trim($_POST['username']));
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password']);
        $role = htmlspecialchars(trim($_POST['role']));

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Invalid email format.";
            exit();
        }

        $user = new User($db);
        if ($user->register($username, $email, $password, $role)) {
            header("Location: login.php");
            exit();
        } else {
            echo "Registration failed. Please try again.";
        }
    }

    if (isset($_POST['login'])) {
        // Sanitize user inputs
        $username = htmlspecialchars(trim($_POST['username']));
        $password = trim($_POST['password']);

        $user = new User($db);
        $result = $user->login($username, $password);
        if ($result) {
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['role'] = $result['role'];
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Invalid credentials. Please try again.";
        }
    }
}
?>