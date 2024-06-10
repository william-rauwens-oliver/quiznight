// index.php
<?php
include('config.php');

$stmt = $conn->prepare("SELECT * FROM quizzes");
$stmt->execute();
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quiz List</title>
</head>
<body>
    <h1>Available Quizzes</h1>
    <ul>
        <?php foreach ($quizzes as $quiz): ?>
            <li><?php echo htmlspecialchars($quiz['title']); ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
