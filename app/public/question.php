<?php

session_start();
include 'DbConnect.php';
$instance = DbConnect::getInstance();
$conn = $instance->getConnection();

$user_id = $_SESSION['user_id'];
$current_question = $_SESSION['current_question'];

// Get the current question
$question = $conn->query("SELECT * FROM questions WHERE id = $current_question")->fetch_assoc();
$answers = $conn->query("SELECT * FROM answers WHERE question_id = $current_question");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answer_id = $_POST['answer'];

    // Save the user's answer
    $stmt = $conn->prepare("INSERT INTO user_answers (user_id, question_id, answer_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $current_question, $answer_id);
    $stmt->execute();

    // Move to the next question or review
    $_SESSION['current_question']++;
    if ($_SESSION['current_question'] > 5) {
        echo json_encode(['status' => 'done', 'redirect' => 'review.php']);
    } else {

        $nextQuestion = $conn->query("SELECT * FROM questions WHERE id =" .  $_SESSION['current_question'])->fetch_assoc();
        $answers = $conn->query("SELECT * FROM answers WHERE question_id =" . $_SESSION['current_question']);
        $questionHtml = '<h2>' . $nextQuestion['question_text'] . '</h2>';
        $answersHtml = '';
        while ($row  = $answers->fetch_assoc()) {
            $answersHtml .= '<input type="radio" name="answer" value="' . $row['id'] . '" required>';
            $answersHtml .= '<label>' . $row['answer_text'] . '</label>';
        }
        echo json_encode([
            'status' => 'next',
            'question' => $questionHtml,
            'answers' => $answersHtml
        ]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Question <?php echo $current_question; ?></title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .container {
            display: flex;
            justify-content: center;
            align-items: center; 
            border-radius: 5px;
            background-color: #f2f2f2;
            padding: 20px;
        }
        .header{
            display: flex;
            justify-content: center;
        }
        
        form {
            width: 300px;           
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #fff;
            text-align: center;     
        }


        button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
        }
    </style>
</head>

<body>

<div class="header" id="question-container">
        <h2><?php echo $question['question_text']; ?></h2>
    </div>
    <div class="container">

        <form name="question" id="questionForm" action="question.php">
            <div id="answer-container">
                <?php while ($row = $answers->fetch_assoc()): ?>
                    <input id="answer" type="radio" name="answer" value="<?php echo $row['id']; ?>">
                    <?php echo $row['answer_text']; ?>
                <?php endwhile; ?>
            </div>
            <button type="submit"
                id="submitButton">submit
            </button>
        </form>
    </div>

    <!-- jQuery Script -->
    <script>
        $(document).ready(function() {
            $("#submitButton").click(function(event) {
                event.preventDefault(); // Prevent default form submission
                let form = $("#questionForm");
                let url = form.attr('action');
                var x = document.forms["question"]["answer"].value;
                if (x == "") {
                    alert("please select one");
                } else {
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: form.serialize(), // Serialize form data
                        success: function(response) {
                            let resp = JSON.parse(response);
                            console.log(resp);
                            if (resp.status == 'next') {
                                $('#question-container').html(resp.question);
                                $('#answer-container').html(resp.answers);


                            } else if (resp.status == 'done') {
                                window.location.href = resp.redirect;
                            } else {
                                alert("error ");
                            }

                        },
                        error: function(data) {
                            alert("Error occurred while submitting the form");
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>