<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: Authentification.php');
    exit();
}

include('Config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_quiz'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $theme = $_POST['theme'];

        $stmt = $conn->prepare("INSERT INTO quizzes (title, description, theme) VALUES (:title, :description, :theme)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':theme', $theme);
        $stmt->execute();

        header('Location: admin.php');
        exit();
    } elseif (isset($_POST['create_question'])) {
        $quiz_id = $_POST['quiz_id'];
        $question_text = $_POST['question_text'];

        $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text) VALUES (:quiz_id, :question_text)");
        $stmt->bindParam(':quiz_id', $quiz_id);
        $stmt->bindParam(':question_text', $question_text);
        $stmt->execute();

        header('Location: admin.php');
        exit();
    }
}

$stmt = $conn->prepare("SELECT * FROM quizzes");
$stmt->execute();
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Panneau d'administration</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Jost:wght@400;500;700&display=swap');

        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Jost', sans-serif;
            background: linear-gradient(to bottom, #0f0c29, #302b63, #24243e);
            color: #fff;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            padding: 20px;
            color: #333;
            overflow: auto;
            max-height: 90vh;
            backdrop-filter: blur(10px);
        }

        h1 {
            color: #302b63;
            text-align: center;
            margin-bottom: 20px;
            font-size: 2.5em;
        }

        form {
            background-color: rgba(247, 247, 247, 0.9);
            padding: 20px;
            margin: 20px 0;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
            transition: border 0.3s ease, box-shadow 0.3s ease;
        }

        input[type="text"]:focus,
        textarea:focus,
        select:focus {
            border: 1px solid #302b63;
            outline: none;
            box-shadow: 0 0 8px rgba(48, 43, 99, 0.3);
        }

        button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            background: #302b63;
            color: white;
            font-size: 1em;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        button:hover {
            background: #5753b8;
            transform: scale(1.05);
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            background-color: rgba(240, 240, 240, 0.9);
            margin: 10px 0;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        li:hover {
            transform: scale(1.02);
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 10px;
            }

            h1 {
                font-size: 2em;
            }

            form {
                padding: 15px;
            }

            button {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Créer un nouveau quiz</h1>
        <form method="POST">
            <input type="text" name="title" placeholder="Titre du quiz" required>
            <textarea name="description" placeholder="Description du quiz" required></textarea>
            <label for="theme">Sélectionnez le thème :</label>
            <select name="theme" id="theme" required>
                <option value="animals">Animaux</option>
                <option value="cars">Voitures</option>
                <option value="computers">Ordinateurs</option>
            </select>
            <button type="submit" name="create_quiz">Créer le quiz</button>
        </form>

        <h1>Créer une nouvelle question</h1>
        <form method="POST">
            <label for="quiz_id">Sélectionnez le quiz :</label>
            <select name="quiz_id" id="quiz_id" required>
                <?php foreach ($quizzes as $quiz): ?>
                    <option value="<?php echo $quiz['id']; ?>"><?php echo htmlspecialchars($quiz['title']); ?></option>
                <?php endforeach; ?>
            </select>
            <textarea name="question_text" placeholder="Texte de la question" required></textarea>
            <button type="submit" name="create_question">Créer la question</button>
        </form>

        <h1>Quiz existants</h1>
        <ul>
            <?php foreach ($quizzes as $quiz): ?>
                <li><?php echo htmlspecialchars($quiz['title']) . " - " . htmlspecialchars($quiz['theme']); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
