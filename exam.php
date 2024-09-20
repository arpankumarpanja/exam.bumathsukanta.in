<?php
session_start();
require('db.php');

if (!isset($_SESSION['student_id'])) {
    header('Location: student_login.php');
    exit();
}

$exam_id = $_GET['exam_id'];
$student_id = $_SESSION['student_id'];

// Get the exam details, including duration
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
    <style>
        .sticky-timer {
            position: fixed;
            top: 10px;
            right: 10px;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 5px;
            color: black;
            font-size: 20px;
            font-weight: bold;
            z-index: 1;
        }
    </style>
</head>
<body>

    <!-- Timer Display (Sticky) -->
    <div id="timer" class="sticky-timer"></div>

    <div class="container mt-5 mb-5 card">
        <div class="p-1 bg-info"><h2 class="card-title text-center"><?php echo $exam['exam_name']; ?></h2></div>
        <form class="m-1" method="POST" action="" id="exam-form">
            <?php foreach ($questions as $question) { ?>
                <div class="form-group card p-2">
                    <div class="form-check bg-light">
                        <h4><?php echo $question['question_text']; ?></h4>
                        <?php if (!empty($question['question_image_url'])) { ?>
                            <img src="<?php echo $question['question_image_url']; ?>" alt="Question Image" style="max-width: 100%;">
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

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="startExamModal" tabindex="-1" role="dialog" aria-labelledby="startExamModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="startExamModalLabel">Exam Instructions</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Please read the following instructions carefully before starting the exam:</p>
                    <ul>
                        <li>You have <strong><?php echo $exam['duration']; ?> minutes</strong> to complete the exam.</li>
                        <li>Once the time ends, the exam will be automatically submitted.</li>
                        <li>Ensure a stable internet connection during the exam.</li>
                    </ul>
                    <p>Are you ready to begin?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                    <button type="button" class="btn btn-primary" id="start-exam-btn">Yes, Start the Exam</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Timer Logic
        let duration = <?php echo $exam['duration']; ?> * 60; // Duration in seconds
        let timerElement = document.getElementById('timer');
        let formElement = document.getElementById('exam-form');

        function startTimer() {
            let interval = setInterval(function () {
                let minutes = Math.floor(duration / 60);
                let seconds = duration % 60;

                // Display the timer
                timerElement.textContent = `Time Left: ${minutes}m ${seconds}s`;

                // When the time runs out
                if (duration <= 0) {
                    clearInterval(interval);
                    formElement.submit(); // Auto submit the form when time is up
                }

                duration--;
            }, 1000);
        }

        // Show modal on page load
        $(document).ready(function() {
            $('#startExamModal').modal('show');
        });

        // Start the exam and timer when "Yes" is clicked in the modal
        $('#start-exam-btn').click(function() {
            $('#startExamModal').modal('hide');
            startTimer();
        });
    </script>
</body>
</html>
