<?php
include '../core/db.php';
include '../core/models.php';

$db = new Database();
$conn = $db->getConnection();
$message = new Message($conn);

header("Content-Type: application/json");


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
}
?>