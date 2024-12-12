<?php
include 'Core/handleForms.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/main.css">
    <title>Login - FindHire</title>
</head>
<body>
<div class="container">
    <h2>Login</h2>
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>
</body>
</html>