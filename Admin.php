// admin.php
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO quizzes (title, description) VALUES (:title, :description)");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description);
    $stmt->execute();

    header('Location: admin.php');
    exit();
}

$stmt = $conn->prepare("SELECT * FROM quizzes");
$stmt->execute();
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
</head>
<body>
    <h1>Create a New Quiz</h1>
    <form method="POST">
        <input type="text" name="title" placeholder="Quiz Title" required>
        <textarea name="description" placeholder="Quiz Description" required></textarea>
        <button type="submit">Create Quiz</button>
    </form>

    <h1>Existing Quizzes</h1>
    <ul>
        <?php foreach ($quizzes as $quiz): ?>
            <li><?php echo htmlspecialchars($quiz['title']); ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
