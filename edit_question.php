<?php
session_start();
require('db.php');

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$question_id = $_GET['id'];
$exam_id = $_GET['exam_id'];

if (isset($_POST['update_question'])) {
    $question_text = $_POST['question_text'];
    $question_image_url = $_POST['question_image_url'];
    $marks = $_POST['marks'];
    $correct_option = $_POST['correct_option'];

    $stmt = $conn->prepare("UPDATE questions SET question_text = ?, question_image_url = ?, marks = ?, correct_option = ? WHERE id = ?");
    $stmt->bind_param('ssiii', $question_text, $question_image_url, $marks, $correct_option, $question_id);
    $stmt->execute();

    $conn->query("DELETE FROM options WHERE question_id = $question_id");

    foreach ($_POST['options'] as $index => $option_text) {
        $option_id = $_POST['option_ids'][$index];   // newly added
        $option_image_url = $_POST['option_images'][$index];
        $stmt = $conn->prepare("INSERT INTO options (question_id, option_id, option_text, option_image_url) VALUES (?, ?, ?, ?)"); //updated
        $stmt->bind_param('iiss', $question_id, $option_id, $option_text, $option_image_url);    //updated
        $stmt->execute();
    }

    header("Location: manage_questions.php?exam_id=$exam_id");
    exit();
}

$question = $conn->query("SELECT * FROM questions WHERE id = $question_id")->fetch_assoc();
$options = $conn->query("SELECT * FROM options WHERE question_id = $question_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Question</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5 mb-5">
        <h2>Edit Question</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="question_text">Question</label>
                <textarea name="question_text" class="form-control" required><?php echo $question['question_text']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="question_image_url">Question Image URL (optional)</label>
                <input type="url" name="question_image_url" class="form-control" value="<?php echo $question['question_image_url']; ?>">
            </div>
            <div class="form-group">
                <label for="marks">Marks</label>
                <input type="number" name="marks" class="form-control" value="<?php echo $question['marks']; ?>" required>
            </div>
            <div class="form-group">
                <label for="correct_option">Correct Option</label>
                <input type="number" name="correct_option" class="form-control" value="<?php echo $question['correct_option']; ?>" required>
            </div>
            <div id="options-container">
                <?php while($option = $options->fetch_assoc()) { ?>
                    <div class="form-group">
                        <label for="options[]">Option <?php echo $option['option_id']; ?></label>
                        <input type="text" name="options[]" class="form-control" value="<?php echo $option['option_text']; ?>" required>
                        <input type="text" name="option_ids[]" class="form-control" value= "<?php echo $option['option_id']; ?>"required readonly>
                        <label for="option_images[]">Image URL (optional)</label>
                        <input type="url" name="option_images[]" class="form-control" value="<?php echo $option['option_image_url']; ?>">
                    </div>
                <?php } ?>
            </div>
            <button type="button" id="add-option" class="btn btn-secondary">Add Option</button>
            <button type="submit" name="update_question" class="btn btn-primary">Update Question</button>
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
