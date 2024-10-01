<?php
session_start();
include 'DbConnect.php';
$instance = DbConnect::getInstance();
$conn = $instance->getConnection();
$user_id = $_SESSION['user_id'];

$query = "SELECT questions.id, questions.question_text,answers.id AS answer_id, answers.answer_text, answers.is_correct,user_answers.answer_id AS user_answer_id
FROM questions
JOIN answers ON questions.id = answers.question_id
LEFT JOIN user_answers ON user_answers.question_id = questions.id AND user_answers.user_id = $user_id;";
$result = $conn->query($query);
$result = $conn->query($query);
$questions = [];
while ($row = $result->fetch_assoc()) {
    $question_id = $row['id'];

    if (!isset($questions[$question_id])) {
        $questions[$question_id] = [
            'question_text' => $row['question_text'],
            'answers' => [],
            'user_answer_id' => $row['user_answer_id']
        ];
    }
    $questions[$question_id]['answers'][] = [
        'answer_id' => $row['answer_id'],
        'answer_text' => $row['answer_text'],
        'correct_answer' => $row['is_correct']
    ];
}
//echo '<pre>',print_r($questions,1),'</pre>';

$success = 0;


if (isset($_POST['reset'])) {
    session_destroy();
    header('Location: start.php');
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Result</title>

    <style>
        .result-container {


            border-radius: 5px;
            background-color: #f2f2f2;
            padding: 20px;
        }
        .result-container .answer{
            padding-top: 5px;
        }

        .header {
            display: flex;
            justify-content: center;
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
<div class="header">
    <h1>Result</h1>
    </div>

    <?php foreach ($questions as $question_id => $question): ?>
        <div class="result-container">
            <div class="question-block">
                <h3><?php echo $question['question_text']; ?></h3>
                <?php foreach ($question['answers'] as $answer):

                    if ($answer['correct_answer'] == 1) {
                        if ($question['user_answer_id'] == $answer['answer_id']) {
                            $success++;
                        }
                    }
                ?>

                    <br>
                    <span <?php echo ($question['user_answer_id'] == $answer['answer_id']) ? "style='font-weight: bold;'" : '' ?>class="answer">
                        <?php echo $answer['answer_text']; ?>
                    </span>
                    <?php if ($answer['correct_answer'] == 1): ?>
                        <span style="display: inline;">&#10004;</span>
                    <?php endif; ?>




                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <h3> Total Questions: 5</h3>
    <h4> Passed: <?php echo $success; ?></h4>
    <h4>Failed: <?php echo  5 - $success; ?></h4>


    <form action="result.php" method="post">
        <input type="hidden" name="reset">
        <button type="submit"> Start Again</button>
    </form>

</body>

</html>