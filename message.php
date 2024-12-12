<?php
session_start();
include 'core/db.php';
include 'core/models.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];


$hr_id = 1;


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    $sender_id = $user_id;
    $receiver_id = ($role === 'applicant') ? $hr_id : $_POST['receiver_id'];
    $content = trim($_POST['content']);

    $message = new Message($conn);
    if (!empty($content) && $message->send($sender_id, $receiver_id, $content)) {
        header("Location: message.php");
        exit();
    }
}


$messages = [];
if ($role === 'applicant') {
    $messages = (new Message($conn))->getMessagesBetweenUsers($user_id, $hr_id);
} else if ($role === 'HR') {
    $selected_applicant_id = $_GET['applicant_id'] ?? null;

    
    if ($selected_applicant_id) {
        $messages = (new Message($conn))->getMessagesBetweenUsers($user_id, $selected_applicant_id);
    }

    $applicants = (new Message($conn))->getApplicantsWhoMessaged($user_id);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/main.css">
    <title>Messages - FindHire</title>
</head>
<body>
<div class="container">
    <h2>Messages</h2>
    
   
    <?php if ($role === 'HR'): ?>
        <?php if (!empty($applicants)): ?>
            <form method="GET" action="">
                <label for="applicant_id">Select Applicant:</label>
                <select name="applicant_id" id="applicant_id" onchange="this.form.submit()">
                    <option value="">-- Select an Applicant --</option>
                    <?php foreach ($applicants as $applicant): ?>
                        <option value="<?php echo $applicant['id']; ?>" 
                            <?php echo ($selected_applicant_id == $applicant['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($applicant['username']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        <?php else: ?>
            <p>No applicants have sent messages yet.</p>
        <?php endif; ?>
        
        <?php if (!empty($selected_applicant_id)): ?>
            <form method="POST" action="">
                <input type="hidden" name="receiver_id" value="<?php echo $selected_applicant_id; ?>">
                <textarea name="content" placeholder="Your message" required></textarea>
                <button type="submit" name="send_message">Send Message</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>

 
    <?php if ($role === 'applicant'): ?>
        <form method="POST" action="">
            <input type="hidden" name="receiver_id" value="<?php echo $hr_id; ?>">
            <textarea name="content" placeholder="Your message" required></textarea>
            <button type="submit" name="send_message">Send Message</button>
        </form>
    <?php endif; ?>


    <h3>Messages:</h3>
    <?php if (!empty($messages)): ?>
        <?php foreach ($messages as $msg): ?>
            <div>
                <strong><?php echo htmlspecialchars($msg['username']); ?>:</strong>
                <?php echo htmlspecialchars($msg['content']); ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No messages yet.</p>
    <?php endif; ?>
</div>
</body>
</html>
