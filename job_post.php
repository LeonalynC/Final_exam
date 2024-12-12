<?php
session_start();
include 'Core/db.php';
include 'Core/models.php';

if ($_SESSION['role'] !== 'HR') {
    header("Location: dashboard.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jobPost = new JobPost($conn);
    if ($jobPost->create($_POST['title'], $_POST['description'], $_POST['location'], $_POST['expiry_date'], $_POST['salary'], $_POST['requirements'], $_SESSION['user_id'])) {
        header("Location: dashboard.php");
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/main.css">
    <title>Create Job Post - FindHire</title>
</head>
<body>
<div class="container">
    <h2>Create Job Post</h2>
    <form method="POST" action="">
        <input type="text" name="title" placeholder="Job Title" required>
        <textarea name="description" placeholder="Job Description" required></textarea>
        <input type="text" name="location" placeholder="Location" required>
        <input type="date" name="expiry_date" required>
        <input type="text" name="salary" placeholder="Salary" required>
        <textarea name="requirements" placeholder="Job Requirements" required></textarea>
        <button type="submit">Post Job</button>
    </form>
</div>
</body>
</html>
