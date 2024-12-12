<?php
include 'Core/db.php';
include 'Core/models.php';

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $db = new Database();
    $conn = $db->getConnection();
    $user = new User($conn);

    if ($user->verifyEmail($user_id)) {
        echo "Email verified successfully!";
       
    } else {
        echo "Verification failed. Please check the link or contact support.";
   
    }
} else {
    echo "Invalid verification link.";

}
?>