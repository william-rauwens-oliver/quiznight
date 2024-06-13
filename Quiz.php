<!DOCTYPE html>
<html lang="fr">
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
            min-height: 100vh;
        }
        .main-content {
            max-width: 800px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
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
        .quiz {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background-color: #f9f9f9;
            text-align: left; /* Alignement du texte à gauche à l'intérieur du quiz */
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
            text-decoration: none; /* Supprime le soulignement par défaut des liens */
            display: inline-block; /* Pour que les liens soient affichés en ligne */
        }
        .theme-btn:hover {
            background-color: #0056b3;
        }
        .option-btn {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .option-btn:hover {
            background-color: #0056b3;
        }
        .return-link {
            text-align: center;
            margin-top: 20px;
        }
        .return-link a {
            text-decoration: none;
            color: #007bff;
            display: inline-block;
            padding: 10px 20px;
            border: 1px solid #007bff;
            border-radius: 4px;
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }
        .return-link a:hover {
            background-color: #007bff;
            color: #fff;
        }
        .return-link a .icon {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="main-content">
        <?php
        include('BDD.php');
        session_start();

        require_once 'Classes/Client/Question.php';
        require_once 'Classes/Client/Quiz.php';
        require_once 'Classes/Client/Options.php';

        $quiz = new Quiz($conn);
        $question = new Question($conn);
        $options = new Options($conn);

        try {
            $themes = $quiz->getThemes();

            if (isset($_GET['theme']) && in_array($_GET['theme'], $themes)) {
                $theme = $_GET['theme'];

                echo "<h1>Thème sélectionné : $theme</h1>";

                $quiz_data = $quiz->getQuizByTheme($theme);

                if (!$quiz_data) {
                    throw new Exception("Aucun quiz trouvé pour le thème '$theme'.");
                }

                $quiz_id = $quiz_data['quiz_id'];

                if (!isset($_SESSION['question_index'])) {
                    $_SESSION['question_index'] = 0;
                }

                if (isset($_POST['answer'])) {
                    $question_id = $_POST['question_id'];
                    $selected_option_id = $_POST['answer'];

                    $stmt_correct_option = $conn->prepare("SELECT * FROM options WHERE id = :question_id AND is_correct = 1");
                    $stmt_correct_option->bindParam(':question_id', $question_id, PDO::PARAM_INT);
                    $stmt_correct_option->execute();
                    $correct_option = $stmt_correct_option->fetch(PDO::FETCH_ASSOC);

                    if ($correct_option) {
                        $correct_option_id = $correct_option['option_id'];

                        if ($selected_option_id == $correct_option_id) {
                            echo "<p style='color: green;'>Bonne réponse !</p>";
                        } else {
                            echo "<p style='color: red;'>Mauvaise réponse. La bonne réponse était : " . htmlspecialchars($correct_option['option_text']) . ".</p>";
                        }
                    }

                    $_SESSION['question_index']++;
                }


                $current_question = $question->getQuestion($quiz_id, $_SESSION['question_index']);

                if ($current_question) {
                    $options_list = $options->getOptions($current_question['id']);

                    echo "<div class='quiz'>";
                    echo "<h3>" . htmlspecialchars($current_question['question_text']) . "</h3>";
                    echo "<form method='POST'>";
                    echo "<input type='hidden' name='question_id' value='" . htmlspecialchars($current_question['id']) . "'>";
                    foreach ($options_list as $option) {
                        echo "<button class='option-btn' type='submit' name='answer' value='" . htmlspecialchars($option['option_id']) . "'>" . htmlspecialchars($option['option_text']) . "</button>";
                    }
                    echo "</form>";
                    echo "</div>";

                    echo "<div class='return-link'>";
                    echo "<a href='quiz.php'>";
                    echo "<span class='icon'>&larr;</span> Retourner à la sélection du thème";
                    echo "</a>";
                    echo "</div>";
                } else {
                    echo "<h2>Vous avez terminé le quiz !</h2>";
                    session_destroy();
                    echo "<div class='return-link'>";
                    echo "<a href='quiz.php'>";
                    echo "<span class='icon'>&larr;</span> Retourner à la sélection du thème";
                    echo "</a>";
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
