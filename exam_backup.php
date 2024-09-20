<?php
session_start();
require('db.php');

if (!isset($_SESSION['student_id'])) {
    header('Location: student_login.php');
    exit();
}

$exam_id = $_GET['exam_id'];
$student_id = $_SESSION['student_id'];

$exam = $conn->query("SELECT * FROM exams WHERE id = $exam_id")->fetch_assoc();
$questions = $conn->query("SELECT * FROM questions WHERE exam_id = $exam_id ORDER BY created_at ASC");

if (isset($_POST['submit_exam'])) {
    $total_score = 0;

    foreach ($questions as $question) {
        $question_id = $question['id'];
        $correct_option = $question['correct_option'];
        $selected_option = $_POST['question_' . $question_id];

        if ($selected_option == $correct_option) {
            $total_score += $question['marks'];
        }

        $stmt = $conn->prepare("INSERT INTO student_answers (student_id, question_id, selected_option) VALUES (?, ?, ?)");
        $stmt->bind_param('iii', $student_id, $question_id, $selected_option);
        $stmt->execute();
    }

    $stmt = $conn->prepare("INSERT INTO results (student_id, exam_id, total_score) VALUES (?, ?, ?)");
    $stmt->bind_param('iii', $student_id, $exam_id, $total_score);
    $stmt->execute();

    // Get the auto-incremented id
    $inserted_result_id = $conn->insert_id;

    header("Location: result.php?exam_id=$exam_id&result_id=$inserted_result_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $exam['exam_name']; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5 mb-5 card">
        <div class="p-1 bg-info"><h2 class="card-title text-center"><?php echo $exam['exam_name']; ?></h2></div>
        <form class="m-1" method="POST" action="">
            <?php foreach ($questions as $question) { ?>
                <div class="form-group card p-2">
                    <div class="form-check bg-light">
                        <h4><?php echo $question['question_text']; ?></h4>
                        <?php if (!empty($question['question_image_url'])) { ?>
                            <img src="<?php echo$question['question_image_url']; ?>" alt="Question Image" style="max-width: 100%;">
                        <?php } ?>
                    </div>
                    <div style="height: 1px; width: 100%; background-color: black;"></div>
                    <?php
                    $options = $conn->query("SELECT * FROM options WHERE question_id = " . $question['id']);
                    foreach ($options as $option) { ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="question_<?php echo $question['id']; ?>" value="<?php echo $option['option_id']; ?>" required>
                            <label class="form-check-label">
                                <?php echo $option['option_text']; ?>
                                <?php if (!empty($option['option_image_url'])) { ?>
                                    <br><img src="<?php echo $option['option_image_url']; ?>" alt="Option Image" style="max-width: 100%;">
                                <?php } ?>
                            </label>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <button type="submit" name="submit_exam" class="btn btn-primary">Submit Exam</button>
        </form>
    </div>
</body>
</html>
