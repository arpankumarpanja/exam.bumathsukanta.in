<?php
session_start();
require('db.php');

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['create_exam'])) {
    $exam_name = $_POST['exam_name'];
    $total_marks = $_POST['total_marks'];
    $duration = $_POST['duration'];

    $stmt = $conn->prepare("INSERT INTO exams (exam_name, total_marks, duration) VALUES (?, ?, ?)");
    $stmt->bind_param('sii', $exam_name, $total_marks, $duration);
    $stmt->execute();

    header('Location: view_exams.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Exam</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Create New Exam</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="exam_name">Exam Name</label>
                <input type="text" name="exam_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="total_marks">Total Marks</label>
                <input type="number" name="total_marks" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="duration">Duration (in minutes)</label>
                <input type="number" name="duration" class="form-control" required>
            </div>
            <button type="submit" name="create_exam" class="btn btn-primary">Create Exam</button>
        </form>
    </div>
</body>
</html>
