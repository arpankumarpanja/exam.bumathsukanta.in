<?php
session_start();
require('db.php');

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$exam_id = $_GET['id'];

if (isset($_POST['update_exam'])) {
    $exam_name = $_POST['exam_name'];
    $total_marks = $_POST['total_marks'];
    $duration = $_POST['duration'];

    $stmt = $conn->prepare("UPDATE exams SET exam_name = ?, total_marks = ?, duration = ? WHERE id = ?");
    $stmt->bind_param('siii', $exam_name, $total_marks, $duration, $exam_id);
    $stmt->execute();

    header('Location: view_exams.php');
    exit();
}

$exam = $conn->query("SELECT * FROM exams WHERE id = $exam_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Exam</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Exam</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="exam_name">Exam Name</label>
                <input type="text" name="exam_name" class="form-control" value="<?php echo $exam['exam_name']; ?>" required>
            </div>
            <div class="form-group">
                <label for="total_marks">Total Marks</label>
                <input type="number" name="total_marks" class="form-control" value="<?php echo $exam['total_marks']; ?>" required>
            </div>
            <div class="form-group">
                <label for="duration">Duration (in minutes)</label>
                <input type="number" name="duration" class="form-control" value="<?php echo $exam['duration']; ?>" required>
            </div>
            <button type="submit" name="update_exam" class="btn btn-primary">Update Exam</button>
        </form>
    </div>
</body>
</html>
