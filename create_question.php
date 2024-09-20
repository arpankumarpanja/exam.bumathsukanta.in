<?php
session_start();
require('db.php');

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$exam_id = $_GET['exam_id'];

if (isset($_POST['create_question'])) {
    $question_text = $_POST['question_text'];
    $question_image_url = $_POST['question_image_url'];
    $marks = $_POST['marks'];
    $correct_option = $_POST['correct_option'];

    $stmt = $conn->prepare("INSERT INTO questions (exam_id, question_text, question_image_url, marks, correct_option) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('issii', $exam_id, $question_text, $question_image_url, $marks, $correct_option);
    $stmt->execute();
    $question_id = $stmt->insert_id;

    foreach ($_POST['options'] as $index => $option_text) {
        $option_id = $_POST['option_ids'][$index];   // newly added
        $option_image_url = $_POST['option_images'][$index];
        $stmt = $conn->prepare("INSERT INTO options (question_id, option_id, option_text, option_image_url) VALUES (?, ?, ?, ?)"); //updated
        $stmt->bind_param('iiss', $question_id, $option_id, $option_text, $option_image_url);   //updated
        $stmt->execute();
    }

    header("Location: manage_questions.php?exam_id=$exam_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Question</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5 mb-5">
        <h2>Create New Question</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="question_text">Question</label>
                <textarea name="question_text" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label for="question_image_url">Question Image URL (optional)</label>
                <input type="url" name="question_image_url" class="form-control">
            </div>
            <div class="form-group">
                <label for="marks">Marks</label>
                <input type="number" name="marks" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="correct_option">Correct Option</label>
                <input type="number" name="correct_option" class="form-control" required>
            </div>
            <div id="options-container">
                <div class="form-group">
                    <label for="options[]">Option 1</label>
                    <input type="text" name="options[]" class="form-control" required>
                    <input type="text" name="option_ids[]" class="form-control" value=1 required>
                    <label for="option_images[]">Image URL (optional)</label>
                    <input type="url" name="option_images[]" class="form-control">
                </div>
            </div>
            <button type="button" id="add-option" class="btn btn-secondary">Add Option</button>
            <button type="submit" name="create_question" class="btn btn-primary">Create Question</button>
        </form>
    </div>

    <script>
        document.getElementById('add-option').addEventListener('click', function () {
            let optionsContainer = document.getElementById('options-container');
            let optionIndex = optionsContainer.children.length + 1;
            let optionHTML = `
                <div class="form-group">
                    <label for="options[]">Option ` + optionIndex + `</label>
                    <input type="text" name="options[]" class="form-control" required>
                    <input type="text" name="option_ids[]" class="form-control" value=` + optionIndex + ` required>
                    <label for="option_images[]">Image URL (optional)</label>
                    <input type="text" name="option_images[]" class="form-control">
                </div>
            `;
            optionsContainer.insertAdjacentHTML('beforeend', optionHTML);
        });
    </script>
</body>
</html>
