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
$jobPost = new JobPost($conn);
$application = new Application($conn);
$messageModel = new Message($conn);

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

$allJobPosts = $jobPost->getAll();
$hr_id = ($role === 'HR') ? $user_id : 1; 

if ($role === 'HR') {
    $applicants = $messageModel->getApplicantsWhoMessaged($user_id);
    $selected_applicant_id = isset($_GET['applicant_id']) ? $_GET['applicant_id'] : null;
    $messages = $selected_applicant_id ? $messageModel->getMessagesBetweenUsers($user_id, $selected_applicant_id) : [];
} else {
    $messages = $messageModel->getMessagesBetweenUsers($user_id, $hr_id);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_application_status'])) {
        $application_id = $_POST['application_id'];
        $status = $_POST['status'];
        $application->updateStatus($application_id, $status);
    }
    if (isset($_POST['send_message'])) {
        $receiver_id = ($role === 'HR') ? $_POST['receiver_id'] : $hr_id;
        $content = $_POST['content'];
        $messageModel->send($user_id, $receiver_id, $content);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/main.css">
    <title>FindHire - Dashboard</title>
</head>
<body>
<header>
    <div class="container">
    <h1 style="color: VIOLET; -webkit-text-stroke: 0.1px white; font-weight: bold; font-size: 100px; text-shadow: -2px -2px 0 black, 2px -2px 0 black, -2px 2px 0 black, 2px 2px 0 black; ">FindHire 💻</h1>

        <nav>
            <ul>
            <li><a href="dashboard.php" style="color: violet;">Dashboard</a></li>
            <li><a href="logout.php" style="color: pink;">Logout</a></li>
            <style>
@import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Lobster&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@300;400&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Pacifico&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Raleway:wght@300;600&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600&display=swap');
@import url('https://fonts.googleapis.com/css2?family=Indie+Flower&display=swap');

body {
    font-family: 'Quicksand', sans-serif;
    background: linear-gradient(135deg, #fdf6fc, #ffe0f7, #f0e4ff);
    margin: 0;
    padding: 0;
    color: #333;
    line-height: 1.8;
    overflow-x: hidden;
    animation: fadeIn 2s ease-in-out;
}

.container {
    max-width: 1200px;
    width: 90%;
    margin: 50px auto;
    padding: 40px;
    background: rgba(255, 236, 250, 0.95);
    border-radius: 25px;
    box-shadow: 0 10px 40px rgba(178, 112, 219, 0.4);
    animation: zoomIn 1s ease-in-out;
}

header {
    background: linear-gradient(90deg, #d19ee5, #f49ac2, #e4c5f9);
    color: white;
    padding: 30px 0;
    border-bottom: 6px solid #c978d9;
    display: flex;
    justify-content: space-between;
    align-items: center;
    text-shadow: 3px 3px 5px rgba(0, 0, 0, 0.3);
    font-family: 'Lobster', cursive;
    animation: slideInDown 1s ease-in-out;
}

header img {
    height: 70px;
    animation: rotateIn 4s infinite linear;
}

header a {
    color: white;
    text-decoration: none;
    text-transform: uppercase;
    font-size: 20px;
    margin-left: 25px;
    transition: color 0.3s ease-in-out, transform 0.3s ease-in-out;
    font-family: 'Roboto Mono', monospace;
}

header a:hover {
    color: #fbd9f4;
    text-shadow: 0 0 12px rgba(255, 255, 255, 0.8);
    transform: scale(1.1);
}

nav ul {
    margin: 0;
    padding: 0;
    list-style: none;
    display: flex;
}

nav ul li {
    margin-left: 20px;
    font-family: 'Montserrat', sans-serif;
}

form {
    background: white;
    padding: 35px;
    margin: 35px 0;
    box-shadow: 0 10px 35px rgba(178, 112, 219, 0.4);
    border-radius: 15px;
    border: 3px solid #d19ee5;
    font-family: 'Raleway', sans-serif;
    animation: fadeInUp 1s ease-in-out;
}

form input, form select, form textarea {
    display: block;
    width: calc(100% - 24px);
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 8px;
    border: 2px solid #ddd;
    font-family: 'Open Sans', sans-serif;
    font-size: 16px;
    box-shadow: inset 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: border-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}

form input:focus, form select:focus, form textarea:focus {
    border-color: #c978d9;
    box-shadow: 0 0 15px rgba(178, 112, 219, 0.5);
    outline: none;
}

form button {
    background: linear-gradient(90deg, #d19ee5, #f49ac2);
    color: white;
    border: none;
    padding: 15px 25px;
    cursor: pointer;
    border-radius: 8px;
    font-size: 18px;
    transition: background 0.3s ease-in-out, transform 0.3s ease-in-out;
    font-family: 'Pacifico', cursive;
}

form button:hover {
    background: linear-gradient(90deg, #c978d9, #f6a8cf);
    transform: translateY(-3px);
}

.card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    margin: 30px 0;
    overflow: hidden;
    transition: transform 0.4s ease-in-out;
    animation: fadeInUp 1.5s ease-in-out;
}

.card:hover {
    transform: translateY(-15px);
}

.card-header, .card-footer {
    background: #d19ee5;
    color: white;
    padding: 15px;
    text-align: center;
    font-family: 'Playfair Display', serif;
}

.card-body {
    padding: 25px;
    font-family: 'Nunito', sans-serif;
}

footer {
    background: linear-gradient(90deg, #d19ee5, #f49ac2);
    color: white;
    text-align: center;
    padding: 20px 0;
    position: fixed;
    bottom: 0;
    width: 100%;
    font-size: 16px;
    font-family: 'Indie Flower', cursive;
    box-shadow: 0 -3px 15px rgba(0, 0, 0, 0.15);
}

h1, h2 {
    color: #c978d9;
    font-weight: 600;
    margin: 20px 0;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.15);
}

h1 {
    font-size: 2.8em;
    letter-spacing: 2px;
    font-family: 'Lobster', cursive;
}

h2 {
    font-size: 2.2em;
    font-family: 'Lobster', cursive;
}

a {
    color: #c978d9;
    text-decoration: none;
    transition: color 0.3s ease-in-out;
}

a:hover {
    color: #f6a8cf;
    text-decoration: underline;
}

button {
    font-family: 'Quicksand', sans-serif;
}

ul {
    padding-left: 30px;
}

ul li {
    margin-bottom: 10px;
    position: relative;
    padding-left: 25px;
}

ul li:before {
    content: '•';
    color: #c978d9;
    position: absolute;
    left: 0;
    top: 0;
}

blockquote {
    background: rgba(209, 158, 229, 0.15);
    border-left: 8px solid #c978d9;
    margin: 30px 0;
    padding: 20px 30px;
    font-style: italic;
    color: #555;
    font-family: 'Raleway', sans-serif;
}

code {
    background: #f9ebfd;
    color: #94429d;
    padding: 4px 8px;
    border-radius: 5px;
    font-family: 'Courier New', Courier, monospace;
    display: inline-block;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin: 30px 0;
    font-size: 16px;
    font-family: 'Roboto Mono', monospace;
}

table th, table td {
    border: 1px solid #ddd;
    padding: 15px;
    text-align: left;
}

table th {
    background: #d19ee5;
    color: white;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
}

button.primary {
    background: linear-gradient(90deg, #d19ee5, #f49ac2);
    border: none;
    color: white;
    padding: 15px 30px;
    text-transform: uppercase;
    cursor: pointer;
    transition: background 0.3s, transform 0.3s;
    margin: 10px 0;
    font-family: 'Pacifico', cursive;
}

button.primary:hover {
    background: linear-gradient(90deg, #c978d9, #f6a8cf);
    transform: translateY(-3px);
}

.alert {
    background: #f8d7da;
    color: #721c24;
    padding: 25px;
    margin-bottom: 30px;
    border: 1px solid #f5c6cb;
    border-radius: 8px;
    position: relative;
    font-family: 'Open Sans', sans-serif;
}

.alert.success {
    background: #d4edda;
    color: #155724;
    border-color: #c3e6cb;
}

.alert.warning {
    background: #fff3cd;
    color: #856404;
    border-color: #ffeeba;
}

.alert.info {
    background: #d1ecf1;
    color: #0c5460;
    border-color: #bee5eb;
}

.alert .close {
    position: absolute;
    top: 15px;
    right: 15px;
    color: inherit;
    text-decoration: none;
    font-size: 24px;
    cursor: pointer;
    font-weight: bold;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideInDown {
    from { transform: translateY(-100%); }
    to { transform: translateY(0); }
}

@keyframes zoomIn {
    from { transform: scale(0); }
    to { transform: scale(1); }
}

@keyframes fadeInUp {
    from { transform: translateY(50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes rotateIn {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

</style>
            </ul>
        </nav>
    </div>
</header>
<div class="container">
<h1 style="color: pink; -webkit-text-stroke: 0.1px white; font-weight: bold; font-size: 50px; text-shadow: -2px -2px 0 black, 2px -2px 0 black, -2px 2px 0 black, 2px 2px 0 black; ">Welcome to FindHire!</h1>


    <?php if ($role == 'HR'): ?>
        <h2 style="color: pink; -webkit-text-stroke: 0.1px white; font-weight: bold; font-size: 50px; text-shadow: -2px -2px 0 black, 2px -2px 0 black, -2px 2px 0 black, 2px 2px 0 black; ">HR Dashboard 👩🏻‍💻</h2>
        <h3>Create Job Post</h3>
        <form method="POST" action="job_post.php">
            <input type="text" name="title" placeholder="Job Title" required>
            <textarea name="description" placeholder="Job Description" required></textarea>
            <input type="text" name="location" placeholder="Location" required>
            <input type="date" name="expiry_date" required>
            <input type="text" name="salary" placeholder="Salary" required>
            <textarea name="requirements" placeholder="Job Requirements" required></textarea>
            <button type="submit">Post Job</button>
        </form>

        <h3>Job Applications</h3>
        <?php foreach ($allJobPosts as $post): ?>
            <div>
                <strong><?php echo htmlspecialchars($post['title']); ?></strong>
                <p><?php echo htmlspecialchars($post['description']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($post['location']); ?></p>
                <p><strong>Salary:</strong> <?php echo htmlspecialchars($post['salary']); ?></p>
                <p><strong>Requirements:</strong> <?php echo htmlspecialchars($post['requirements']); ?></p>
                <p>Applications:</p>
                <?php
                $applications = $application->getApplicationsByJob($post['id']);
                foreach ($applications as $app): ?>
                    <div>
                        <p>Applicant: <?php echo htmlspecialchars($app['username']); ?></p>
                        <p>Status: <?php echo htmlspecialchars($app['status']); ?></p>
                        <p>Message: <?php echo htmlspecialchars($app['message']); ?></p>
                        <?php if (!empty($app['resume'])): ?>
                            <p>Resume: <a href="uploads/<?php echo htmlspecialchars($app['resume']); ?>" target="_blank">View Resume</a></p>
                        <?php endif; ?>
                        <form method="POST" action="">
                            <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                            <select name="status">
                                <option value="accepted">Accept</option>
                                <option value="hired">Hire</option>
                                <option value="rejected">Reject</option>
                            </select>
                            <button type="submit" name="update_application_status">Update Status</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <h3>Messages</h3>
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

    <?php if ($selected_applicant_id): ?>
        <h3>Messages with <?php echo htmlspecialchars($applicants[array_search($selected_applicant_id, array_column($applicants, 'id'))]['username']); ?>:</h3>
        <div class="messages-container">
            <?php if (!empty($messages)): ?>
                <?php foreach ($messages as $msg): ?>
                    <div>
                        <strong><?php echo htmlspecialchars($msg['username']); ?>:</strong>
                        <p><?php echo htmlspecialchars($msg['content']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No messages yet.</p>
            <?php endif; ?>
        </div>
        
        <form method="POST" action="">
            <input type="hidden" name="receiver_id" value="<?php echo $selected_applicant_id; ?>">
            <textarea name="content" placeholder="Your reply" required></textarea>
            <button type="submit" name="send_message">Reply</button>
        </form>
    <?php else: ?>
        <p>Select an applicant to view messages.</p>
    <?php endif; ?>
<?php else: ?>
    <p>No applicants have messaged you yet.</p>
<?php endif; ?>


    <?php else: ?>
        <h2 style="color: pink; -webkit-text-stroke: 0.1px white; font-weight: bold; font-size: 50px; text-shadow: -2px -2px 0 black, 2px -2px 0 black, -2px 2px 0 black, 2px 2px 0 black;">Applicant Dashboard 🤵🏻</h2>

        <h3>Available Jobs</h3>
        <?php foreach ($allJobPosts as $post): ?>
            <div>
                <strong><?php echo htmlspecialchars($post['title']); ?></strong>
                <p><?php echo htmlspecialchars($post['description']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($post['location']); ?></p>
                <p><strong>Salary:</strong> <?php echo htmlspecialchars($post['salary']); ?></p>
                <p><strong>Requirements:</strong> <?php echo htmlspecialchars($post['requirements']); ?></p>
                <form method="POST" enctype="multipart/form-data" action="apply.php">
                    <input type="hidden" name="job_id" value="<?php echo $post['id']; ?>">
                    <textarea name="message" placeholder="Why are you the best candidate?" required></textarea>
                    <input type="file" name="resume" accept=".pdf" required>
                    <button type="submit" name="apply_job">Apply</button>
                </form>
            </div>
        <?php endforeach; ?>

        <h3>Messages with HR</h3>
        <?php foreach ($messages as $msg): ?>
            <div>
                <strong><?php echo htmlspecialchars($msg['username']); ?>:</strong>
                <p><?php echo htmlspecialchars($msg['content']); ?></p>
            </div>
        <?php endforeach; ?>
        <form method="POST" action="">
            <textarea name="content" placeholder="Your message" required></textarea>
            <button type="submit" name="send_message">Send Message</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>