<?php
session_start();
require('db.php');

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$question_id = $_GET['id'];
$exam_id = $_GET['exam_id'];

$conn->query("DELETE FROM questions WHERE id = $question_id");

header("Location: manage_questions.php?exam_id=$exam_id");
exit();
