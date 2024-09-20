<?php
session_start();
require('db.php');

if (!isset($_SESSION['student_id'])) {
    header('Location: student_login.php');
    exit();
}

$student_name = $_SESSION['student_name'];
$exams = $conn->query("SELECT * FROM exams ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Welcome, <?php echo $student_name; ?></h2>
        <h4>Your Exams</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Exam Name</th>
                    <th>Total Marks</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($exam = $exams->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $exam['exam_name']; ?></td>
                        <td><?php echo $exam['total_marks']; ?></td>
                        <td><a href="exam.php?exam_id=<?php echo $exam['id']; ?>" class="btn btn-primary">Take Exam</a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <a href="logout.php" class="btn btn-secondary">Logout</a>
    </div>
</body>
</html>
