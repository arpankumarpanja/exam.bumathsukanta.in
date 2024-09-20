<?php
session_start();
require('db.php');

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$exam_id = $_GET['exam_id'];

$questions = $conn->query("SELECT * FROM questions WHERE exam_id = $exam_id ORDER BY created_at DESC");

$exam = $conn->query("SELECT exam_name FROM exams WHERE id = $exam_id")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions for <?php echo $exam['exam_name']; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Manage Questions for <?php echo $exam['exam_name']; ?></h2>
        <a href="create_question.php?exam_id=<?php echo $exam_id; ?>" class="btn btn-primary">Add New Question</a>
        <hr>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Question</th>
                    <th>Correct Option</th>
                    <th>Marks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($question = $questions->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $question['id']; ?></td>
                        <td><?php echo $question['question_text']; ?></td>
                        <td><?php echo $question['correct_option']; ?></td>
                        <td><?php echo $question['marks']; ?></td>
                        <td>
                            <a href="edit_question.php?id=<?php echo $question['id']; ?>&exam_id=<?php echo $exam_id; ?>" class="btn btn-warning">Edit</a>
                            <a href="delete_question.php?id=<?php echo $question['id']; ?>&exam_id=<?php echo $exam_id; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
