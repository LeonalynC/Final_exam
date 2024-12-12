<?php
include '../core/db.php';
include '../core/models.php';

$db = new Database();
$conn = $db->getConnection();
$jobPost = new JobPost($conn);

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    echo json_encode($jobPost->getAll());
}
?>