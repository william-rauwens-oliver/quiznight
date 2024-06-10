<?php
// Inclusion du fichier de configuration de la base de données
include('Config.php');

// Récupération des thèmes depuis la base de données
try {
    $stmt_themes = $conn->query("SELECT DISTINCT theme FROM quizzes");
    $themes = $stmt_themes->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    echo "Erreur PDO : " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizzes</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 800px;
            padding: 40px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        h1, h2, h3 {
            margin: 0;
            padding: 0;
            color: #007bff;
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
        }
        h2 {
            margin: 20px 0;
        }
        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        li {
            margin-bottom: 10px;
        }
        a {
            text-decoration: none;
            color: #007bff;
            transition: color 0.3s ease;
        }
        a:hover {
            color: #0056b3;
        }
        .quiz {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
        }
        .quiz h3 {
            margin-bottom: 10px;
            color: #333;
        }
        .theme-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }
        .theme-item {
            margin-bottom: 10px;
        }
        .theme-btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .theme-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        try {
            if (isset($_GET['theme']) && in_array($_GET['theme'], $themes)) {
                $theme = $_GET['theme'];

                echo "<h1>Thème sélectionné : $theme</h1>";

                $stmt_quiz = $conn->prepare("SELECT * FROM quizzes WHERE theme = :theme");
                $stmt_quiz->bindParam(':theme', $theme);
                $stmt_quiz->execute();
                $quizzes = $stmt_quiz->fetchAll(PDO::FETCH_ASSOC);

                if (!$quizzes) {
                    throw new Exception("Aucun quiz trouvé pour le thème '$theme'.");
                }

                echo "<h2>Quizzes sur " . ucfirst($theme) . "</h2>";
                foreach ($quizzes as $quiz) {
                    echo "<div class='quiz'>";
                    echo "<h3>" . htmlspecialchars($quiz['title']) . "</h3>";

                    // Récupération des questions du quiz en fonction de l'ID du quiz
                    $stmt_question = $conn->prepare("SELECT * FROM questions WHERE quiz_id = :quiz_id");
                    $stmt_question->bindParam(':quiz_id', $quiz['quiz_id']);
                    $stmt_question->execute();
                    $questions = $stmt_question->fetchAll(PDO::FETCH_ASSOC);

                    if (!$questions) {
                        throw new Exception("Aucune question trouvée pour le quiz '{$quiz['title']}'.");
                    }

                    echo "<ul>";
                    foreach ($questions as $question) {
                        echo "<li>" . htmlspecialchars($question['question_text']) . "</li>";
                    }
                    echo "</ul>";
                    echo "</div>";
                }
            } else {
                echo "<h1>Sélectionnez un thème</h1>";
                echo "<div class='theme-list'>";
                foreach ($themes as $theme_option) {
                    echo "<div class='theme-item'><a class='theme-btn' href='?theme=$theme_option'>" . ucfirst($theme_option) . "</a></div>";
                }
                echo "</div>";
            }
        } catch (PDOException $e) {
            echo "Erreur PDO : " . $e->getMessage();
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
        }
        ?>
    </div>
</body>
</html>
