<?php
session_start();
include 'Core/db.php';
include 'Core/models.php';

if ($_SESSION['role'] !== 'applicant') {
    header("Location: dashboard.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $applicant_id = $_SESSION['user_id'];
    $job_id = $_POST['job_id'];
    $message = htmlspecialchars(trim($_POST['message']));
    $resume = $_FILES['resume']['name'];
    $resume_tmp = $_FILES['resume']['tmp_name'];
    $resume_path = "uploads/" . basename($resume);

    
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }

    
    if (move_uploaded_file($resume_tmp, $resume_path)) {
        $application = new Application($conn);
        if ($application->apply($applicant_id, $job_id, $resume, $message)) {
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Failed to submit application.";
        }
    } else {
        echo "Failed to upload resume.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/main.css">
    <title>Apply for Job - FindHire</title>
</head>
<body>
<div class="container">
    <h2>Apply for Job</h2>
    <form method="POST" enctype="multipart/form-data" action="">
        <input type="hidden" name="job_id" value="<?php echo htmlspecialchars($_GET['job_id']); ?>">
        <textarea name="message" placeholder="Why are you the best candidate?" required></textarea>
        <input type="file" name="resume" accept=".pdf" required>
        <button type="submit">Apply</button>
    </form>
</div>
</body>
</html>