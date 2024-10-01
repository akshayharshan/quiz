<?php
session_start();
include 'DbConnect.php';
$instance = DbConnect::getInstance();
$conn = $instance->getConnection();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['name'])) {
        $errors['name'] = "Name is required";
        
    }

    if (empty($errors)) {


        $name = $_POST['name'];
        // Insert user into the database
        $stmt = $conn->prepare("INSERT INTO users (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();

        // Save user ID in session
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['current_question'] = 1; // Start at question 1

        header('Location: question.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Start Quiz</title>
    <style>
        .form-container {
            width: 300px;
            margin: auto;
            text-align: center;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
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
    <form method="POST" action="start.php" enctype="multipart/form-data" autocomplete="off" class="form-container">
        <label for="name">Enter your Name:</label>
        <input type="text" id="name" name="name" required>
        <span style="color:red;"><?php echo $errors['name'] ?? ''; ?></span><br>
        <button type="submit">Start Quiz</button>
    </form>
</body>

</html>

<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>