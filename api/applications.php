<?php
include '../core/db.php';
include '../core/models.php';

$db = new Database();
$conn = $db->getConnection();
$application = new Application($conn);

header("Content-Type: application/json");


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
}
?>