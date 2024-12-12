<?php
include 'core/handleForms.php';

function sendPasswordResetEmail($email) {
   
    echo "Password reset logic is not implemented.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    sendPasswordResetEmail($_POST['email']);
    echo "If the email is registered, a password reset link has been sent.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/main.css">
    <title>Reset Password - FindHire</title>
</head>
<body>
<div class="container">
    <h2>Reset Password</h2>
    <form method="POST" action="">
        <input type="email" name="email" placeholder="Email" required>
        <button type="submit" name="reset_password">Send Reset Link</button>
    </form>
</div>
</body>
</html>