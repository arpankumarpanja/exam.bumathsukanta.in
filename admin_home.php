<?php
session_start();
require('db.php');

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Welcome to Admin Dashboard</h2>
        <a href="logout.php" class="btn btn-danger">Logout</a>
        <hr>
        <h3>Manage Exams</h3>
        <a href="create_exam.php" class="btn btn-primary">Create New Exam</a>
        <a href="view_exams.php" class="btn btn-info">View All Exams</a>
    </div>
</body>
</html>
