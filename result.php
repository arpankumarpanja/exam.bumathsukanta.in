<?php
session_start();
require('db.php');

$exam_id = $_GET['exam_id'];
$result_id=$_GET['result_id'];
$student_id = $_SESSION['student_id'];

$exam = $conn->query("SELECT * FROM exams WHERE id = $exam_id")->fetch_assoc();
$result = $conn->query("SELECT * FROM results WHERE exam_id = $exam_id AND student_id = $student_id AND id=$result_id")->fetch_assoc();
$questions = $conn->query("SELECT * FROM questions WHERE exam_id = $exam_id ORDER BY created_at ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $exam['exam_name']; ?> - Result</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2><?php echo $exam['exam_name']; ?> - Result</h2>
        <p>Total Marks: <?php echo $result['total_score']; ?> / <?php echo $exam['total_marks']; ?></p>
        <hr>
        <?php foreach ($questions as $question) {
            $question_id = $question['id'];
            $correct_option = $question['correct_option'];
            $student_answer = $conn->query("SELECT * FROM student_answers WHERE question_id = $question_id AND student_id = $student_id")->fetch_assoc();
            $options = $conn->query("SELECT * FROM options WHERE question_id = $question_id");
            ?>
            <div class="form-group">
                <h4><?php echo $question['question_text']; ?></h4>
                <?php foreach ($options as $option) { ?>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" disabled <?php if ($option['id'] == $student_answer['selected_option']) echo 'checked'; ?>>
                        <label class="form-check-label">
                            <?php echo $option['option_text']; ?>
                            <?php if (!empty($option['option_image_url'])) { ?>
                                <br><img src="<?php echo $option['option_image_url']; ?>" alt="Option Image" style="max-width: 100px;">
                            <?php } ?>
                            <?php if ($option['id'] == $correct_option) { ?>
                                <span class="badge badge-success">Correct Answer</span>
                            <?php } ?>
                        </label>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</body>
</html>
