<!DOCTYPE html>
<html>
<head>
    <title>Quiz List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            width: 100%;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .quiz-list {
            margin-top: 40px;
            list-style: none;
            padding: 0;
        }
        .quiz-list li {
            padding: 10px 0;
            border-bottom: 1px solid #ccc;
        }
        .play-button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }
        .play-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="text-align: center;">Quiz Disponible</h1>
        <a href="quiz.php" class="play-button" style="display: block; margin: 0 auto;">Jouer</button></a>
        <ul class="quiz-list">
            <?php
            include('Config.php');

            $stmt = $conn->prepare("SELECT * FROM quizzes");
            $stmt->execute();
            $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($quizzes as $quiz): ?>
                <li><?php echo htmlspecialchars($quiz['title']); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
