<?php
session_start();
include 'DbConnect.php';
$instance = DbConnect::getInstance();
$conn = $instance->getConnection();
$user_id = $_SESSION['user_id'];



$query = "SELECT questions.id, questions.question_text,answers.id AS answer_id, answers.answer_text,user_answers.answer_id AS user_answer_id
FROM questions
JOIN answers ON questions.id = answers.question_id
LEFT JOIN user_answers ON user_answers.question_id = questions.id AND user_answers.user_id = $user_id;";


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
        'answer_text' => $row['answer_text']
    ];
}
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['answers'])){
        foreach($_POST['answers'] as $ques => $ans ){
            $stmt = $conn->prepare("UPDATE user_answers SET answer_id = ? WHERE user_id = ? AND question_id = ?");
            $stmt->bind_param("iii", $ans, $user_id, $ques);
            $stmt->execute();
        }
        header('Location: result.php');
        exit();
        
    }

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Review Answers</title>
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
    <div class="header">
    <h1>Review Your Answers</h1>
    </div>
    <div class="container">
    <form id="reviewForm" action="review.php" method="POST">
        <?php foreach ($questions as $question_id => $question): ?>
            <div class="question-block">
                <h3><?php echo $question['question_text']; ?></h3>
                <?php foreach ($question['answers'] as $answer): ?>
                    <input type="radio" name="answers[<?php echo $question_id; ?>]" value="<?php echo $answer['answer_id']; ?>"
                        <?php echo ($question['user_answer_id'] == $answer['answer_id']) ? 'checked' : ''; ?>>
                    <?php echo $answer['answer_text']; ?><br>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <button type="submit">Confirm Answers</button>
    </form>
</body>

</html>