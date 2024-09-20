<?php
session_start();
require('db.php');

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$exam_id = $_GET['id'];

$conn->query("DELETE FROM exams WHERE id = $exam_id");

header('Location: view_exams.php');
exit();
