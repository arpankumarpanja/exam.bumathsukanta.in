<?php
session_start();
require('db.php');

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$result = $conn->query("SELECT * FROM exams ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Exams</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>All Exams</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Exam Name</th>
                    <th>Total Marks</th>
                    <th>Duration (minutes)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($exam = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $exam['id']; ?></td>
                        <td><?php echo $exam['exam_name']; ?></td>
                        <td><?php echo $exam['total_marks']; ?></td>
                        <td><?php echo $exam['duration']; ?></td>
                        <td>
                            <a href="edit_exam.php?id=<?php echo $exam['id']; ?>" class="btn btn-warning">Edit</a>
                            <a href="delete_exam.php?id=<?php echo $exam['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                            <a href="manage_questions.php?exam_id=<?php echo $exam['id']; ?>" class="btn btn-info">Manage Questions</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
